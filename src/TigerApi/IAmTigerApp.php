<?php

namespace TigerApi;


use TigerCore\Auth\ICanGetCurrentUser;

interface IAmTigerApp extends ICanGetCurrentUser, ICanGetHttpRequest{

}