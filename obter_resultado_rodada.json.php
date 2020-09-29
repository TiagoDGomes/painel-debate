<?php
include_once('database.php');
header('Content-Type: application/json');

//echo json_encode($_GET);

if (isset($_GET['id']) && isset($_GET['codigo_rodada'])) {
    $codigo_rodada = filter_input(INPUT_GET, 'codigo_rodada', FILTER_SANITIZE_SPECIAL_CHARS);
    $res = obter_resultado_rodada((int) $_GET['id'], $codigo_rodada);
    $json = json_encode($res);
    echo $json;
}
