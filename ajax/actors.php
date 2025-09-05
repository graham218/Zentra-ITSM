<?php

$AJAX_INCLUDE = 1;
include('../inc/includes.php');

Session::checkLoginUser();

switch ($_REQUEST['action']) {
    case "getActors":
        header("Content-Type: application/json; charset=UTF-8");
        Html::header_nocache();
        Session::writeClose();
        echo Dropdown::getDropdownActors($_POST);
        break;
}
