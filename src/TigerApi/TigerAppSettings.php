<?php

namespace TigerApi;

use TigerCore\ICanMatchRoutes;
use TigerCore\Response\ICanGetPayload;

class TigerAppSettings {

  public function __construct(
    public ICanMatchRoutes $router,
    public ICanGetPayload $payloadGetter
  ) {

  }

}