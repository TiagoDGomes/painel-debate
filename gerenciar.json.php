<?php

include_once('database.php');
header('Content-Type: application/json');
$json = '{}';


if (isset($_GET['id'])) {
    switch (filter_input(INPUT_GET, 'acao')) {
        case 'preparar':
            $json = preparar_cronometro(
                filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'tempo_definido', FILTER_SANITIZE_SPECIAL_CHARS)
            );
            break;
        case 'iniciar':
            $json = iniciar_cronometro(
                filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS)
            );
            break;
        case 'rodada':
            if (isset($_GET['codigo_rodada_atual']) && $_GET['codigo_rodada_atual'] != '') {
                $json = ativar_rodada(
                    filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS),
                    filter_input(INPUT_GET, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS),
                    filter_input(INPUT_GET, 'codigo_rodada_atual', FILTER_SANITIZE_SPECIAL_CHARS)
                );
            } else {
                $json = desativar_rodada(
                    filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS),
                    filter_input(INPUT_GET, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS)
                );
            }
            break;
        case 'mensagem':
            $json = mensagem_manual(
                filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'marcador_manual', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_input(INPUT_GET, 'mensagem_manual', FILTER_SANITIZE_SPECIAL_CHARS)
            );
            break;
    }
}
echo $json;
