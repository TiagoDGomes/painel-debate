<?php
include_once('database.php');
header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $painel = ler_painel((int) $_GET['id']);
    $painel['rodada_atual'] = obter_numeros_rodada_atual((int) $_GET['id']);
    $json = json_encode($painel);
    echo $json;
} 