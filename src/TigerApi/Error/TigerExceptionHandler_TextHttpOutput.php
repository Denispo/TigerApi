<?php

namespace TigerApi\Error;


use Nette\Http\IResponse;

class TigerExceptionHandler_TextHttpOutput implements ICanHandleUncaughtException {

  public function handleUncaughtException(\Throwable $exception):void {
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('text/plain','utf-8');
    $httpResponse->setCode(IResponse::S500_InternalServerError);
    echo "Uncaught exception: " , $exception->getMessage(), "\n",get_class($exception),"\n";
    print_r($exception->getTrace());
  }
}