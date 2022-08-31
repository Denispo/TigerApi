<?php

namespace TigerApi\Error;

interface ICanHandleUncaughtException {
  public function handleUncaughtException(\Throwable $exception);

}