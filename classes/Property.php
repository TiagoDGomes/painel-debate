<?php

$user_prop_names_write = array();

$props_type_number = array('timer-prepared', 'timer-end', 'timer-start');

class Property {
    public static function get($prop_name) {
        global $props_type_number;
        $query = "SELECT prop_value FROM props WHERE prop_name = ?";
        $params = array($prop_name);
        $value = Database::fetchOne($query, $params);
        if (in_array($prop_name, $props_type_number)) {
            return $value * 1;
        } else {
            return $value;
        }
    }
    public static function getAll() {
        global $props_type_number;
        $props = array();
        $query = "SELECT prop_name, prop_value FROM props WHERE prop_name <> 'admin-page'";
        $params = array();
        $values = Database::fetchAll($query, $params);
        foreach ($values as $key) {
            $prop_name = $key['prop_name'];
            $prop_value = $key['prop_value'];
            if (in_array($prop_name, $props_type_number)) {
                $props[$prop_name] = $prop_value * 1;
            } else {
                $props[$prop_name] = $prop_value;
            }
        }
        return $props;
    }
    public static function set($prop_name, $prop_value) {
        global $user_prop_names_write;
        if (!AccessCheck::isValidAdmin() && !in_array($prop_name, $user_prop_names_write)) {
            HTTPResponse::forbidden('');
        }
        $query = "INSERT OR REPLACE INTO props (prop_name, prop_value) VALUES (?, ?)";
        $params = array($prop_name, $prop_value);
        return Database::execute($query, $params);
    }
}
