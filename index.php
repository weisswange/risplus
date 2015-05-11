<?php

function risplusAutoloader($sClass)
{
    include_once('classes/class.' . $sClass . '.php');
}

spl_autoload_register('risplusAutoloader');

// include all required stuff
//include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.app.php');

// start app
$me = new App();
$me->run();