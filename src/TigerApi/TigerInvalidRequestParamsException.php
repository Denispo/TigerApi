<?php

namespace TigerApi;

use TigerCore\Response\UnprocessableEntityException;

class TigerInvalidRequestParamsException extends UnprocessableEntityException {

  public function __construct(public ICanGetInvalidRequestParams $invalidParams) {
    $params = $invalidParams->getInvalidRequestParams();
    $response = new TigerApiResponse();
    foreach ($params as $oneParam) {
      $response->addPayload(new InvalidRequestParamPayload($oneParam));
    }
    parent::__construct($response);
  }

}