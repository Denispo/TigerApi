<?php

namespace TigerApi;


use TigerApi\Auth\ICanGetCurrentUser;

interface IAmTigerApp extends ICanGetCurrentUser , ICanGetHttpRequest{

}