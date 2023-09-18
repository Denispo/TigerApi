<?php

namespace TigerApi\Controller;

use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerCore\Payload\BasePayload;
use TigerCore\Validator\BaseAssertableObject;

interface ICanProcessRequest
{
  public function processRequest(BaseAssertableObject $requestData): BasePayload;
}
