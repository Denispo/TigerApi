<?php

namespace TigerApi;

use TigerCore\BaseRestRouter;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\ICanMatchRoutes;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_HttpRequestMethod;
use TigerCore\ValueObject\VO_RouteMask;

abstract class ATigerRouter implements ICanMatchRoutes {

  private BaseRestRouter $router;

  abstract protected function onBeforeRunMatch(VO_HttpRequestMethod $requestMethod, string $requestUrlPath);

  public function __construct()
  {
    $this->router = new BaseRestRouter();
  }

  /**
   * @param 'PUT'|'GET'|'POST'|'DELETE'|'PATCH'|('PUT'|'GET'|'POST'|'DELETE'|'PATCH')[] $method
   * @param VO_RouteMask $mask
   * @param ICanHandleMatchedRoute $handler
   * @return void
   */
  public function addRoute(string|array $method, VO_RouteMask $mask, ICanHandleMatchedRoute $handler)
  {
    $this->router->addRoute($method, $mask, $handler);
  }

  public function runMatch(VO_HttpRequestMethod $requestMethod, string $requestUrlPath): null|ICanGetPayloadRawData
  {
    $this->onBeforeRunMatch($requestMethod, $requestUrlPath);
    return $this->router->runMatch($requestMethod, $requestUrlPath);
  }

}