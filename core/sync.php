<?php

require_once 'classes/Timer.php';



$t = Timer::timeCalc();
$dados = array();
$dados['syncCount'] = null;
$dados['localTime'] = null;
$dados['serverTimeMillis'] = Timer::timeServerInt($t);;
$dados['diff'] = null;
$dados['simul'] = Timer::isSimulated();
$dados['real'] = $t;



if (isset($_GET['localTime'])) {
    $microtime_user = (int) @$_GET['localTime'];
    $dados['diff'] =  $dados['serverTimeMillis'] - $microtime_user;
    $dados['localTime'] = $microtime_user;
} else {
    $dados['diff'] = $dados['serverTimeMillis'];
}
if (isset($_GET['syncCount'])) {
    $dados['syncCount'] = (int) $_GET['syncCount'];
}

header("Content-Type: application/json");
exit(json_encode($dados));

