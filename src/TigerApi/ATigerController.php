<?php

namespace TigerApi;

use TigerApi\Request\ICanSetRequestDataIsInvalid;
use TigerApi\Request\RequestAuthorizationStatus;
use TigerApi\Request\TigerInvalidRequestParamsException;
use TigerApi\Request\TigerRequestDataValidator;
use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\TypeNotDefinedException;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S401_UnauthorizedException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\Validator\BaseAssertableObject;
use TigerCore\Validator\DataMapper;

abstract class ATigerController implements ICanHandleMatchedRoute {

  /**
   * @var string[]
   */
  private array $invalidParams = [];

  abstract protected function onGetTigrApp():IAmTigerApp;

  abstract protected function onGetAuthorizationStatus():RequestAuthorizationStatus;

  /**
   * @param ICanSetRequestDataIsInvalid $validator
   * @return void
   * @throws @BaseResponseException
   */
  abstract protected function onValidateParams(ICanSetRequestDataIsInvalid $validator):void;

  /**
   * @return ICanGetPayloadRawData
   * @throws BaseResponseException
   */
  abstract protected function onProcessRequest():ICanGetPayloadRawData;

  abstract protected function onGetObjectToMapRequestDataOn():BaseAssertableObject|null;

  /**
   * @param array $params
   * @param mixed $customData
   * @return ICanGetPayloadRawData
   * @throws BaseResponseException
   * @throws InvalidArgumentException
   * @throws S401_UnauthorizedException
   * @throws S404_NotFoundException
   * @throws TigerInvalidRequestParamsException
   * @throws TypeNotDefinedException
   */
  public function handleMatchedRoute(array $params, mixed $customData):ICanGetPayloadRawData {
    $obj = $this->onGetObjectToMapRequestDataOn();
    if ($obj) {
      $requestData = [];
      //inspirace: https://www.slimframework.com/docs/v4/objects/request.html#the-request-body
      $contentType = $this->onGetTigrApp()->getHttpRequest()->getHeader('Content-Type')?? '';
      if (str_contains($contentType, 'application/json')) {
        $requestData = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
          throw new InvalidArgumentException('Request JSON is not properly formatted');
        }
      } elseif (str_contains($contentType, 'multipart/form-data')) {
        $requestData = $this->onGetTigrApp()->getHttpRequest()->getPost();
        if (!is_array($requestData)) {
          $requestData = [];
        }
      }
      // Chceme, at se $params zmerguje do $requestData. Klice v $params maji vyssi prioritu a prepisou pripadne stejne klice v $requestData;
      $params = array_merge($requestData, $params);
      $mapper = new DataMapper($params);
      $mapper->mapTo($obj);
    }
    $authorizationStatus = $this->onGetAuthorizationStatus();
    if (!$authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_ALLOWED)) {
      if ($authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_IS_NOT_AUTHENTICATED)) {
        throw new S401_UnauthorizedException();
      } elseif ($authorizationStatus->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
        throw new S401_UnauthorizedException();
      }
      throw new S404_NotFoundException();
    }

    $requestParamValidator = new TigerRequestDataValidator();
    $this->onValidateParams($requestParamValidator);
    $invalidParams = $requestParamValidator->getInvalidRequestData();
    if (count($invalidParams) > 0) {
      throw new TigerInvalidRequestParamsException($requestParamValidator);
    }

    return $this->onProcessRequest();
  }


}