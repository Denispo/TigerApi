<?php

namespace TigerApi\Error;


interface ICanHandlePhpError {
  public function handlePhpError(int $errNo, string $errMsg, string $file, int $line);

}