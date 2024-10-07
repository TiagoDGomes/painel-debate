<?php

class AccessCheck {
    public static function isSystemMessageActive(){
        return FALSE;
    }

    public static function isValidPage() {
        return Database::isValidDatabase();
    }
    public static function isValidAdmin() {
        if (isset($_SESSION['admin_valid'])) {
            return TRUE;
        }
        return AccessCheck::isValidAdminPage();
    }
    public static function isValidAdminPage() {
        if (isset($_GET['g'])) {
            $valid = Property::get('admin-page') == $_GET['g'];
            if ($valid) {
                $_SESSION['admin_valid'] = TRUE;
                return TRUE;
            }
        }
        return FALSE;
    }
    public static function isSetRequest() {
        return isset($_GET['set']);
    }
    public static function isUpdateData() {
        return isset($_GET['up']);
    }

    public static function isTimerRequest() {
        return isset($_GET['timer']);
    }
    public static function isRequestingInvalidDatabase() {
        global $DATABASE_PATH, $ID_LENGTH;
        if (isset($_GET['i'])) {
            if (!file_exists($DATABASE_PATH . "/" . substr($_GET['i'], 0, $ID_LENGTH) . ".db")) {
                return TRUE;
            }
        }
        return FALSE;
    }
    public static function isRequestingNewDatabase() {
        return !Database::isValidDatabase();
    }
}
