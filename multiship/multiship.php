<?php

define("MULTISHIP_API", true);

require_once "include/error.php";
require_once "objects/object.php";
require_once "include/open_api.php";
require_once "include/config.php";

class Multiship
{
  static function init($config = null)
  {
    if ($config == null)
    {
      require "config/config.php";
    }
    if (!isset($config) || !isset($config->client_id) || ($config->client_id == ''))
    {
      die(MULTISHIP_ERROR_CONFIG);
    }

    return new Multiship_OpenApi($config);
  }
}
