<?php

namespace TigerApi\Request;

use TigerCore\Response\S422_UnprocessableEntityException;

class TigerInvalidRequestParamsException extends S422_UnprocessableEntityException {

  public function __construct(public ICanGetInvalidRequestData $invalidParams) {
    parent::__construct('',$invalidParams->getInvalidRequestData());
  }

}