<?php

namespace TigerApi;

use TigerCore\Response\BaseResponseException;

class TigerInvalidRequestParamsException extends BaseResponseException {

  public function __construct(public ICanGetInvalidRequestParams $invalidParams) {
    parent::__construct();

  }

}