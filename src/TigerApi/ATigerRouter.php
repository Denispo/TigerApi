<?php

namespace TigerApi;

use Nette\Http\IRequest;
use TigerCore\Auth\ICanGetCurrentUser;
use TigerCore\BaseRestRouter;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\ICanMatchRoutes;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_RouteMask;

abstract class ATigerRouter implements ICanMatchRoutes {

  abstract protected function onGetCurrentUser():ICanGetCurrentUser;

  private BaseRestRouter $router;

  public function __construct()
  {
    $this->router = new BaseRestRouter();
  }


  public function addRoute(string|array $method, VO_RouteMask $mask, ICanHandleMatchedRoute $handler)
  {
    $this->router->addRoute($method, $mask, $handler);
  }

  public function runMatch(IRequest $httpRequest): ICanGetPayloadRawData
  {
    return $this->router->runMatch($httpRequest);
  }

}