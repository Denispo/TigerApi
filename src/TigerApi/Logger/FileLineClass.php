<?php

namespace TigerApi\Logger;

class FileLineClass implements IAmFileLineClass{

  private int $line;
  private string $file;
  private string $class;
  private string $methodOrFunction;

  public function __construct() {
    $data = debug_backtrace(options: 2,limit: 2);
    $this->line = $data[0]['line'] ?? 0;
    $this->file = $data[0]['file'] ?? '';
    $this->class = $data[1]['class'] ?? '';
    $this->methodOrFunction = $data[1]['function'] ?? '';
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