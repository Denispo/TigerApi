<?php

namespace TigerApi\Logger;

class BaseLogData {

  public function __construct(private string $message, private array $data, private ?FileLineClass $fileLineClass = null) {

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

  /**
   * @return FileLineClass|null
   */
  public function getFileLineClass(): ?FileLineClass {
    return $this->fileLineClass;
  }

}