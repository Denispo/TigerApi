<?php

namespace TigerApi;

use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\BaseApp;
use Nette\Http\IRequest;
use TigerCore\Response\BaseResponseException;

abstract class TigerApp extends BaseApp {

  protected abstract function onGetAppSettings():TigerAppSettings;

  public function run(IRequest $httpRequest, ICanGetCurrentUser $currentUser) {
    $appSettings = $this->onGetAppSettings();
    $router = $appSettings->router;

    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setContentType('application/json','utf-8');

    try {
      $router->match($httpRequest, $currentUser);
      $json = json_encode($appSettings->payloadGetter->getPayload());
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