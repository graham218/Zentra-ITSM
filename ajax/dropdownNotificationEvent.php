<?php


if (strpos($_SERVER['PHP_SELF'], "dropdownNotificationEvent.php")) {
    include('../inc/includes.php');
    header("Content-Type: text/html; charset=UTF-8");
    Html::header_nocache();
}

Session::checkRight("notification", UPDATE);

NotificationEvent::dropdownEvents($_POST['itemtype']);
