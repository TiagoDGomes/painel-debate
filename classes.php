<?php

if (!defined('PAINEL_ROOT')) die("Nope");

@include_once('config.php');
@include_once('relogio.php');

const QUERYPART_RESTRICAO_PERMISSAO_PAINEL = 'p.id = ? AND ((p.chave_usuario = ?) OR (p.chave_usuario IS NULL) OR (p.chave_gerencia = ?) )';
const QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL = 'p.id = ? AND p.chave_gerencia = ?';

try {
    $db_file = new PDO(DATABASE_CONNECTION, DATABASE_USERNAME, DATABASE_PASSWORD);
} catch (PDOException $pe) {
    exit('Database connection error: ' . $pe->getMessage());
}


class Painel
{
    public static function novo()
    {
        global  $array_resposta_json;
        $novo_descricao = filter_input(INPUT_POST, POST_NOVO_DESCRICAO, FILTER_UNSAFE_RAW);
        $novo_chave_usuario = filter_input(INPUT_POST, POST_NOVO_CHAVE_USUARIO, FILTER_UNSAFE_RAW);
        $novo_chave_gerencia = filter_input(INPUT_POST, POST_NOVO_CHAVE_GERENCIA, FILTER_UNSAFE_RAW);
        $query = 'INSERT INTO painel (descricao, chave_usuario, chave_gerencia) VALUES (?,?,?)';
        $param = array($novo_descricao, $novo_chave_usuario, $novo_chave_gerencia);
        try {
            Database::execute($query, $param);

            $query = "SELECT max(id) as max_id FROM painel";
            $results = Database::fetch($query, array());

            header('Location: ?' . http_build_query(array(
                GET_NOVO_OK => $results['max_id'],
                GET_CHAVE_USUARIO => $novo_chave_usuario,
                GET_CHAVE_GERENCIA => $novo_chave_gerencia
            )));
        } catch (Exception $exc) {
            $array_resposta_json['erro'] = $exc;
        }
    }
    public static function buscarAtualizacoes()
    {
        global $array_resposta_json, $global_id, $chave_usuario_atual, $chave_gerencia_atual;
        $array_resposta_json['acao'] = 'update';
        $query = 'SELECT cronometro_tempo_preparado,
                         cronometro_tempo_inicio,
                         cronometro_tempo_fim,
                         mensagem_titulo,
                         mensagem_conteudo,
                         descricao,
                         codigo_sorteio_atual,
                         codigo_sorteio_anterior,
                         codigo_roleta_atual,
                         codigo_roleta_anterior,
                         ultimo_numero_sorteado
                    FROM painel AS p
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_PAINEL;
        $param = array($global_id, $chave_usuario_atual, $chave_gerencia_atual);
        try {
            $results = Database::fetch($query, $param);
            $array_resposta_json['update_info'] = $results;
        } catch (Exception $exc) {
            $array_resposta_json['erro'] = $exc;
        }
    }
    public static function verificarPermissaoParaPaginaOuErro($ignorar_json = FALSE)
    {
        global $aguardando_resposta_json, $global_id, $chave_usuario_atual, $chave_gerencia_atual;
        if (!$aguardando_resposta_json || $ignorar_json) {
            $query = 'SELECT * FROM painel AS p WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_PAINEL;
            $param = array($global_id, $chave_usuario_atual, $chave_gerencia_atual);
            try {
                $results = Database::fetch($query, $param);
                if (!is_array($results)) {
                    header('HTTP/1.1 403 Forbidden');
                    exit('Proibido por permissão');
                }
                //var_dump($GLOBALS);
                //exit();
                return;
            } catch (Exception $exc) {
                header('HTTP/1.1 403 Forbidden');
                exit('Proibido com erro');
            }
            header('HTTP/1.1 403 Forbidden');
            exit('Proibido por razão desconhecida');
        }
    }
}


class Cronometro
{


