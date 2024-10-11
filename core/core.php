<?php

$APP_VERSION = "2.4";
$APP_TITLE = "Painel";

define('PCORE', true);

session_start();

@include_once 'config.php';

if (!isset($DATABASE_PATH)){
    exit('$DATABASE_PATH is not definied.');
}
if (!isset($MAX_DATABASES)){
    $MAX_DATABASES = 6;
}
if (!isset($ID_LENGTH)){
    $ID_LENGTH = 12;
}
if (!isset($SYNC_PING_COUNT)){
    $SYNC_PING_COUNT = 4;
}
if (!isset($HIDE_STYLES)){
    $HIDE_STYLES = array();
}


require_once 'classes.php';







