<?php

namespace TigerApi\Logger;

Interface IAmFileLineClass {

  public function getLine():int;
  public function getFile():string;
  public function getClass():string;
  public function getMethodOrFunction():string;


}