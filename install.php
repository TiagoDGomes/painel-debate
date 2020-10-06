<?php

@include_once('config.php');

if (strpos('sqlite', DATABASE_CONNECTION) > 0) {
    $AUTO_INCREMENT_KEYWORD = 'AUTOINCREMENT';
    $TEXT = 'TEXT';   
    $LONG_TEXT = 'TEXT';
    $BYTE = 'INTEGER';
    $INT = 'INTEGER';
    $LONG_INT = 'INTEGER';
} else {
    $AUTO_INCREMENT_KEYWORD = 'AUTO_INCREMENT'; 
    $TEXT = 'VARCHAR(100)';   
    $LONG_TEXT = 'TEXT';
    $BYTE = 'BYTE';
    $INT = 'INT';
    $LONG_INT = 'BIGINT';
}

$queries = array(
    "CREATE TABLE IF NOT EXISTS painel(
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            descricao $TEXT,
            chave_usuario $TEXT,
            chave_gerencia $TEXT,
            codigo_sorteio_atual $INT DEFAULT NULL,
            codigo_sorteio_anterior $INT DEFAULT 0,
            codigo_roleta_atual $INT DEFAULT NULL,
            codigo_roleta_anterior $INT DEFAULT 0,
            mensagem_titulo $LONG_TEXT,
            mensagem_conteudo $LONG_TEXT,
            cronometro_tempo_preparado $LONG_INT DEFAULT NULL,
            cronometro_tempo_inicio $LONG_INT DEFAULT NULL,
            cronometro_tempo_fim $LONG_INT DEFAULT NULL,                        
            ultimo_numero_sorteado $INT DEFAULT NULL                      
        );",
    "CREATE TABLE IF NOT EXISTS roleta(
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,
            id_painel $INT,          
            titulo $TEXT,
            FOREIGN KEY(id_painel) REFERENCES painel(id)
        );",
    "CREATE TABLE IF NOT EXISTS itens_roleta(
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD,
            id_roleta $INT,  
            numero $INT DEFAULT 1, 
            conteudo $LONG_TEXT,
            FOREIGN KEY(id_roleta) REFERENCES roleta(id)
        );",
    "CREATE TABLE IF NOT EXISTS sorteio(
            id $INT PRIMARY KEY $AUTO_INCREMENT_KEYWORD, 
            id_painel $INT,
            id_roleta $INT,
            codigo_sorteio $INT DEFAULT 0,
            id_session $TEXT,
            ip_address $TEXT,
            somatorio $INT,
            numeros_obtidos $LONG_TEXT,
            tstamp $LONG_INT,
            FOREIGN KEY(id_painel) REFERENCES painel(id)
        );",
    "CREATE UNIQUE INDEX IF NOT EXISTS  
            idx_roleta_conteudo_unico 
            ON roleta (id_painel, titulo, numero);"
    
);

function init_db()
{
    global $db_file, $queries;
    header('Content-Type: text/plain');
    $db_file = new PDO(DATABASE_CONNECTION, DATABASE_USERNAME, DATABASE_PASSWORD);
    $db_file->beginTransaction();
    foreach ($queries as $query){
        //echo "\n$query";
        $db_file->exec($query);
    }
    $db_file->commit();
}

init_db();
