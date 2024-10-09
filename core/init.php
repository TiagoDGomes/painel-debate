<?php
require_once 'classes/AccessCheck.php';

if (AccessCheck::isTimerRequest()) {
    require_once 'core/sync.php';
}

require_once 'core/core.php';

if (AccessCheck::isRequestingInvalidDatabase()) {
    HTTPResponse::forbidden("The database doesn't exists.");
}

Database::setGlobalDatabase(@$_GET['i']);

if (AccessCheck::isRequestingNewDatabase()) {
    require_once 'core/create.php';
}

if (AccessCheck::isSetRequest()) {
    require_once 'core/set.php';
}

if (AccessCheck::isUpdateData()) {
    require_once 'core/info.php';
}

$flag_access = AccessCheck::isValidAdminPage() ? 'admin' : 'user';

if (AccessCheck::isValidAdminPage()) {
    $flag_access = 'admin';
    $full_screen_url = '?i=' . $_GET['i'];
    $target_full_screen = '';
} else {
    $flag_access = 'user';
    $full_screen_url = 'javascript:fullScreen();';
    $target_full_screen = '';
}