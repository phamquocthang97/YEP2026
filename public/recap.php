<?php

require dirname(__DIR__) . '/bootstrap.php';

$event = loadEvent();
$recap = require BASE_PATH . '/config/recap.php';

require BASE_PATH . '/views/recap.php';
