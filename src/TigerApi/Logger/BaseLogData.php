<?php

namespace TigerApi\Logger;

use TigerCore\ICanGetValueAsBoolean;
use TigerCore\ICanGetValueAsFloat;
use TigerCore\ICanGetValueAsInit;
use TigerCore\ICanGetValueAsString;
use TigerCore\ICanGetValueAsTimestamp;

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
     foreach ($customData as $key => $value) {
        if ($value instanceof ICanGetValueAsInit) {
           $value = $value->getValueAsInt();
        } elseif ($value instanceof ICanGetValueAsString) {
           $value = $value->getValueAsString();
        } elseif ($value instanceof ICanGetValueAsBoolean) {
           $value = $value->getValueAsBool();
        } elseif ($value instanceof ICanGetValueAsFloat) {
           $value = $value->getValueAsFloat();
        } elseif ($value instanceof ICanGetValueAsTimestamp) {
           $value = $value->getValueAsTimestamp();
        }
        $this->customData[$key] = $value;
     }
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