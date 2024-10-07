<?php if (!defined('PCORE')) die("Nope"); 



$response = Property::getAll();
$response['serverTimeMillis'] = Timer::timeServerInt();



HTTPResponse::JSON($response);