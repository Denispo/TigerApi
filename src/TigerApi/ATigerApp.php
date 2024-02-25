<?php

namespace TigerApi;

use JetBrains\PhpStorm\NoReturn;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\RequestFactory;
use Throwable;
use TigerApi\Error\ICanHandlePhpError;
use TigerApi\Error\ICanHandleUncaughtException;
use TigerApi\Logger\_LogBridge;
use TigerApi\Logger\Log;
use TigerApi\Logger\LogDataError;
use TigerApi\Logger\LogDataException;
use TigerApi\Logger\LogDataNotice;
use TigerApi\Logger\LogDataWarning;
use TigerApi\Request\TigerInvalidRequestParamsException;
use TigerCore\Constants\Environment;
use TigerCore\ICanMatchRoutes;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\Base_4xx_RequestException;
use TigerCore\Response\Base_5xx_RequestException;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\Response\S405_MethodNotAllowedException;
use TigerCore\ValueObject\VO_HttpRequestMethod;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerApp implements IAmTigerApp {

  private VO_TokenPlainStr|null $authTokenPlainStr = null;
  private Environment|null $environment = null;
  private IRequest $request;

  /*
   $_logBridge slouzi pro to, aby potomek TigerApp mohl pouzivat onLogNotice atd.
   Kdyby byla v TigerApp metoda public function logError(LogDataError $logData), kterou bychom predavali do Log::_init()
   public function logError(LogDataError $logData) {
     $this->onLogError($logData);
   }
   Potom by potomek mohl tuto metodu volat v obsluze udalosti (protoze ji vidi, protoze je public) onLogError.
  napr. v potomkovi
  class App extend TigerApp
  protected function onLogError(LogDataError $baseLogData):void;{
    $this->logError($baseLogData);
  }

  Coz by v TygerApp zavolalo zase onLogError a program by se zacyklil

  Cil je, aby potomek nevidel nic, co nemuze volat.

  */
  /**
   * @var _LogBridge
   */
  private _LogBridge $_logBridge;

  protected abstract function onGetUnexpectedExceptionHandler():ICanHandleUncaughtException;
  protected abstract function onGetErrorHandler():ICanHandlePhpError;

  /**
   * @return ICanMatchRoutes
   */
  protected abstract function onGetRouter():ICanMatchRoutes;
  protected abstract function onGetEnvironment(): Environment;


  /**
   * If payload is returned, router->runMatch() will be skipped and this payload will be returned instead
   * @param VO_HttpRequestMethod $method
   * @param string $path
   * @return ICanGetPayloadRawData|null
   */
  protected abstract function onGetPayloadBeforeRouterMatch(VO_HttpRequestMethod $method, string $path):null|ICanGetPayloadRawData;

  /**
   * Use some kind of IAmLogger or ICanLogNotice to log this Notice
   * You can not call Log::Notice inside this method!
   * @param LogDataNotice $baseLogData
   * @return void
   */
  protected abstract function onLogNotice(LogDataNotice $baseLogData):void;

  /**
   * Use some kind of IAmLogger or ICanLogError to log this Error
   * You can not call Log::Error inside this method!
   * @param LogDataError $baseLogData
   * @return void
   */
  protected abstract function onLogError(LogDataError $baseLogData):void;

  /**
   * Use some kind of IAmLogger or ICanLogWarning to log this Warning
   * You can not call Log::Warning inside this method!
   * @param LogDataWarning $baseLogData
   * @return void
   */
  protected abstract function onLogWarning(LogDataWarning $baseLogData):void;

  /**
   * Use some kind of IAmLogger or ICanLogException to log this Exception
   * You can not call Log::Exception inside this method!
   * @param LogDataException $logData
   * @return void
   */
  protected abstract function onLogException(LogDataException $logData):void;


  #[NoReturn]
  private function doHandleUnexpectedException(Throwable $exception):void {
    $handler = $this->onGetUnexpectedExceptionHandler();
    $handler->handleUncaughtException($exception);
    exit;
  }

  private function getEnvironment():Environment
  {
    if ($this->environment == null) {
      $this->environment = $this->onGetEnvironment();
    }
    return $this->environment;
  }

  public function getHttpRequest(): IRequest
  {
    return $this->request;
  }

  #[NoReturn]
  public function _exception_handler(Throwable $exception):void {
    $this->doHandleUnexpectedException($exception);
  }

  public function _error_handler(int $errNo, string $errMsg, string $file, int $line):void {
    $handler = $this->onGetErrorHandler();
    $handler->handlePhpError($errNo, $errMsg, $file, $line);
  }

  /**
   * @param IRequest|null $request If null, request will be created from globals
   * @param string $defaultTimeZone
   * @throws \ReflectionException
   */
  public function __construct(IRequest|null $request = null, string $defaultTimeZone = 'Europe/Prague') {
    if ($request === null) {
      $request = (new RequestFactory())->fromGlobals();
    }
    $this->request = $request;
    set_exception_handler([$this,'_exception_handler']);
    set_error_handler([$this,'_error_handler']);

    date_default_timezone_set($defaultTimeZone);

    $this->_logBridge = new _LogBridge(
      function (LogDataError $baseLogData){$this->onLogError($baseLogData);},
      function (LogDataWarning $baseLogData){$this->onLogWarning($baseLogData);},
      function (LogDataNotice $baseLogData){$this->onLogNotice($baseLogData);},
      function (LogDataException $baseLogData){$this->onLogException($baseLogData);}
    );

    // Abychom mohli zavolat Log::_init, musime metode _init zmenit na chvili private na public
    //    (vyse zminene jiz neplati od php 8.1)
    $class = new \ReflectionClass(Log::class);
    $method = $class->getMethod('_init');
    //$method->setAccessible(true); // from 8.1 everything is accessible
    $method->invokeArgs(null, [$this->_logBridge]);
    //$method->setAccessible(false); // from 8.1 everything is accessible
  }

  #[NoReturn]
  private function doResponse4xxException(Base_4xx_RequestException $exception)
  {
    $json = json_encode([
      'exception '.get_class($exception) => [
        $exception->getMessage(),
        'CDATA: '=> $exception->getCustomdata(),
        'FILE: ' =>$exception->getFile()
      ]
    ]);
    echo($json);
    exit;
  }

  #[NoReturn]
  private function doResponse5xxException(Base_5xx_RequestException $exception)
  {
    $sentryId = $exception->getSentryEventId();
    if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
      $json = json_encode([
        'exception '.get_class($exception) => [
          $exception->getMessage(),
          'SentryId: ' => $sentryId,
          'CDATA: '=> $exception->getCustomdata(),
          'FILE: ' =>$exception->getFile(),
          'TRACE: '=> $exception->getTrace()
        ]
      ]);
    } else {
      if ($sentryId) {
        $json = '{"errorNo": "'.$exception->getSentryEventId().'"}';
      } else {
        $json = '{"errorNo": "NA"}';
      }
    }
    echo($json);
    exit;
  }

  public function run():void {
    $request = $this->getHttpRequest();
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin',$this->request->getHeader('origin'));
    $httpResponse->setContentType('application/json','utf-8');
    $httpResponse->setHeader('Access-Control-Allow-Credentials','true');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*, authorization, content-type');



    try {
      $router = $this->onGetRouter();

      $requestMethod = new VO_HttpRequestMethod($request->getMethod());
      $requestPath = $request->getUrl()->getPath();

      if ($requestMethod->isOPTIONS()) {
        // For OPTION runMatch will set header Access-Control-Allow-Methods according to route match result.
        // TODO: Refactor. $router shuld have dedicated method to only returns valid hmethods. Router should not set Reposne hedares.
        $router->runMatch($requestMethod, $requestPath);
        exit;
      }


      $payload = $this->onGetPayloadBeforeRouterMatch($requestMethod, $requestPath);

      if ($payload === null) {
        $payload = $router->runMatch($requestMethod, $requestPath);
      }

      if ($payload === null) {
        throw new S404_NotFoundException('Path not found');
      }
    } catch (TigerInvalidRequestParamsException $e){
      $httpResponse->setCode($e->getResponseCode());
      echo(json_encode($e->getCustomData()));
      exit;
    } catch (S405_MethodNotAllowedException $e){
      $httpResponse->setHeader('Access-Control-Allow-Methods', implode(', ',$e->getAllowedMethods()));
      $httpResponse->setCode($e->getResponseCode());
      exit;
    } catch (Base_4xx_RequestException $e) {
      $httpResponse->setCode($e->getResponseCode());
      $this->doResponse4xxException($e);
      exit;
    } catch (Base_5xx_RequestException $e){
      $httpResponse->setCode($e->getResponseCode());
      $this->doResponse5xxException($e);
      exit;
    } catch (BaseResponseException $e){
      $httpResponse->setCode($e->getResponseCode());
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
        echo($json);
      }
      exit;
    } catch (\Throwable $e){
      $httpResponse->setCode(IResponse::S500_InternalServerError);
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        $json = json_encode(['exception '.get_class($e) => [$e->getMessage(), 'FILE: ' =>$e->getFile()]]);
        echo($json);
      }
      exit;
    }





    $json = json_encode($payload->getPayloadRawData());
    $error = json_last_error();

    if ($error) {
      $errorMsg = json_last_error_msg();
      $errorResponse = new \Nette\Http\Response();
      $errorResponse->setCode(\Nette\Http\IResponse::S500_InternalServerError);
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        echo($error.': '.$errorMsg);
      }
    } else {
      echo($json);
    }
  }

 }