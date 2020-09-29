<?php 
include_once 'database.php';

$global_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$codigo_chave = filter_input(INPUT_POST, 'codigo_chave', FILTER_SANITIZE_SPECIAL_CHARS);

$marcador = $_FILES['roleta_upload']["name"];
$marcador = preg_replace('/[^0-9,A-Z,a-z,.]+/', '_',  $marcador);

$lista = file_get_contents ($_FILES["roleta_upload"]["tmp_name"]);

var_dump($_FILES['roleta_upload']);

criar_roleta($global_id, $codigo_chave, $marcador, $lista);


header('Location: gerenciar.php?id=' . $global_id . '&codigo_chave=' . $codigo_chave);