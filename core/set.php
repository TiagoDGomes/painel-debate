<?php if (!defined('PCORE')) die("Nope"); 

require_once 'core.php';

switch (@$_GET['prop_name']){
    case 'timer-prepared':
        Timer::setPreparedTime($_GET['prop_value']);
        break;
    case 'timer-start':
        Timer::start($_GET['prop_value']);
        break;
    case 'text-content':
        break;
}
if (isset($_POST['text-content'])){
    Property::set('text-content', strip_tags($_POST['text-content'], array(                                                    
                                                    '<span>','<p>','<br>','<div>',
                                                    '<strong>','<b>',
                                                    '<i>','<em>',
                                                    '<u>','<del>',
                                                    '<ul>','<ol>','<li>',
                                                    '<blockquote>'
                                                )));
}

require_once 'info.php';

