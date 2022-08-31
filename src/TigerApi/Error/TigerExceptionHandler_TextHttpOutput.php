<?php

namespace TigerApi\Error;


class TigerExceptionHandler_TextHttpOutput implements ICanHandleUncaughtException {

  public function handleUncaughtException(\Throwable $exception) {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('text/plain','utf-8');
    echo "Uncaught exception: " , $exception->getMessage(), "\n",get_class($exception),"\n";
    print_r($exception->getTrace());
  }
}