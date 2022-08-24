<?php

namespace TigerApi;

use TigerCore\ICanMatchRoutes;
use TigerCore\Response\ICanGetPayloadData;

class TigerAppSettings {

  public function __construct(
    public ICanMatchRoutes $router,
    public ICanGetPayloadData $payloadGetter
  ) {

  }

}