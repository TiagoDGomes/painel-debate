<?php if (!defined('PCORE')) die("Nope"); 

header('Content-Type: text/plain');

if (!is_dir($DATABASE_PATH)){
    mkdir($DATABASE_PATH);
} else {
    $filecount = count(glob($DATABASE_PATH . "/*.db"));
    if ($filecount >= $MAX_DATABASES){
        HTTPResponse::forbidden('Max databases excedded.');
    }    
}

$NEW_ID = substr(hash('sha256', rand(0, 99999) . Timer::timeServerInt() . rand(0, 999999999)), 0, $ID_LENGTH);
$NEW_ADMIN = hash('sha256', rand(0, 999999999) . Timer::timeServerInt() . rand(0, 999999999));
$AUTO_INCREMENT_KEYWORD = 'AUTOINCREMENT';
$TEXT = 'TEXT';   
$LONG_TEXT = 'TEXT';
$BYTE = 'INTEGER';
$INT = 'INTEGER';
$LONG_INT = 'INTEGER';

$queries = array(

    "CREATE TABLE IF NOT EXISTS props (
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            prop_name $TEXT,
            prop_value $TEXT DEFAULT ''
        );",

    "CREATE TABLE IF NOT EXISTS roullete(
        id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,
        title $TEXT
    );",

    "CREATE TABLE IF NOT EXISTS roullete_items(
        id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,
        id_roullete $INT,  
        num $INT DEFAULT 1, 
        contents $LONG_TEXT,
        FOREIGN KEY(id_roullete) REFERENCES roullete(id)
    );",

    "CREATE TABLE IF NOT EXISTS draw(
        id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
        id_roullete $INT,
        draw_code $INT DEFAULT 0,
        id_session $TEXT,
        ip_address $TEXT,
        sum $INT,
        numbers $LONG_TEXT,
        tstamp $LONG_INT
    );",

    "CREATE TABLE IF NOT EXISTS styles(
        id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
        name $TEXT DEFAULT '',
        css $TEXT DEFAULT ''
    );",

    "CREATE UNIQUE INDEX IF NOT EXISTS  
        idx_prop_name_unique
        ON props (prop_name);",
    "CREATE UNIQUE INDEX IF NOT EXISTS  
        idx_id_roullete_num_unique
        ON roullete_items (id_roullete, num);",

    "INSERT INTO props (prop_name, prop_value) VALUES ('admin-page', '$NEW_ADMIN')",
    "INSERT INTO props (prop_name, prop_value) VALUES ('timer-prepared', 60)",
    "INSERT INTO props (prop_name, prop_value) VALUES ('timer-start', NULL)",
    "INSERT INTO props (prop_name, prop_value) VALUES ('timer-end', NULL)",
    "INSERT INTO props (prop_name, prop_value) VALUES ('message-title', '')",
    "INSERT INTO props (prop_name, prop_value) VALUES ('message-content', '')",
    "INSERT INTO props (prop_name, prop_value) VALUES ('system-message', '0')",
        
);

Database::setGlobalDatabase($NEW_ID);
Database::startInstance();
Database::beginTransaction();
Database::executeQueries($queries);
Database::commit();
HTTPResponse::redirect("?i=$NEW_ID&g=$NEW_ADMIN");
exit();