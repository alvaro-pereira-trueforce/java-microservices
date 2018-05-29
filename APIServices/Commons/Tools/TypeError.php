<?php
namespace APIServices\Commons\Tools\TypeError;

use APIServices\Commons\Tools\BasicEnum\BasicEnum;

class TypeError extends BasicEnum
{
    const UNAUTHORIZED = 300;
    const INVALID_REQUEST = 301;
    const INVALID_TOKEN = 302;
    const NOT_FOUND = 303;
    const SERVER_FACEBOOK_ERROR = 400;
}
