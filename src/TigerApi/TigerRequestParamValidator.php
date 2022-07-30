<?php

namespace TigerApi;

use TigerCore\Requests\BaseRequestParam;

class TigerRequestParamValidator implements ICanSetRequestParamIsInvalid, ICanGetInvalidRequestParams
{

  /**
   * @var TigerInvalidRequestParam[]
   */
  private array $erros = [];

  public function setRequestParamIsInvalid(BaseRequestParam $param, string $errorDescription = '') {
    $this->erros[] = new TigerInvalidRequestParam($param,  $errorDescription);
  }

  public function getInvalidRequestParams(): array {
    return $this->erros;
  }
}