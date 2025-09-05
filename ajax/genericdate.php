<?php


$AJAX_INCLUDE = 1;
include('../inc/includes.php');

// Send UTF8 Headers
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

if (isset($_POST['value']) && (strcmp($_POST['value'], '0') == 0)) {
    $withtime = filter_var($_POST['withtime'], FILTER_VALIDATE_BOOLEAN);
    if ($withtime) {
        Html::showDateTimeField($_POST['name'], ['value' => $_POST['specificvalue']]);
    } else {
        Html::showDateField($_POST['name'], ['value' => $_POST['specificvalue']]);
    }
} else {
    echo "<input type='hidden' name='" . Html::cleanInputText($_POST['name']) . "' value='" . Html::cleanInputText($_POST['value']) . "'>";
}
