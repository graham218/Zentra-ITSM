<?php

use Glpi\System\Status\StatusChecker;

include('./inc/includes.php');

// Force in normal mode
$_SESSION['glpi_use_mode'] = Session::NORMAL_MODE;

// Need to be used using :
// check_http -H servername -u /glpi/status.php -s GLPI_OK

$valid_response_types = ['text/plain', 'application/json'];
$fallback_response_type = 'text/plain';

if (!isset($_SERVER['HTTP_ACCEPT']) || !in_array($_SERVER['HTTP_ACCEPT'], $valid_response_types, true)) {
    $_SERVER['HTTP_ACCEPT'] = $fallback_response_type;
}

$format = $_SERVER['HTTP_ACCEPT'];
if (isset($_REQUEST['format'])) {
    switch ($_REQUEST['format']) {
        case 'json':
            $format = 'application/json';
            break;
        case 'plain':
            $format = 'text/plain';
            break;
    }
}

if ($format === 'text/plain') {
    Toolbox::deprecated('Plain-text status output is deprecated please use the JSON format instead by specifically setting the Accept header to "application/json". In the future, JSON output will be the default.');
}
header('Content-type: ' . $format);

if ($format === 'application/json') {
    echo json_encode(StatusChecker::getServiceStatus($_REQUEST['service'] ?? null, true, true));
} else {
    echo StatusChecker::getServiceStatus($_REQUEST['service'] ?? null, true, false);
}
