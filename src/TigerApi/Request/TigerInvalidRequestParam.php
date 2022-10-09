<?php

namespace TigerApi\Request;

use TigerCore\ValueObject\VO_RequestParamErrorCode;
use TigerCore\ValueObject\VO_RequestParamName;

class TigerInvalidRequestParam {

  public function __construct(public VO_RequestParamName $paramName, public VO_RequestParamErrorCode $errorCode) {

  }

}