    public static function preparar($tempo)
    {
        global $global_id,  $chave_gerencia_atual, $array_resposta_json;
        $query = 'UPDATE painel AS p
                SET cronometro_tempo_preparado = ?, 
                    cronometro_tempo_inicio = NULL,
                    cronometro_tempo_fim = NULL WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($tempo, $global_id,  $chave_gerencia_atual);
        try {
            $array_resposta_json['preparar'] = Database::execute($query, $param);
        } catch (Exception $exc) {
            $array_resposta_json['preparar'] = false;
        }
    }
    public static function iniciar()
    {
        global $global_id,  $chave_gerencia_atual, $array_resposta_json;
        Database::beginTransaction();
        $tempo_agora = intval(Relogio::tempoServidor()) + 3;
        $query = 'UPDATE painel AS p
                    SET 
                        cronometro_tempo_inicio = ? 
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($tempo_agora, $global_id, $chave_gerencia_atual);
        Database::execute($query, $param);
        $query = 'UPDATE painel AS p
                        SET 
                            cronometro_tempo_fim = cronometro_tempo_inicio + cronometro_tempo_preparado 
                        WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($global_id, $chave_gerencia_atual);
        Database::execute($query, $param);
        Database::commit();
    }
}

class Roleta
{
    public static function obterItensRoletas()
    {
        global $global_id;
        Painel::verificarPermissaoParaPaginaOuErro(TRUE);
        $query = 'SELECT roleta.id as id_roleta, numero, titulo, conteudo 
                    FROM roleta 
                    INNER JOIN itens_roleta ON itens_roleta.id_roleta = roleta.id
                  WHERE id_painel = ?';
        $param = array($global_id);
        return Database::fetchAll($query, $param);
    }

    public static function tratarInclusaoItensRoleta()
    {
        global $global_id;
        Painel::verificarPermissaoParaPaginaOuErro(TRUE);
        Database::beginTransaction();
        $titulo = $_FILES['roleta_upload']["name"];
        $lista = file_get_contents($_FILES["roleta_upload"]["tmp_name"]);
        $lista_real = explode(PHP_EOL, $lista);
        $numero = 1;
        $titulo = substr($titulo, 0, strpos($titulo, '.'));

        $query = "INSERT INTO roleta (id_painel, titulo) VALUES (?, ?)";
        $param = array($global_id, $titulo);        
        Database::execute($query, $param);

        $query = "SELECT max(id) FROM roleta";
        $roleta_id = Database::fetchOne($query, array());
         
        $query = "INSERT INTO itens_roleta (id_roleta, numero, conteudo) VALUES (?, ?, ?)";
        
        foreach ($lista_real as $conteudo) {
            if ($conteudo != '') {
                $param = array($roleta_id, $numero, $conteudo);
                $numero++;
                Database::execute($query, $param);
            }
        }
        Database::commit();
        redirecionar_para_atualizar();
        
    }
    public static function iniciarSorteio($codigo_roleta_atual)
    {
        global $global_id,  $chave_gerencia_atual, $array_resposta_json;
        $array_resposta_json['acao'] = 'iniciar_sorteio';
        $query = 'UPDATE painel AS p
                    SET codigo_sorteio_atual = codigo_sorteio_anterior + 1,
                    codigo_roleta_atual = ?,
                    ultimo_numero_sorteado = NULL 
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($codigo_roleta_atual, $global_id, $chave_gerencia_atual);
        try {
            //$array_resposta_json['query'] = $query;
            $array_resposta_json['iniciar_sorteio'] = Database::execute($query, $param);
        } catch (Exception $exc) {
            $array_resposta_json['iniciar_sorteio'] = false;
        }
    }
    public static function terminarSorteio()
    {
        global $global_id,  $chave_gerencia_atual, $array_resposta_json;

        Painel::verificarPermissaoParaPaginaOuErro(TRUE);
        
        $array_resposta_json['acao'] = 'terminar_sorteio';

        $query = 'UPDATE painel AS p
                     SET codigo_sorteio_anterior = codigo_sorteio_atual, 
                         codigo_roleta_anterior = codigo_roleta_atual
                     WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($global_id, $chave_gerencia_atual);

        $query2 = 'UPDATE painel AS p
                    SET codigo_sorteio_atual = NULL,
                        codigo_roleta_atual = NULL            
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param2 = array($global_id, $chave_gerencia_atual);

        $query3 = 'SELECT sum(somatorio) FROM sorteio                                
                        WHERE id_painel = ? 
                        AND id_roleta = (SELECT codigo_roleta_anterior FROM painel AS p WHERE p.id = ?)
                        AND codigo_sorteio = (SELECT codigo_sorteio_anterior FROM painel AS p WHERE p.id = ?)';
        $param3 = array($global_id, $global_id, $global_id);

        $query4 = 'SELECT count(*) FROM itens_roleta                                
                        WHERE id_roleta = (SELECT codigo_roleta_anterior FROM painel AS p WHERE p.id = ?)';
        $param4 = array($global_id);

        $query5 = 'UPDATE painel AS p
                    SET ultimo_numero_sorteado = ?           
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;


        try {
            Database::beginTransaction();
            Database::execute($query, $param);
            Database::execute($query2, $param2);
            $somatorio = Database::fetchOne($query3, $param3);
            $numero_alternativas = Database::fetchOne($query4, $param4);
            $array_resposta_json['somatorio'] = $somatorio;
            $array_resposta_json['numero_alternativas'] = $numero_alternativas;
            if ($somatorio != null) {
                if ($numero_alternativas == 0 || $numero_alternativas == NULL) {
                    $numero_alternativas = 2;
                }
                $numero_sorteado = ($somatorio % $numero_alternativas) + 1;
                $param5 = array($numero_sorteado, $global_id, $chave_gerencia_atual);
                Database::execute($query5, $param5);
                $array_resposta_json['numero_sorteado'] = $numero_sorteado;
            }
            Database::commit();
        } catch (Exception $exc) {
            $array_resposta_json['terminar_sorteio'] = false;
        }
    }
    public static function obterSorteado()
    {
    }
    public static function incluirAleatorios($dados_brutos)
    {
        global $array_resposta_json,  $global_id, $chave_usuario_atual, $chave_gerencia_atual, $ip_address;
        $array_resposta_json['acao'] = 'aleatorios';
        $numeros_obtidos = array_map('intval', explode(' ', $dados_brutos));
        $somatorio = array_sum($numeros_obtidos);
        $timestamp = Relogio::tempoServidorInt();
        $array_resposta_json['numeros_obtidos'] = $numeros_obtidos;
        $array_resposta_json['somatorio'] = $somatorio;
        $query_codigo_roleta_atual = 'SELECT codigo_roleta_atual FROM painel AS p WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_PAINEL;
        $codigo_sorteio_atual = "SELECT codigo_sorteio_atual FROM painel AS p WHERE p.id = $global_id";

        $query = "INSERT INTO sorteio 
                         (tstamp, ip_address, id_session, id_painel, somatorio, numeros_obtidos, id_roleta, codigo_sorteio)
                    VALUES (?,?,?,?,?,?, ($query_codigo_roleta_atual), ($codigo_sorteio_atual))";
        $param = array($timestamp, $ip_address, session_id(), $global_id, $somatorio, $dados_brutos, $global_id, $chave_usuario_atual, $chave_gerencia_atual);
        try {
            $array_resposta_json['incluir-aleatorios'] = Database::execute($query, $param);
        } catch (Exception $exc) {
            $array_resposta_json['incluir-aleatorios'] = false;
        }
    }
    public static function autorizado()
    {
        return true;
    }
}
class Mensagem
{

