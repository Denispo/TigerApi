<?php

namespace TigerApi\Request;

interface ICanGetInvalidRequestData {

  /**
   * @return string[]
   */
  public function getInvalidRequestData():array;

}