<?php

namespace web;

use App\Src\Autoloader;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once __DIR__ . '/../App/Src/Autoloader.php';
Autoloader::register();

$app = require_once __DIR__ . '/../App/Bootstrap.php';
$app->run();
