<?php
declare(strict_types=1);

$config = require dirname(__DIR__) . '/config/config.php';
date_default_timezone_set($config['timezone']);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (!is_dir(dirname(__DIR__) . '/logs')) { mkdir(dirname(__DIR__) . '/logs', 0755, true); }
ini_set('error_log', dirname(__DIR__) . '/logs/error.log');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/media.php';
require_once __DIR__ . '/middleware.php';

start_secure_session($config);
$GLOBALS['config'] = $config;
$GLOBALS['db'] = db_connect($config);