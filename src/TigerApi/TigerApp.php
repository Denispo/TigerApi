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
use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use TigerCore\Constants\Environment;
use TigerCore\ICanMatchRoutes;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanGetPayloadData;
use TigerCore\Response\MethodNotAllowedException;
use TigerCore\ValueObject\VO_TokenPlainStr;

abstract class TigerApp extends BaseApp implements ICanGetCurrentUser{

  private VO_TokenPlainStr|null $authTokenPlainStr = null;

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

  protected abstract function onGetCurrentUser(VO_TokenPlainStr $tokenPlainStr):ICurrentUser;

  protected abstract function onGetUnexpectedExceptionHandler():ICanHandleUncaughtException;
  protected abstract function onGetErrorHandler():ICanHandlePhpError;
  protected abstract function onGetRouter():ICanMatchRoutes;
  protected abstract function onGetPayloadGetter():ICanGetPayloadData;

  protected abstract function onLogNotice(LogDataNotice $baseLogData):void;
  protected abstract function onLogError(LogDataError $baseLogData):void;
  protected abstract function onLogWarning(LogDataWarning $baseLogData):void;
  protected abstract function onLogException(LogDataException $logData):void;


  #[NoReturn]
  private function doHandleUnexpectedException(Throwable $exception) {
    $handler = $this->onGetUnexpectedExceptionHandler();
    $handler->handleUncaughtException($exception);
    exit;
  }

  protected function getEnvironment(): Environment {
    return $this->environment;
  }

  private function getAuthTokenPlainStr():VO_TokenPlainStr {
    if (!$this->authTokenPlainStr) {
      $this->authTokenPlainStr = VO_TokenPlainStr::createFromBearerRequest($this->getHttpRequest());
    }
    return $this->authTokenPlainStr;
  }

  public function getCurrentUser():ICurrentUser {
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
   * @param Environment $environment
   * @param string $defaultTimeZone
   * @throws \ReflectionException
   */
  public function __construct(private Environment $environment, string $defaultTimeZone = 'Europe/Prague') {
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

  public function run() {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('application/json','utf-8');


    try {
      $this->onGetRouter()->match($this->getHttpRequest(), $this);
      $json = json_encode($this->onGetPayloadGetter()->getPayloadData());
      $error = json_last_error();
    } catch (MethodNotAllowedException $e){
      $httpResponse->setHeader('Access-Control-Allow-Methods', implode(', ',$e->getAllowedMethods()));
      $httpResponse->setCode($e->getCode());
      exit;
    } catch (BaseResponseException $e) {
      $httpResponse->setCode($e->getCode());
      $json = json_encode([$e->getPayloadKey()->getValue() => $e->getPayloadData()]);
      $error = json_last_error();
    }

    if ($error) {
      $errorMsg = json_last_error_msg();
      $errorResponse = new \Nette\Http\Response();
      $errorResponse->setCode(\Nette\Http\IResponse::S500_INTERNAL_SERVER_ERROR);
      echo($error.': '.$errorMsg);
    } else {
      echo($json);
    }
  }

}