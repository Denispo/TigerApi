<?php

namespace TigerApi\Request;

interface ICanGetInvalidRequestParams {

  /**
   * @return TigerInvalidRequestParam[]
   */
  public function getInvalidRequestParams():array;

}