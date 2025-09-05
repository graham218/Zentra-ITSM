<?php

if (strpos($_SERVER['PHP_SELF'], "dropdownNotificationTemplate.php")) {
    include('../inc/includes.php');
    header("Content-Type: text/html; charset=UTF-8");
    Html::header_nocache();
}

Session::checkRight("notification", UPDATE);

NotificationTemplate::dropdownTemplates('notificationtemplates_id', $_POST['itemtype']);
