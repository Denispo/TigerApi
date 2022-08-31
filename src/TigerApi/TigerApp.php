<?php

namespace TigerApi;

use JetBrains\PhpStorm\NoReturn;
use Nette\Loaders\RobotLoader;
use Throwable;
use TigerApi\Error\ICanHandlePhpError;
use TigerApi\Error\ICanHandleUncaughtException;
use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use Nette\Http\IRequest;
use TigerCore\ICanMatchRoutes;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanGetPayloadData;
use TigerCore\Response\MethodNotAllowedException;

abstract class TigerApp extends BaseApp implements ICanGetCurrentUser{

  private RobotLoader $loader;
  private IRequest|null $httpRequest = null;

  protected abstract function onGetCurrentUser(IRequest $httpRequest):ICurrentUser;

  protected abstract function onGetUnexpectedExceptionHandler():ICanHandleUncaughtException;
  protected abstract function onGetErrorHandler():ICanHandlePhpError;
  protected abstract function onGetRouter():ICanMatchRoutes;
  protected abstract function onGetPayloadGetter():ICanGetPayloadData;

  #[NoReturn]
  private function doHandleUnexpectedException(Throwable $exception) {
    $handler = $this->onGetUnexpectedExceptionHandler();
    $handler->handleUncaughtException($exception);
    exit;
  }

  public function getCurrentUser():ICurrentUser {
    try {
      if (!$this->httpRequest) {
        throw new TigerAppIsNotRunningException();
      }
    } catch (TigerAppIsNotRunningException $e ) {
      $this->doHandleUnexpectedException($e);
    }
    return $this->onGetCurrentUser($this->httpRequest);
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
   * @throws Throwable
   * @return void
   */
  public function __construct(string $defaultTimeZone = 'Europe/Prague') {


    date_default_timezone_set($defaultTimeZone);

    set_exception_handler([$this,'_exception_handler']);
    set_error_handler([$this,'_error_handler']);

  }

  public function run(IRequest $httpRequest) {
    $this->httpRequest = $httpRequest;

    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('application/json','utf-8');

    try {
      $this->onGetRouter()->match($httpRequest, $this);
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