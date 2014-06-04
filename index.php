<?php

// include all required stuff
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.app.php');

// start app
$me = new App();
$me->run();