<?php

namespace TigerApi;

use TigerCore\Constants\BaseConst;
use TigerCore\Constants\IBaseConst;

class RequestAuthorizationStatus extends BaseConst implements IBaseConst {

  const int REQUEST_NOTALLOWED_NA = 0;
  const int REQUEST_ALLOWED = 1;
  const int REQUEST_NOTALLOWED_USER_IS_NOT_AUTHENTICATED = 2;
  const int REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS = 3;

  public function IsSetTo($value): bool {
    return parent::IsSetToValue($value);
  }
}