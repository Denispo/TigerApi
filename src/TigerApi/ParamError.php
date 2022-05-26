<?php

namespace TigerApi;

class ParamError {

  public function __construct(private string $paramName, private string $description) {

  }

  public function getParamName():string {
    return $this->paramName;
  }

  public function getErrorDescription():string {
    return $this->description;

  }
}