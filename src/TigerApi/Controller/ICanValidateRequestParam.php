<?php

namespace TigerApi\Controller;

use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerCore\Validator\BaseAssertableObject;

interface ICanValidateRequestParam
{
  public function validateParams(BaseAssertableObject $requestData, ICanSetRequestParamIsInvalid $validator): void;
}
