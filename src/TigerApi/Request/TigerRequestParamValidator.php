<?php

namespace TigerApi\Request;

use TigerCore\Request\ICanGetRequestParamName;
use TigerCore\ValueObject\VO_RequestParamErrorCode;

class TigerRequestParamValidator implements ICanSetRequestParamIsInvalid, ICanGetInvalidRequestParams
{

  /**
   * @var TigerInvalidRequestParam[]
   */
  private array $erros = [];

  public function setRequestParamIsInvalid(ICanGetRequestParamName $paramName, VO_RequestParamErrorCode|null $errorCode):void {
    $paramName = $paramName->getParamName();
    $this->erros[] = new TigerInvalidRequestParam($paramName,  $errorCode ?? new VO_RequestParamErrorCode(''));
  }

  /**
   * @return TigerInvalidRequestParam[]
   */
  public function getInvalidRequestParams(): array {
    return $this->erros;
  }
}