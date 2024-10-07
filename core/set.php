<?php if (!defined('PCORE')) die("Nope"); 

require_once 'core.php';
switch ($_GET['prop_name']){
    case 'timer-prepared':
        Timer::setPreparedTime($_GET['prop_value']);
        break;
    case 'timer-start':
        Timer::start($_GET['prop_value']);

}

require_once 'info.php';

