<?php


/**
 * @var array $CFG_GLPI
 * @var \Psr\SimpleCache\CacheInterface $GLPI_CACHE
 */
global $CFG_GLPI, $GLPI_CACHE;

$AJAX_INCLUDE = 1;

include("../inc/includes.php");

header("Content-Type: application/json; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

echo json_encode(Entity::getEntitySelectorTree());
return;
