<?php

namespace TigerApi\Logger;

interface ICanLogError {

  public function logError(LogDataError $logData);


}