    public static function enviar($titulo, $conteudo)
    {
        global $global_id,  $chave_gerencia_atual, $array_resposta_json;
        $array_resposta_json['acao'] = 'mensagem';
        $query = 'UPDATE painel AS p
                    SET mensagem_titulo = ? ,
                        mensagem_conteudo = ?                    
                    WHERE ' . QUERYPART_RESTRICAO_PERMISSAO_GERENCIA_PAINEL;
        $param = array($titulo, $conteudo, $global_id, $chave_gerencia_atual);
        try {
            $array_resposta_json['salvar-mensagem'] = Database::execute($query, $param);
        } catch (Exception $exc) {
            $array_resposta_json['salvar-mensagem'] = false;
        }
    }
    
}


class Database
{
    public static function beginTransaction()
    {
        global $db_file;
        $db_file->beginTransaction();
    }
    public static function commit()
    {
        global $db_file;
        $db_file->commit();
    }
    public static function fetch($query, $param, $mode = PDO::FETCH_ASSOC)
    {
        global $db_file;
        $stmt = $db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetch($mode);
    }
    public static function fetchOne($query, $param)
    {
        global $db_file;
        $stmt = $db_file->prepare($query);
        $stmt->execute($param);
        $f = $stmt->fetch();
        return $f[0];
    }
    public static function fetchAll($query, $param, $mode = PDO::FETCH_ASSOC)
    {
        global $db_file;
        $stmt = $db_file->prepare($query);
        $stmt->execute($param);
        return $stmt->fetchAll($mode);
    }
    public static function execute($query, $param)
    {
        global $db_file;
        $stmt = $db_file->prepare($query);
        return $stmt->execute($param);
    }
}
