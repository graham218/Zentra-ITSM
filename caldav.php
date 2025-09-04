<?php

/**
 * @since 9.5.0
 */

define('GLPI_ROOT', __DIR__);
ini_set('session.use_cookies', 0);

include_once(GLPI_ROOT . '/inc/includes.php');

global $CFG_GLPI;

$server = new Glpi\CalDAV\Server();
$server->setBaseUri($CFG_GLPI['root_doc'] . '/caldav.php');
$server->start();
