<?php
define('PAINEL_ROOT', true);
$versao = '1.0';
const GET_CHAVE_ID = 'id';
const GET_CHAVE_USUARIO = 'u';
const GET_CHAVE_GERENCIA = 'g';
const GET_NUMEROS_ALEATORIOS = 'n';
const GET_MENSAGEM_TITULO = 'mt';
const GET_MENSAGEM_CONTEUDO = 'mc';
const GET_MENSAGEM_LIMPAR = 'mz';
const GET_PREPARAR_CRONOMETRO = 'pc';
const GET_BUSCANDO_ATUALIZACAO = 'up';
const GET_ESPERA_JSON = 'json';
const POST_NOVO_DESCRICAO = 'descricao';
const POST_NOVO_CHAVE_USUARIO = 'chave_usuario';
const POST_NOVO_CHAVE_GERENCIA = 'chave_gerencia';
const GET_NOVO_OK = 'ok';
const GET_INICIAR_CRONOMETRO = 'st';
const GET_INICIAR_SORTEIO = 'iniciarsorteio';
const GET_TERMINAR_SORTEIO = 'terminarsorteio';

@include_once('relogio.php');

$array_resposta_json = array(
    'erro' => NULL,
    'acao' => NULL,
    'timestamp' => (int) Relogio::tempoServidor(),
    'rnd' => rand(0, 999999),
    '_get' => $_GET,
    'simul' => Relogio::tempoSimulado()
);


function error_handler($e)
{
    global $array_resposta_json;
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    exit(json_encode($array_resposta_json));
}

function default_exception_handler($e)
{
    global $array_resposta_json;
    $array_resposta_json['erro'] = $e->getMessage();
    $array_resposta_json['erro_tipo'] = 'exception';
    header('Content-Type: application/json');
    exit(json_encode($array_resposta_json));
}
function default_error_handler($e)
{
    global $array_resposta_json;
    $array_resposta_json['erro'] = var_export($e);
    $array_resposta_json['erro_tipo'] = 'error';
    header('Content-Type: application/json');
    exit(json_encode($array_resposta_json));
    //error_handler($e);
}
ob_start();
function default_fatal_error_handler()
{
    global $array_resposta_json, $aguardando_resposta_json;
    $error = error_get_last();
    if ($error != NULL) {
        ob_clean();
        include_once('install.php');
        if ($aguardando_resposta_json) {
            $array_resposta_json['erro'] = $error;
            $array_resposta_json['erro_tipo'] = 'error';
            header('Content-Type: application/json');
            exit(json_encode($array_resposta_json));
        } else {
            echo '<pre style="color:red; border: 1px solid black">';
            echo var_dump($error);
            echo '</pre>';
        }
    }
    //error_handler($e);
}

//set_exception_handler("default_exception_handler");
//set_error_handler('default_error_handler');
register_shutdown_function("default_fatal_error_handler");

include_once 'classes.php';



if (session_id() == "") {
    session_start();
}

$body_class = '';

$painel_valido = NULL;

$painel_titulo = '&nbsp;';

$titulo = '';

$itens_roleta = array();
$nomes_roleta = array();

$global_id = filter_input(INPUT_GET, GET_CHAVE_ID, FILTER_SANITIZE_NUMBER_INT);
$tempo_preparado = filter_input(INPUT_GET, GET_PREPARAR_CRONOMETRO, FILTER_SANITIZE_NUMBER_INT);
$chave_usuario_atual = filter_input(INPUT_GET, GET_CHAVE_USUARIO, FILTER_UNSAFE_RAW);
$chave_usuario_atual = $chave_usuario_atual ? $chave_usuario_atual : '';
$chave_gerencia_atual = filter_input(INPUT_GET, GET_CHAVE_GERENCIA, FILTER_UNSAFE_RAW);
$chave_gerencia_atual = $chave_gerencia_atual ? $chave_gerencia_atual : '';
$numeros_aleatorios = filter_input(INPUT_GET, GET_NUMEROS_ALEATORIOS, FILTER_UNSAFE_RAW);
$mensagem_titulo = filter_input(INPUT_GET, GET_MENSAGEM_TITULO, FILTER_UNSAFE_RAW);
$mensagem_conteudo = filter_input(INPUT_GET, GET_MENSAGEM_CONTEUDO, FILTER_UNSAFE_RAW);
$roleta_ativa = filter_input(INPUT_GET, GET_INICIAR_SORTEIO, FILTER_SANITIZE_NUMBER_INT);

$ip_address = isset($_SERVER['X-Real-IP']) ? $_SERVER['X-Real-IP'] : $_SERVER['REMOTE_ADDR'];

