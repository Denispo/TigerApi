<?php

namespace TigerApi\Request;

use TigerCore\Requests\BaseRequestParam;

class TigerInvalidRequestParam {

  public function __construct(public BaseRequestParam $param, public string $description) {

  }

}