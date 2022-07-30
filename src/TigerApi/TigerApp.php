<?php

namespace TigerApi;

use TigerCore\Auth\ICurrentUser;
use TigerCore\BaseApp;
use TigerCore\ICanMatchRoutes;
use Nette\Http\IRequest;

abstract class TigerApp extends BaseApp {


  protected abstract function onGetRouter():ICanMatchRoutes;

  public function run(IRequest $httpRequest, ICurrentUser $currentUser) {
    $router = $this->onGetRouter();
    if ($router) {
      $router->match($httpRequest, $currentUser);
    }
  }

}