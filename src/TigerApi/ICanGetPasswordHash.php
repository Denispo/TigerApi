<?php

namespace TigerApi;

use TigerCore\ValueObject\VO_Password;

interface ICanGetPasswordHash {
  public function getHashForPassword(VO_Password $plainPassword);

}