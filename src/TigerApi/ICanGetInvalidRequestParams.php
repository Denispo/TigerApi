<?php

namespace TigerApi;

interface ICanGetInvalidRequestParams {

  /**
   * @return TigerInvalidRequestParam[]
   */
  public function getInvalidRequestParams():array;

}