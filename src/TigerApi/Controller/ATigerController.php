<?php

namespace TigerApi\Controller;

use Nette\Http\IRequest;
use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerApi\Request\RequestAuthorizationStatus;
use TigerApi\Request\TigerInvalidRequestParamsException;
use TigerApi\Request\TigerRequestParamValidator;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\TypeNotDefinedException;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Request\Validator\InvalidRequestParam;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S401_UnauthorizedException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\Validator\BaseAssertableObject;
use TigerCore\Validator\DataMapper;

abstract class ATigerController implements ICanHandleMatchedRoute {

  /**
   * @var InvalidRequestParam[]
   */
  private array $invalidParams = [];

  abstract protected function onGetAuthorizationStatus():RequestAuthorizationStatus;

  /**
   * @param ICanSetRequestParamIsInvalid $validator
   * @return void
   * @throws @BaseResponseException
   */
  abstract protected function onValidateParams(ICanSetRequestParamIsInvalid $validator):void;

  /**
   * @param IRequest $httpRequest
   * @return ICanGetPayloadRawData
   * @throws BaseResponseException
   */
  abstract protected function onProcessRequest(IRequest $httpRequest):ICanGetPayloadRawData;

  abstract protected function onGetObjectToMapRequestDataOn():BaseAssertableObject|null;

  /**
   * @throws S404_NotFoundException
   * @throws S401_UnauthorizedException
   * @throws TypeNotDefinedException
   * @throws BaseResponseException
   * @throws InvalidArgumentException
   * @throws TigerInvalidRequestParamsException
   */
  public function handleMatchedRoute(array $params, IRequest $request):ICanGetPayloadRawData {
    $obj = $this->onGetObjectToMapRequestDataOn();
    if ($obj) {
      //inspirace: https://www.slimframework.com/docs/v4/objects/request.html#the-request-body
      $contentType = $request->getHeader('Content-Type')?? '';
      if (str_contains($contentType, 'application/json')) {
        $requestData = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
          // Chceme, at se $params zmerguje do $requestData. Klice v $params maji vyssi prioritu a prepisou pripadne stejne klice v $requestData;
          $params = array_merge($requestData, $params);
        } else {
          throw new InvalidArgumentException('Request JSON is not properly formatted');
        }

      }
      $mapper = new DataMapper($params);
      $mapper->mapTo($obj);
    }
    $authorizationStatus = $this->onGetAuthorizationStatus();
    if (!$authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_ALLOWED)) {
      if ($authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED)) {
        throw new S401_UnauthorizedException();
      } elseif ($authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
        throw new S401_UnauthorizedException();
      }
      throw new S404_NotFoundException();
    }

    $requestParamValidator = new TigerRequestParamValidator();
    foreach ($this->invalidParams as $oneInvalidParam) {
      $requestParamValidator->setRequestParamIsInvalid($oneInvalidParam->getParamName(), $oneInvalidParam->getErrorCode()->getErrorCodeValue());
    }
    $this->onValidateParams($requestParamValidator);
    $invalidParams = $requestParamValidator->getInvalidRequestParams();
    if ($invalidParams) {
      throw new TigerInvalidRequestParamsException($requestParamValidator);
    }

    return $this->onProcessRequest($request);
  }


}