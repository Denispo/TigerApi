<?php

namespace TigerApi\Logger;

interface ICanLogException {

  public function logException(\Throwable $exception);


}