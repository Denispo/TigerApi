<?php

namespace TigerApi\Logger;

interface ICanLogException {

  public function logException(LogDataException $logData);


}