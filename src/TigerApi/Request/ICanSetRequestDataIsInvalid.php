<?php

namespace TigerApi\Request;

interface ICanSetRequestDataIsInvalid {
  public function setRequestParamIsInvalid(string $errorMessage);

}