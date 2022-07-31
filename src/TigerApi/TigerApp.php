<?php

namespace TigerApi;

use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use Nette\Http\IRequest;
use TigerCore\Response\BaseResponseException;

abstract class TigerApp extends BaseApp {

  protected abstract function onGetAppSettings():TigerAppSettings;

  public function run(IRequest $httpRequest, ICurrentUser $currentUser) {
    $appSettings = $this->onGetAppSettings();
    $router = $appSettings->router;
    try {
      $router->match($httpRequest, $currentUser);
    } catch (BaseResponseException $e) {
      $errorResponse = new \Nette\Http\Response();
      $errorResponse->setCode($e->getCode());
      echo($e->getMessage());
      exit;
    }

    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setContentType('application/json','utf-8');

    $json = json_encode($appSettings->payloadGetter->getPayload());

    $error = json_last_error();

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