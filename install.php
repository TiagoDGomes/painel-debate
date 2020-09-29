<?php

@include_once('config.php');

function init_db()
{
    global $db_file;
    $db_file = new PDO(DATABASE_CONNECTION, DATABASE_USERNAME, DATABASE_PASSWORD);
    $db_file->exec("CREATE TABLE IF NOT EXISTS painel(
        id INTEGER PRIMARY KEY, 
        inicio INTEGER,
        fim INTEGER,
        tempo_inicio INTEGER,
        id_atual TEXT,
        marcador TEXT,
        mensagem TEXT,
        hash_code TEXT,
        regressiva INTEGER,
        codigo_rodada_atual TEXT,
        codigo_rodada_anterior TEXT
        ); ");

    $db_file->exec("CREATE TABLE IF NOT EXISTS rodada(
        id INTEGER PRIMARY KEY, 
        id_painel INTEGER, 
        codigo_rodada TEXT,
        ip_address TEXT,
        numero_aleatorio INTEGER,
        id_session TEXT,
        tstamp INTEGER)");

    $db_file->exec("CREATE TABLE IF NOT EXISTS roleta(
        id INTEGER PRIMARY KEY,
        id_painel INTEGER,            
        marcador TEXT,
        numero INTEGER, 
        conteudo TEXT)");

    try{
        $db_file->exec("ALTER TABLE painel MODIFY COLUMN id INT AUTO_INCREMENT");
        $db_file->exec("ALTER TABLE rodada MODIFY COLUMN id INT AUTO_INCREMENT");
        $db_file->exec("ALTER TABLE roleta MODIFY COLUMN id INT AUTO_INCREMENT");
    } catch (PDOException $pe){
        // erro de nao-mysql
    }


    $db_file->exec("CREATE UNIQUE INDEX IF NOT EXISTS  
        idx_roleta_conteudo_unico 
        ON roleta (id_painel, marcador, numero);");

}

init_db();

