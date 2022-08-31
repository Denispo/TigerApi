<?php

namespace TigerApi\Logger;

class FileLineClass implements IAmFileLineClass{

  public function __construct() {
    print_r(debug_backtrace(options: 2,limit: 2));
    exit;

  }

  public function getLine(): int {
    return '';
  }

  public function getFile(): string {
    return '';
  }

  public function getClass(): string {
    return '';
  }

  public function getMethodOrFunction(): string {
    return '';
  }
}