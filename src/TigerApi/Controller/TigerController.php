<?php

namespace TigerApi\Controller;

use Nette\Http\IRequest;
use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerApi\Request\RequestAuthorizationStatus;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\S500_InternalServerErrorException;
use TigerCore\Validator\BaseAssertableObject;

class Controller extends ATigerController {

  public function __construct(
    private BaseAssertableObject        $requestData,
    private ICanValidateRequestParam          $validator,
    private ICanProcessRequest          $processor,
  ) {

  }

  /**
   * @param IRequest $httpRequest
   * @return ICanGetPayloadRawData
   * @throws S500_InternalServerErrorException
   */
  protected function onProcessRequest(IRequest $httpRequest): ICanGetPayloadRawData {
    return $this->processor->processRequest($this->requestData);
  }

  protected function onGetAuthorizationStatus(): RequestAuthorizationStatus
  {
    //return $this->authorizator->getAuthorizationStatus($this->currentUser->getCurrentUser());
    return  new RequestAuthorizationStatus(RequestAuthorizationStatus::REQUEST_ALLOWED);
  }

  protected function onValidateParams(ICanSetRequestParamIsInvalid $validator): void
  {
    $this->validator->validateParams($this->requestData, $validator);
  }

  protected function onGetObjectToMapRequestDataOn(): BaseAssertableObject|null
  {
    return $this->requestData;
  }
}