<?php

namespace TigerApi;

use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use TigerCore\ICanMatchRoutes;
use TigerCore\Response\ICanAddPayload;
use Nette\Http\IRequest;

abstract class TigerApp extends BaseApp {

  public function run(IRequest $httpRequest, ICurrentUser $currentUser) {
    $router = $this->onGetRouter();
    if ($router) {
      $router->match($httpRequest, $currentUser);
    }
  }

  protected abstract function onGetRouter():ICanMatchRoutes;
}