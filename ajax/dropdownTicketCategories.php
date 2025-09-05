<?php


if (strpos($_SERVER['PHP_SELF'], "dropdownTicketCategories.php")) {
    include('../inc/includes.php');
    header("Content-Type: text/html; charset=UTF-8");
    Html::header_nocache();
} elseif (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access this file directly");
}

$opt = ['entity' => Session::getMatchingActiveEntities($_POST['entity_restrict'])];
$condition  = [];

if (Session::getCurrentInterface() == "helpdesk") {
    $condition['is_helpdeskvisible'] = 1;
}

$currentcateg = new ITILCategory();
$currentcateg->getFromDB($_POST['value']);

if ($_POST["type"]) {
    switch ($_POST['type']) {
        case Ticket::INCIDENT_TYPE:
            $condition['is_incident'] = 1;
            if ($currentcateg->getField('is_incident') == 1) {
                $opt['value'] = $_POST['value'];
            }
            break;

        case Ticket::DEMAND_TYPE:
            $condition['is_request'] = 1;
            if ($currentcateg->getField('is_request') == 1) {
                $opt['value'] = $_POST['value'];
            }
            break;
    }
}

$opt['condition'] = $condition;
$opt['width']     = '100%';
ITILCategory::dropdown($opt);
