<?php

@include_once('config.php');

const DATABASE_FILE = __DIR__ . '/database.db';



if (session_id() == "") {
    session_start();
} else {
    // Anything you want
}


function init_db()
{
    $db_file = new PDO('sqlite:' . DATABASE_FILE);
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

    $db_file->exec("CREATE UNIQUE INDEX 
        idx_roleta_conteudo_unico 
        ON roleta (id_painel, marcador, numero);");
}

if (!file_exists(DATABASE_FILE)) {
    init_db();
}

$db_file = new PDO('sqlite:' . DATABASE_FILE);

function converter_chave_hash($codigo_chave)
{
    return hash('sha256', $codigo_chave);
}


function criar_painel($codigo_chave, $regressiva)
{
    global $db_file;
    $db_file->beginTransaction();
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "INSERT INTO painel (hash_code, regressiva) VALUES (?, ?)";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($hash_code, ($regressiva ? 1 : 0)));

    $db_file->commit();

    $query = "SELECT max(id) as max_id FROM painel";
    $stmt = $db_file->prepare($query);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $results['max_id'];
}


function validar_painel($id, $codigo_chave)
{
    global $db_file;
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "SELECT id FROM painel WHERE id = ? and hash_code = ?";
    $stmt = $db_file->prepare($query);
    $r = $stmt->execute(array($id, $hash_code));
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_array($results);
}


function ler_painel($id)
{
    global $db_file;
    $query = "SELECT * FROM painel WHERE id = ?";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($id));
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    $results['tempo_referencia'] = (int) microtime(1);
    unset($results['hash_code']);
    return $results;
}

function preparar_cronometro($id, $codigo_chave, $tempo_inicio)
{
    global $db_file;
    $db_file->beginTransaction();
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "UPDATE painel SET tempo_inicio = ?, inicio = null, fim = null WHERE id = ? and hash_code = ? ";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($tempo_inicio, $id, $hash_code));
    $db_file->commit();
}

function iniciar_cronometro($id, $codigo_chave)
{
    global $db_file;
    $db_file->beginTransaction();
    $c = ler_painel($id);
    $regressiva = $c['regressiva'];
    if ($regressiva) {
        $acrescimo = 2;
    } else {
        $acrescimo = 0;
    }

    $tempo_inicio = (int) $c['tempo_inicio'];
    $inicio =  (int) microtime(1) + $acrescimo;

    $fim = $inicio + $tempo_inicio + $acrescimo;

    $hash_code = converter_chave_hash($codigo_chave);
    $query = "UPDATE painel SET inicio = ?, fim = ? WHERE id = ? and hash_code = ? ";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($inicio, $fim, $id, $hash_code));

    $db_file->commit();
}



function ativar_rodada($id, $codigo_chave, $codigo_rodada_atual)
{
    global $db_file;
    $db_file->beginTransaction();
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "UPDATE painel SET codigo_rodada_atual = ? WHERE id = ? and hash_code = ? ";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($codigo_rodada_atual, $id, $hash_code));
    $db_file->commit();
}



function desativar_rodada($id, $codigo_chave)
{
    global $db_file;
    $db_file->beginTransaction();
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "UPDATE painel SET codigo_rodada_anterior = codigo_rodada_atual, codigo_rodada_atual = NULL WHERE id = ? and hash_code = ? ";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($id, $hash_code));
    $db_file->commit();
}


function mensagem_manual($id, $codigo_chave, $marcador_manual, $mensagem_manual)
{
    global $db_file;
    $db_file->beginTransaction();
    $hash_code = converter_chave_hash($codigo_chave);
    $query = "UPDATE painel SET marcador = ?, mensagem = ? WHERE id = ? and hash_code = ? ";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($marcador_manual,  $mensagem_manual, $id, $hash_code));
    $db_file->commit();
}


