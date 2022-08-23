<?php

namespace TigerApi;

use TigerCore\ValueObject\VO_Password;

interface ICanHandleUncaughtException {
  public function handleUncaughtException(\Throwable $exception);

}