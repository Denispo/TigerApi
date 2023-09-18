<?php

namespace TigerApi;


use Nette\Http\IRequest;

interface ICanGetHttpRequest {
  public function getHttpRequest():IRequest;
}