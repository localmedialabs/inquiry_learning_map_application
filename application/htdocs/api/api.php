<?php
ini_set('display_errors',0);
//ini_set('session.cookie_secure', 1);
if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    require_once( dirname(__FILE__) .'/../../application/handler.php' );

    $apiAccessPath = $_REQUEST['api_path'];
    $_REQUEST['api_path'] = '';

    $hdObject = new appHandler();
    $hdObject->setAccessPath($apiAccessPath);

    $hdObject->execute();
}

?>