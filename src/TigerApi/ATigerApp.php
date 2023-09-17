<?php

namespace TigerApi;

use JetBrains\PhpStorm\NoReturn;
use Nette\Http\IRequest;
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
use TigerCore\Response\Base_4xx_RequestException;
use TigerCore\Response\Base_5xx_RequestException;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S405_MethodNotAllowedException;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerApp{

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
  protected abstract function onGetRouter():ICanMatchRoutes;
  protected abstract function onGetEnvironment(): Environment;

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

  #[NoReturn]
  public function _exception_handler(Throwable $exception):void {
    $this->doHandleUnexpectedException($exception);
  }

  public function _error_handler(int $errNo, string $errMsg, string $file, int $line):void {
    $handler = $this->onGetErrorHandler();
    $handler->handlePhpError($errNo, $errMsg, $file, $line);
  }

  /**
   * @param string $defaultTimeZone
   * @throws \ReflectionException
   */
  public function __construct(string $defaultTimeZone = 'Europe/Prague') {
    set_exception_handler([$this,'_exception_handler']);
    set_error_handler([$this,'_error_handler']);

    date_default_timezone_set($defaultTimeZone);

    $this->request = (new RequestFactory())->fromGlobals();

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
    if ($exception instanceof S405_MethodNotAllowedException) {

    }
    exit;
  }

  #[NoReturn]
  private function doResponse5xxException(Base_5xx_RequestException $exception)
  {

    exit;
  }

  public function run():void {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('application/json','utf-8');


    $payload = [];

    try {
      $payload = $this->onGetRouter()->runMatch($this->request);
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
      $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
      echo($json);
    } catch (Base_5xx_RequestException $e){
      $httpResponse->setCode($e->getResponseCode());
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
        echo($json);
        exit;
      }
    } catch (BaseResponseException $e){
      $httpResponse->setCode($e->getResponseCode());
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
        echo($json);
        exit;
      }
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