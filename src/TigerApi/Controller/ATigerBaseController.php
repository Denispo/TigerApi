<?php

namespace TigerApi\Controller;

use Nette\Http\IRequest;
use TigerApi\Request\ICanSetRequestParamIsInvalid;
use TigerApi\Request\RequestAuthorizationStatus;
use TigerApi\Request\TigerInvalidRequestParamsException;
use TigerApi\Request\TigerRequestParamValidator;
use TigerCore\Auth\IAmCurrentUser;
use TigerCore\ICanGetValueAsBoolean;
use TigerCore\ICanGetValueAsFloat;
use TigerCore\ICanGetValueAsInit;
use TigerCore\ICanGetValueAsString;
use TigerCore\ICanGetValueAsTimestamp;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\Payload\IAmPayloadContainer;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Request\BaseRequest;
use TigerCore\Request\MatchedRequestData;
use TigerCore\Request\RequestParam;
use TigerCore\Request\Validator\BaseParamErrorCode;
use TigerCore\Request\Validator\BaseRequestParamValidator;
use TigerCore\Request\Validator\ICanGuardBooleanRequestParam;
use TigerCore\Request\Validator\ICanGuardFloatRequestParam;
use TigerCore\Request\Validator\ICanGuardIntRequestParam;
use TigerCore\Request\Validator\ICanGuardStrRequestParam;
use TigerCore\Request\Validator\ICanGuardTimestampRequestParam;
use TigerCore\Request\Validator\InvalidRequestParam;
use TigerCore\Requests\BaseRequestParam;
use TigerCore\Response\ICanAddPayload;
use TigerCore\Response\S401_UnauthorizedException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\ValueObject\BaseValueObject;

abstract class ATigerBaseController implements ICanHandleMatchedRoute {

  /**
   * @var InvalidRequestParam[]
   */
  private array $invalidParams = [];

  abstract protected function onSecurityCheck(IAmCurrentUser $currentUser):RequestAuthorizationStatus;
  abstract protected function onValidateParams(ICanSetRequestParamIsInvalid $validator);
  abstract protected function onProcessRequest(ICanAddPayload $payload, IRequest $httpRequest):void;

  abstract protected function onGetObjectToMapRequestDataOn():object|null;

  protected abstract function onGetPayloadContainer():IAmPayloadContainer;

  private function validateParam(object $class, \ReflectionProperty $property):BaseParamErrorCode|null
  {
    $attributes = $property->getAttributes(BaseRequestParamValidator::class, \ReflectionAttribute::IS_INSTANCEOF);
    $requestParam = $property->getValue($class);
    foreach ($attributes as $oneAttribute) {

      /**
       * @var BaseRequestParamValidator $attrInstance
       */
      $attrInstance = $oneAttribute->newInstance();

      if (
        ($requestParam instanceof ICanGetValueAsInit && $attrInstance instanceof ICanGuardIntRequestParam) ||
        ($requestParam instanceof ICanGetValueAsString && $attrInstance instanceof ICanGuardStrRequestParam) ||
        ($requestParam instanceof ICanGetValueAsFloat && $attrInstance instanceof ICanGuardFloatRequestParam) ||
        ($requestParam instanceof ICanGetValueAsTimestamp && $attrInstance instanceof ICanGuardTimestampRequestParam) ||
        ($requestParam instanceof ICanGetValueAsBoolean && $attrInstance instanceof ICanGuardBooleanRequestParam)
      ){
        $result = $attrInstance->runGuard($requestParam);
        if ($result) {
          return $result;
        }
      }
    }
    return null;
  }

  private function mapData(object $class, array $data):void {

    $reflection = new \ReflectionClass($class);
    $props = $reflection->getProperties();

    $data = array_change_key_case($data, CASE_LOWER);

    foreach ($props as $oneProp) {
      $attributes = $oneProp->getAttributes(RequestParam::class);
      foreach ($attributes as $oneAttribute) {

        /**
         * @var RequestParam $attr
         */
        $attr = $oneAttribute->newInstance();
        $paramName = $attr->getParamName();


        $value = $data[$paramName->getValue()] ?? null;
        $type = $oneProp->getType();
        if ($type && !$type->isBuiltin()) {
          if (is_a($type->getName(), BaseValueObject::class, true)) {
            // Parametr je BaseValueObject
            $oneProp->setValue($class, new ($type->getName())($value));

          } elseif (is_a($type->getName(), BaseRequestParam::class, true))  {
            // Parametr je potomkem BaseRequestParam
            $tmpProp = new ($type->getName())($paramName, $value);
            $oneProp->setValue($class, $tmpProp);
            $result = $this->validateParam($class, $oneProp);
            if ($result) {
              $this->invalidParams[] = new InvalidRequestParam($paramName, $result);
            }

          } else {
            // Parametr je nejaka jina trida (class, trait nebo interface), ktera neni potomkem BaseValueObject ani BaseRequestParam
          }
        } else {
          // Parametr je obycejneho PHP typu (int, string, mixed atd.)
          $oneProp->setValue($class, $value);
        }



      }
    }


  }

  public function handleMatchedRoute(array $params):ICanGetPayloadRawData {
    $container = $this->onGetPayloadContainer();
    $obj = $this->onGetObjectToMapRequestDataOn();
    if (!$obj) {
      return $container;
    }
    $this->mapData($obj, $params);
    $securityCheck = $this->onSecurityCheck($requestData->getCurrentUser());
    if (!$securityCheck->IsSetTo(RequestAuthorizationStatus::REQUEST_ALLOWED)) {
      if ($securityCheck->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED)) {
        throw new S401_UnauthorizedException();
      } elseif ($securityCheck->IsSetTo(RequestAuthorizationStatus::REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS)){
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

    $this->onProcessRequest($requestData->getPayloadContainer(), $requestData->getHttpRequest());
  }

  public function runMatchedRequest(MatchedRequestData $requestData): void {
  }

}