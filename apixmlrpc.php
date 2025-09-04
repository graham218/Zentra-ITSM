<?php

/**
 * @since 9.1
 */

use Glpi\Cache\CacheManager;

define('GLPI_ROOT', __DIR__);

include_once(GLPI_ROOT . "/inc/based_config.php");

// Init loggers
$GLPI = new GLPI();
$GLPI->initLogger();
$GLPI->initErrorHandler();

//init cache
$cache_manager = new CacheManager();
$GLPI_CACHE = $cache_manager->getCoreCacheInstance();

$api = new Glpi\Api\APIXmlrpc();
$api->call();
