<?php

namespace TigerApi\Request;

class TigerRequestDataValidator implements ICanSetRequestDataIsInvalid, ICanGetInvalidRequestData
{

  /**
   * @var string[]
   */
  private array $erros = [];

  public function setRequestParamIsInvalid(string $errorMessage):void {
    $this->erros[] = $errorMessage;
  }

  /**
   * @return string[]
   */
  public function getInvalidRequestData(): array {
    return $this->erros;
  }
}