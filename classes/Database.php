<?php

class Database {
    public static PDO $db_file;
    public static string $key;

    public static function getDatabaseFilename() {
        global $DATABASE_PATH;
        return "$DATABASE_PATH/" . Database::$key . ".db";
    }

    public static function isValidDatabase() {
        $valid = file_exists(Database::getDatabaseFilename());
        if ($valid) {
            Database::startInstance();
        }
        return $valid;
    }

    public static function setGlobalDatabase($key) {
        global $DATABASE_PATH;
        $filter_key = Filter::onlyAlphaNumeric($key);
        $database_file = "$DATABASE_PATH/$filter_key.db";
        Database::$key = $filter_key;
    }

    public static function startInstance() {
        Database::$db_file = new PDO("sqlite:" . Database::getDatabaseFilename(), '', '');
    }

    public static function beginTransaction() {
        Database::$db_file->beginTransaction();
    }

    public static function commit() {
        Database::$db_file->commit();
    }

    public static function fetch($query, $param, $mode = PDO::FETCH_ASSOC) {
        $stmt = Database::$db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetch($mode);
    }

    public static function fetchOne($query, $param) {
        $stmt = Database::$db_file->prepare($query);
        if (is_array($param)) {
            $stmt->execute($param);
        } else {
            $stmt->execute(array($param));
        }
        $f = $stmt->fetch();
        if ($f) {
            return $f[0];
        }
        return NULL;
    }

    public static function fetchAll($query, $param, $mode = PDO::FETCH_ASSOC) {
        $stmt = Database::$db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetchAll($mode);
    }

    public static function execute($query, $param) {
        $stmt = Database::$db_file->prepare($query);
        return $stmt->execute($param);
    }

    public static function executeQueries($queries) {
        foreach ($queries as $query) {
            Database::execute($query, []);
        }
    }
}
