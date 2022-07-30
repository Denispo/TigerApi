<?php

namespace TigerApi;

use TigerCore\Constants\BaseConst;
use TigerCore\Constants\IBaseConst;

class RequestSecurityCheck extends BaseConst implements IBaseConst {

  const REQUEST_NOTALLOWED_NA = 0;
  const REQUEST_ALLOWED = 1;
  const REQUEST_NOTALLOWED_USER_IS_UNAUTHORIZED = 2;
  const REQUEST_NOTALLOWED_USER_HAS_INSUFFICIENT_RIGHTS = 3;

  public function IsSetTo($value): bool {
    return parent::IsSetToValue($value);
  }
}