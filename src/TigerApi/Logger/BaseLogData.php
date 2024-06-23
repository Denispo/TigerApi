<?php

namespace TigerApi\Logger;

class BaseLogData implements IAmBaseLogData, IAmFileLineClass {

  public function __construct(
    private string          $message = "",
    private array           $customData = [],
    private \Throwable|null $exception = null,
    private string          $file = '',
    private int             $line = 0,
    private string          $class = '',
    private string          $methodOrFunction = ''
  ) {

  }

  /**
   * @return string
   */
  public function getMessage(): string {
    return $this->message;
  }

  /**
   * @return array
   */
  public function getCustomData(): array {
    return $this->customData;
  }


  public function getLine(): int {
   return $this->line;
  }

  public function getFile(): string {
    return $this->file;
  }

  public function getClass(): string {
    return $this->class;
  }

  public function getMethodOrFunction(): string {
    return $this->methodOrFunction;
  }

   public function getException(): \Throwable|null
   {
      return $this->exception;
   }
}