<?php

$APP_VERSION = "2.3";
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
if (!isset($MAX_DATABASES)){
    $ID_LENGTH = 12;
}
if (!isset($MAX_DATABASES)){
    $SYNC_PING_COUNT = 4;
}
require_once 'classes.php';







