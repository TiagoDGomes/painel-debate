<?php

include_once('database.php');
header('Content-Type: application/json');

$global_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$codigo_rodada_atual = filter_input(INPUT_GET, 'codigo_rodada_atual', FILTER_SANITIZE_SPECIAL_CHARS);
$numero_aleatorio = filter_input(INPUT_GET, 'numero_aleatorio', FILTER_SANITIZE_SPECIAL_CHARS);

$painel = ler_painel($global_id);

if (!isset($painel['codigo_rodada_atual']) || $painel['codigo_rodada_atual'] != $codigo_rodada_atual){
    header('HTTP/1.0 403 Forbidden');
    echo '{"erro":"Proibido"}';
} else {
    registrar_aleatorio($global_id, $codigo_rodada_atual, $numero_aleatorio);    
    echo '{"erro": null}';
}
