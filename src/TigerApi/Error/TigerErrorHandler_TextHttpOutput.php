<?php

namespace TigerApi\Error;


use Nette\Http\IResponse;

class TigerErrorHandler_TextHttpOutput implements ICanHandlePhpError {

  public function handlePhpError(int $errNo, string $errMsg, string $file, int $line):void{
    if (error_reporting() === 0 || error_reporting() === (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_PARSE)) {
      // Error was suppressed by @ so we do not want to report it.
      // ( Nette is still using @ to suppress errors. Unfortunately :/ )
      // https://stackoverflow.com/a/74324388
      return;
    }
    $httpResponse = new \Nette\Http\Response();
    $httpResponse->setHeader('Access-Control-Allow-Origin','*');
    $httpResponse->setHeader('Access-Control-Allow-Headers','*');
    $httpResponse->setContentType('text/plain','utf-8');
    $httpResponse->setCode(IResponse::S500_InternalServerError);
    echo "Wow my custom error handler got #[$errNo] occurred in [$file] at line [$line]: [$errMsg]";
  }

}