<?php
header('Content-Type: application/json');
include 'relogio.php';


$t = Relogio::tempoCalculoReal();
$dados = array();
$dados['id'] = null;
$dados['tl'] = null;
$dados['ts'] = Relogio::tempoServidorInt($t);;
$dados['diff'] = null;
$dados['simul'] = Relogio::tempoSimulado();
$dados['real'] = $t;



if (isset($_GET['tl'])) {
    $microtime_user = (int) @$_GET['tl'];
    $dados['diff'] =  $dados['ts'] - $microtime_user;
    $dados['tl'] = $microtime_user;
} else {
    $dados['diff'] = $dados['ts'];
}
if (isset($_GET['id'])) {
    $dados['id'] = (int) $_GET['id'];
}

echo json_encode($dados);
