<?php

namespace TigerApi;

use TigerCore\Requests\BaseRequestParam;

interface ICanSetRequestParamIsInvalid {
  public function setRequestParamIsInvalid(BaseRequestParam $param, string $errorDescription);

}