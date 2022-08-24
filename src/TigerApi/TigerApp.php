<?php

namespace TigerApi;

use JetBrains\PhpStorm\NoReturn;
use Nette\Loaders\RobotLoader;
use Throwable;
use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use Nette\Http\IRequest;
use TigerCore\ICanMatchRoutes;
use TigerCore\Response\BaseResponseException;

abstract class TigerApp extends BaseApp implements ICanGetCurrentUser{

  private bool $appIsInitialized = false;
  private RobotLoader $loader;
  private IRequest|null $httpRequest = null;

  protected abstract function onGetAppSettings():TigerAppSettings;

  protected abstract function onGetCurrentUser(IRequest $httpRequest):ICurrentUser;

  //protected abstract function onGetIndexPhpRootDir():string;
  //protected abstract function onGetTempDir(string $indexPhpRootDir):string;
  protected abstract function onHandleUnexpectedException(Throwable $exception);
  protected abstract function onGetRouter():ICanMatchRoutes;

  #[NoReturn]
  private function doHandleUnexpectedException(Throwable $exception) {
    $this->onHandleUnexpectedException($exception);
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

  /**
   * @param string $tempDirectory
   * @param string $appSourceDirectoryRoot
   * @param string $defaultTimeZone
   * @throws Throwable
   * @return void
   */
  public function initialize(string $tempDirectory, string $appSourceDirectoryRoot, string $defaultTimeZone = 'Europe/Prague'):void {
    if ($this->appIsInitialized) {
      return;
    }

    $this->loader = new RobotLoader();

    // directories to be indexed by RobotLoader (including subdirectories)
    $this->loader->addDirectory($appSourceDirectoryRoot);

    // use 'temp' directory for cache
    $this->loader->setTempDirectory($tempDirectory);
    $this->loader->register(); // Run the RobotLoader

    date_default_timezone_set($defaultTimeZone);


    $this->appIsInitialized = true;

  }

  public function run(IRequest $httpRequest) {
    $appSettings = $this->onGetAppSettings();

    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setContentType('application/json','utf-8');

    try {
      $this->onGetRouter()->match($httpRequest, $this);
      $json = json_encode($appSettings->payloadGetter->getPayloadData());
      $error = json_last_error();
    } catch (BaseResponseException $e) {
      $errorResponse = new \Nette\Http\Response();
      $errorResponse->setCode($e->getCode());
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