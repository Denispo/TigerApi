<?php

namespace TigerApi;

use JetBrains\PhpStorm\NoReturn;
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
use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\BaseApp;
use TigerCore\Constants\Environment;
use TigerCore\ICanMatchRoutes;
use TigerCore\Response\Base_4xx_RequestException;
use TigerCore\Response\Base_5xx_RequestException;
use TigerCore\Response\S405_MethodNotAllowedException;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class ATigerApp extends BaseApp implements ICanGetCurrentUser{

  private VO_TokenPlainStr|null $authTokenPlainStr = null;
  private Environment|null $environment = null;

  /*
   $_logBridge slouzi pro to, aby potomek TigerApp mohl pouzivat onLogNotice atd.
   Kdyby byla v TigerApp metoda public function logError(LogDataError $logData), kterou bychom predavali do Log::_init()
   public function logError(LogDataError $logData) {
     $this->onLogError($logData);
   }
   Potom by potomek mohl tuto metodu volat v obsluze udalosti (protoze ji vydi, protoze je public) onLogError.
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

  protected abstract function onGetCurrentUser(VO_TokenPlainStr $tokenPlainStr):IAmCurrentUser;

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
  private function doHandleUnexpectedException(Throwable $exception) {
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


  private function getAuthTokenPlainStr():VO_TokenPlainStr {
    if (!$this->authTokenPlainStr) {
      $this->authTokenPlainStr = VO_TokenPlainStr::createFromBearerRequest($this->getHttpRequest());
    }
    return $this->authTokenPlainStr;
  }

  public function getCurrentUser():IAmCurrentUser {
    return $this->onGetCurrentUser($this->getAuthTokenPlainStr());
  }

  #[NoReturn]
  public function _exception_handler(Throwable $exception) {
    $this->doHandleUnexpectedException($exception);
  }

  public function _error_handler(int $errNo, string $errMsg, string $file, int $line) {
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

    parent::__construct((new RequestFactory())->fromGlobals());

    $this->_logBridge = new _LogBridge(
      function (LogDataError $baseLogData){$this->onLogError($baseLogData);},
      function (LogDataWarning $baseLogData){$this->onLogWarning($baseLogData);},
      function (LogDataNotice $baseLogData){$this->onLogNotice($baseLogData);},
      function (LogDataException $baseLogData){$this->onLogException($baseLogData);}
    );

    // Abychom mohli zavolat Log::_init, musime metode _init zmenit na chvili private na public
    $class = new \ReflectionClass(Log::class);
    $method = $class->getMethod('_init');
    $method->setAccessible(true);
    $method->invokeArgs(null, [$this->_logBridge]);
    $method->setAccessible(false);
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

  public function run() {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('application/json','utf-8');


    $json = '';
    try {
      $payload = $this->onGetRouter()->match($this->getHttpRequest(), $this);
      $json = json_encode($payload->getPayloadRawData());
      $error = json_last_error();
    } catch (S405_MethodNotAllowedException $e){
      $httpResponse->setHeader('Access-Control-Allow-Methods', implode(', ',$e->getAllowedMethods()));
      $httpResponse->setCode($e->getCode());
      exit;
    } catch (Base_4xx_RequestException $e) {
      $httpResponse->setCode($e->getCode());
      $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
    } catch (Base_5xx_RequestException $e){
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        $json = json_encode(['exception '.get_class($e) => [$e->getMessage(),'CDATA: '=> $e->getCustomdata(), 'FILE: ' =>$e->getFile()]]);
      }
    }

    $error = json_last_error();

    if ($error) {
      $errorMsg = json_last_error_msg();
      $errorResponse = new \Nette\Http\Response();
      $errorResponse->setCode(\Nette\Http\IResponse::S500_INTERNAL_SERVER_ERROR);
      if ($this->getEnvironment()->IsSetTo(Environment::ENV_DEVELOPMENT)) {
        echo($error.': '.$errorMsg);
      }
    } else {
      echo($json);
    }
  }

}