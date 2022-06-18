<?php

namespace TigerApi;

use TigerCore\BaseRestRouter;
use TigerCore\ICanAddRequest;

abstract class TigerRouter extends BaseRestRouter {

  protected abstract function onGetUserLoginRequest():TigerLoginUserRequest;
  protected abstract function onGetAuthTokenRequest():TigerGetAuthTokenRequest;

  protected function onGetRoutes(ICanAddRequest $r) {
    $r->addRequest('POST', $this->onGetUserLoginRequest());
    $r->addRequest('POST', $this->onGetAuthTokenRequest());
  }


}