<?php

namespace TigerApi\Request;

interface ICanSetRequestParamIsInvalid {
  public function setRequestParamIsInvalid(string $errorMessage);

}