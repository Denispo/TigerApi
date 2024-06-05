<?php

namespace TigerApi;

use TigerCore\Exceptions\InvalidArgumentException;
use TigerCore\Exceptions\InvalidFormatException;
use TigerCore\Exceptions\TypeNotDefinedException;
use TigerCore\ICanHandleMatchedRoute;
use TigerCore\Payload\ICanGetPayloadRawData;
use TigerCore\Response\Base_4xx_RequestException;
use TigerCore\Response\Base_5xx_RequestException;
use TigerCore\Response\BaseResponseException;
use TigerCore\Response\S400_BadRequestException;
use TigerCore\Response\S401_UnauthorizedException;
use TigerCore\Response\S404_NotFoundException;
use TigerCore\Response\S422_UnprocessableEntityException;
use TigerCore\Response\S500_InternalServerErrorException;
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
   * @return ICanGetPayloadRawData
   * @throws BaseResponseException
   */
  abstract protected function onProcessRequest():ICanGetPayloadRawData;

  abstract protected function onGetObjectToMapRequestDataOn():BaseAssertableObject|null;

  /**
   * @param array $params
   * @param mixed $customData
   * @return ICanGetPayloadRawData
   * @throws Base_5xx_RequestException
   * @throws Base_4xx_RequestException
   * @throws S500_InternalServerErrorException
   */
  public function handleMatchedRoute(array $params, mixed $customData):ICanGetPayloadRawData {
    try {
      $obj = $this->onGetObjectToMapRequestDataOn();
      if ($obj) {
        $requestData = [];
        //inspirace: https://www.slimframework.com/docs/v4/objects/request.html#the-request-body
        $contentType = $this->onGetTigrApp()->getHttpRequest()->getHeader('Content-Type')?? '';
        if (str_contains($contentType, 'application/json')) {
          $requestData = json_decode(file_get_contents('php://input'), true);
          if (json_last_error() !== JSON_ERROR_NONE) {
            throw new S400_BadRequestException('Request JSON is not properly formatted');
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

        try {
          $mapper->mapTo($obj);
        } catch (InvalidArgumentException $e) {
          throw new S500_InternalServerErrorException('BaseAssertableObject contains invalid definition',['message' => $e->getMessage()],$e);
        } catch (TypeNotDefinedException $e){
          throw new S500_InternalServerErrorException('Some BaseAssertableObject\'s property is missing type definition',['message' => $e->getMessage()],$e);
        } catch (InvalidFormatException $e){
          // Client sends mallformed or invalid/unnasignable data
          throw new S422_UnprocessableEntityException('Malformed data to map data from ',['msg'=>$e->getMessage()],$e);
        }

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

      return $this->onProcessRequest();
    } catch (Base_4xx_RequestException $e) {
      // 4xx exceptions are ok, They will be handled by parent (by tigerApp mostly)
      throw $e;
    } catch (Base_5xx_RequestException $e){
      // 5xx exceptions are ok, They will be handled by parent (by tigerApp mostly)
      throw $e;
    } catch (\Throwable $e){
      // Exceptions except 4xx or 5xx are not allowed and has to be transformed to 500 exception
      throw new S500_InternalServerErrorException('Uncaught exception during calling controllers onProcessRequest()',['message' => $e->getMessage(), 'file' => $e->getFile()],$e);
    }
  }


}