function criar_roleta($id, $codigo_chave, $marcador, $lista)
{
    global $db_file;

    if (validar_painel($id, $codigo_chave)) {
        $db_file->beginTransaction();
        $lista_real = explode(PHP_EOL, $lista);
        $numero = 0;
        $marcador = substr($marcador, 0, strpos($marcador, '.'));
        $query = "INSERT INTO roleta (id_painel, marcador, numero, conteudo) VALUES (?, ?, ?, ?)";
        $stmt = $db_file->prepare($query);
        foreach ($lista_real as $item) {            
            if ($item != ''){
                $numero++;
                $stmt->execute(array($id, $marcador,  $numero, $item));
            }
        }
        $db_file->commit();
    }
}

function obter_nomes_roleta($id)
{
    global $db_file;
    $query = "SELECT marcador FROM roleta WHERE id_painel = ? GROUP BY marcador";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($id));
    $results = $stmt->fetchAll(PDO::FETCH_NUM);
    return $results;
}

function obter_itens_roleta($id)
{
    global $db_file;
    $query = "SELECT numero, marcador, conteudo FROM roleta WHERE id_painel = ? ORDER BY marcador, numero";
    $stmt = $db_file->prepare($query);
    $stmt->execute(array($id));
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $results;
}


function obter_resultado_rodada($id, $codigo_rodada)
{
    global $db_file;

    $return_value = array();
    $return_value['id'] = $id;
    $return_value['codigo_rodada'] = $codigo_rodada;

    $param = array($id, $codigo_rodada);

    $query = "SELECT numero_aleatorio 
                FROM rodada WHERE id_painel = ? and codigo_rodada = ?  ";
    //var_dump($query);
    $stmt = $db_file->prepare($query);
    $stmt->execute($param);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $return_value['numeros'] = $results;
    $return_value['soma_todos_numeros'] = array_sum($results);

    $query = "SELECT 
                count(*) 
                 FROM roleta  
                WHERE id_painel = ? 
                and marcador = ?               
                ";

    $stmt = $db_file->prepare($query);
    $stmt->execute($param);

    $results = $stmt->fetch(PDO::FETCH_NUM);



    $return_value['numero_perguntas'] = (int) $results[0];

    if ($return_value['numero_perguntas'] != 0) {
        $return_value['resultado_divisao'] = $return_value['soma_todos_numeros'] / $results[0];
        $return_value['resto_divisao'] = $return_value['soma_todos_numeros'] % $results[0];
    }

    $query = "SELECT 
        *
        FROM roleta rl
        INNER JOIN rodada rd ON marcador = codigo_rodada 
            AND rl.id_painel = rl.id_painel 
        WHERE rl.id_painel = ? 
            AND marcador = ?               
        ";
    $stmt = $db_file->prepare($query);
    $stmt->execute($param);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //$return_value['resultado'] = $results;

    return $return_value;
}


function obter_numeros_rodada_atual($id)
{
    global $db_file;
    $numeros = array();
    $painel = ler_painel($id);
    if (isset($painel['id'])) {
        $codigo_rodada_atual = $painel['codigo_rodada_atual'];
        if ($codigo_rodada_atual) {
            $query = "SELECT 
                    id, 
                    numero_aleatorio as numero, 
                    CASE id_session 
                        WHEN ? THEN 1
                        ELSE 0
                    END as pessoal
                FROM rodada 
                WHERE id_painel = ? 
                AND codigo_rodada = ? 
                ORDER BY tstamp DESC";
            $stmt = $db_file->prepare($query);
            $stmt->execute(array(session_id(), $id, $codigo_rodada_atual));
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //foreach ($results as $result){
            //    $numeros[] = $result[0];
            //} 
            $numeros = $results;
        }
    }
    return $numeros;
}


function registrar_aleatorio($id, $codigo_rodada_atual, $numero_aleatorio)
{
    $painel = ler_painel($id);
    if (isset($painel['id'])) {
        global $db_file;
        $db_file->beginTransaction();
        $query = "INSERT INTO rodada (ip_address, id_painel, codigo_rodada, numero_aleatorio,tstamp, id_session) VALUES (NULL, ?, ?,  ?, ?, ?)";
        $stmt = $db_file->prepare($query);
        $stmt->execute(array($id,  $codigo_rodada_atual,  $numero_aleatorio, (int) microtime(1), session_id()));
        $db_file->commit();
    }
}
