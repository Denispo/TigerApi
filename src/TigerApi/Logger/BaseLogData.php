<?php

namespace TigerApi\Logger;

class BaseLogData implements IAmBaseLogData, IAmFileLineClass {

  public function __construct(
    private string $message,
    private array $data,
    private string $file = '',
    private int $line = 0,
    private string $class = '',
    private string $methodOrFunction = ''
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
  public function getData(): array {
    return $this->data;
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
}