// ===== Definições ======= //
$acessando_como_usuario = (isset($_GET[GET_CHAVE_USUARIO]) && (!isset($_GET[GET_NOVO_OK]))) || ($global_id > 0 && !isset($_GET[GET_CHAVE_USUARIO]) && !isset($_GET[GET_CHAVE_GERENCIA]));
$acessando_como_gerencia = isset($_GET[GET_CHAVE_GERENCIA]) && !isset($_GET[GET_NOVO_OK]) && !isset($_GET[GET_CHAVE_USUARIO]);
$acessando_para_cadastrar_novo = isset($_POST[POST_NOVO_DESCRICAO]);
$acessando_apos_cadastrar_novo = isset($_GET[GET_NOVO_OK]);
$acessando_para_novo_em_branco = (!$acessando_como_usuario && !$acessando_como_gerencia && !$acessando_para_cadastrar_novo && !$acessando_apos_cadastrar_novo);
$enviando_novo_aleatorio = (!$acessando_para_novo_em_branco) && isset($_GET[GET_NUMEROS_ALEATORIOS]);
$enviando_mensagem = ($acessando_como_gerencia) && isset($_GET[GET_MENSAGEM_TITULO]) && isset($_GET[GET_MENSAGEM_CONTEUDO]);
$limpando_mensagem = ($acessando_como_gerencia) && isset($_GET[GET_MENSAGEM_LIMPAR]);
$buscando_atualizacao = (!$acessando_para_novo_em_branco) && isset($_GET[GET_BUSCANDO_ATUALIZACAO]);

$enviando_lista_roleta = $acessando_como_gerencia && isset($_FILES["roleta_upload"]);

$iniciar_sorteio = $acessando_como_gerencia && isset($_GET[GET_INICIAR_SORTEIO]);
$terminar_sorteio = $acessando_como_gerencia && isset($_GET[GET_TERMINAR_SORTEIO]);

$aguardando_resposta_json  = isset($_GET[GET_ESPERA_JSON]);

$enviando_inicio_cronometro = isset($_GET[GET_INICIAR_CRONOMETRO]);


$url_base_array[GET_CHAVE_USUARIO] = $chave_usuario_atual;
$url_base_array[GET_CHAVE_GERENCIA] = $chave_gerencia_atual;

$url_base = '?' . http_build_query($_GET) . '&';
$url_file = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER["HTTP_HOST"] .  $_SERVER["SCRIPT_NAME"];
$url_full = $url_file . $url_base;

function redirecionar_para_atualizar()
{
    global $url_base;
    exit(header('Location: ' . $url_base));
}

// ========= Acoes possiveis: ========= //
if ($acessando_como_usuario || $acessando_como_gerencia) {
    Painel::verificarPermissaoParaPaginaOuErro();
    if ($enviando_novo_aleatorio) {
        Roleta::incluirAleatorios($numeros_aleatorios);
    } else if ($buscando_atualizacao) {
        Painel::buscarAtualizacoes();
    }
    if ($acessando_como_gerencia) {
        $body_class .= ' gerencia';
        if ($enviando_mensagem) {
            $body_class .= ' enviando-mensagem';
            Mensagem::enviar($mensagem_titulo, $mensagem_conteudo);
        } else if ($limpando_mensagem) {
            Mensagem::enviar(NULL, NULL);
        } else if ($tempo_preparado > 0) {
            $body_class .= ' tempo-preparado';
            Cronometro::preparar($tempo_preparado);
        } else if ($enviando_inicio_cronometro) {
            $body_class .= ' enviando-inicio-cronometro';
            Cronometro::iniciar();
        } else if ($iniciar_sorteio) {
            $body_class .= ' iniciar-sorteio';
            Roleta::iniciarSorteio($roleta_ativa);
        } else if ($terminar_sorteio) {
            $body_class .= ' terminar-sorteio';
            Roleta::terminarSorteio();
        } else if ($enviando_lista_roleta) {
            $body_class .= ' tratar-inclusao-itens-roleta';
            Roleta::tratarInclusaoItensRoleta();
        } else {
            $body_class .= ' normal';
            $itens_roleta = Roleta::obterItensRoletas();
        }
        $painel_titulo = "Painel de gerência";
    } else {
        $body_class .= ' usuario';
    }
} else if ($acessando_para_novo_em_branco) {
    $titulo = 'Novo painel';
    $body_class .= ' novo';
    $nova_chave_gerencia_aleatoria = hash('sha256', rand(0, 999999999) . Relogio::tempoServidorInt() . rand(0, 999999999));
    $nova_chave_usuario_aleatoria = substr(hash('sha256', rand(0, 99999) . Relogio::tempoServidorInt() . rand(0, 999999999)), 0, 8);
} else if ($acessando_para_cadastrar_novo) {
    Painel::novo();
} else if ($acessando_apos_cadastrar_novo) {
    $painel_titulo = 'Novo painel criado';
    $titulo = $painel_titulo;
    $body_class .= ' novo-criado';
    $global_id = filter_input(INPUT_GET, GET_NOVO_OK, FILTER_SANITIZE_NUMBER_INT);
    $link_gerencia = '?' . http_build_query(array(
        GET_CHAVE_ID => $global_id,
        GET_CHAVE_GERENCIA => $chave_gerencia_atual
    ));
    $link_usuario = '?' . http_build_query(array(
        GET_CHAVE_ID => $global_id,
        GET_CHAVE_USUARIO => $chave_usuario_atual
    ));
}
