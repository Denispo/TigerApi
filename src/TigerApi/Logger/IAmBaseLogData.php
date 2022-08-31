<?php

namespace TigerApi\Logger;

interface IAmBaseLogData {

  /**
   * @return string
   */
  public function getMessage(): string ;

  /**
   * @return array
   */
  public function getData(): array;


}