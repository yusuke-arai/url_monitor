<?php
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
define('DB_FILE', $config['db_file']);
define('HTTP_CHECK_SCRIPT', __DIR__ . '/http_check.py');
