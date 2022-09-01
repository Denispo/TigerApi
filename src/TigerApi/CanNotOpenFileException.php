<?php

namespace TigerApi;


class CanNotOpenFileException extends BaseFileSystemException {

  public function __construct(string $message) {
    parent::__construct($message);
  }


}