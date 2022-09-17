<?php

namespace TigerApi\Request;

use TigerApi\TigerApiResponse;
use TigerCore\Response\S422_UnprocessableEntityException;

class TigerInvalidRequestParamsException extends S422_UnprocessableEntityException {

  public function __construct(public ICanGetInvalidRequestParams $invalidParams) {
    $params = $invalidParams->getInvalidRequestParams();
    $response = new TigerApiResponse();
    foreach ($params as $oneParam) {
      $response->addPayload(new InvalidRequestParamPayload($oneParam));
    }
    parent::__construct('',$response->getPayloadRawData());
  }

}