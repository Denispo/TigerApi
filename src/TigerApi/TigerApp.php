<?php

namespace TigerApi;

use Nette\Http\IResponse;
use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use TigerCore\ICanMatchRoutes;
use Nette\Http\IRequest;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\ICanGetPayload;

abstract class TigerApp extends BaseApp {


  protected abstract function onGetRouter():ICanMatchRoutes;
  protected abstract function onGetPayloadGetter():ICanGetPayload;

  public function run(IRequest $httpRequest, ICurrentUser $currentUser) {
    $router = $this->onGetRouter();
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

    $json = json_encode($this->onGetPayloadGetter()->getPayload());

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