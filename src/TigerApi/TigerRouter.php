<?php

namespace TigerApi;

use Core\BaseRestRouter;
use Core\Constants\RequestMethod;
use Core\ICanAddRequest;

abstract class TigerRouter extends BaseRestRouter {

  protected abstract function onGetUserLoginRequest():TigerLoginUserRequest;
  protected abstract function onGetAuthTokenRequest():TigerGetAuthTokenRequest;

  protected function onGetRoutes(RequestMethod $requestMethod, ICanAddRequest $r) {
    if ($requestMethod->IsSetTo(RequestMethod::HTTP_POST)) {
      $r->add($this->onGetUserLoginRequest());
      $r->add($this->onGetAuthTokenRequest());
    }
  }


}