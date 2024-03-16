<?php

namespace TigerApi;

use TigerCore\BaseRestRouter;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\ValueObject\VO_HttpRequestMethod;
use TigerCore\ValueObject\VO_RouteMask;

abstract class ATigerRouter implements IAmTigerRouter {

  private BaseRestRouter $router;

  public function __construct(private readonly VO_RouteMask $pathPrefix)
  {
    $this->router = new BaseRestRouter();
  }

  /**
   * @param VO_RouteMask $mask
   * @param ICanHandleMatchedRoute|null $handler
   * @return void
   */
  public function addRoute(VO_RouteMask $mask, ICanHandleMatchedRoute|null $handler): void
  {
    // Everything is POST. See Allan Holub presentation from 2015
    $this->router->addRoute('POST', $this->pathPrefix->add($mask), $handler);
  }

  public function runMatch(VO_HttpRequestMethod $requestMethod, string $requestUrlPath): null|ICanGetPayloadRawData
  {
    return $this->router->runMatch($requestMethod, $requestUrlPath);
  }

  public function getRoutesCount(): int
  {
    return $this->router->getRoutesCount();
  }

  public function runMatchPreflight(string $requestUrlPath): array
  {
    return $this->router->runMatchPreflight($requestUrlPath);
  }
}