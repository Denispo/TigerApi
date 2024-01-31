<?php

namespace TigerApi\Error;


use TigerCore\Response\S500_InternalServerErrorException;

class TigerErrorHandler_TextHttpOutput implements ICanHandlePhpError {

  /**
   * @throws S500_InternalServerErrorException
   */
  public function handlePhpError(int $errNo, string $errMsg, string $file, int $line):void{
    if (error_reporting() === 0 || error_reporting() === (E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_PARSE)) {
      // Error was suppressed by @ so we do not want to report it.
      // ( Nette is still using @ to suppress errors. Unfortunately :/ )
      // https://stackoverflow.com/a/74324388
      return;
    }
    throw new S500_InternalServerErrorException('Unhandled app error occured',['$errNo'=>$errNo,'$file'=>$file,'$line'=>$line,'$errMsg'=>$errMsg]);
  }

}