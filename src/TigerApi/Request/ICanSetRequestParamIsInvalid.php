<?php

namespace TigerApi\Request;

use TigerCore\Requests\ICanGetRequestParamName;
use TigerCore\ValueObject\VO_RequestParamErrorCode;
use TigerCore\ValueObject\VO_RequestParamName;

interface ICanSetRequestParamIsInvalid {
  public function setRequestParamIsInvalid(ICanGetRequestParamName|VO_RequestParamName $paramName, VO_RequestParamErrorCode $errorCode);

}