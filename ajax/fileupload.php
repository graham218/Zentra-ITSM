<?php


use Glpi\Application\ErrorHandler;

include('../inc/includes.php');

Session::checkLoginUser();

// Ensure warnings will not break ajax output.
ErrorHandler::getInstance()->disableOutput();

GLPIUploadHandler::uploadFiles($_POST);
