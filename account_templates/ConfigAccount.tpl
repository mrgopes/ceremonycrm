<?php

// urls

$config['logDir']                        = __DIR__ . '/log';
$config['tmpDir']                        = __DIR__ . '/tmp';
$config['uploadDir']                     = __DIR__ . '/upload';
$config['uploadUrl']                     = '//' . ($_SERVER['HTTP_HOST'] ?? '') . $config['rewriteBase'] . 'upload';

// db
$config["db_host"]             = '{{ dbHost }}';
$config["db_user"]             = '{{ dbUser }}';
$config["db_password"]         = '{{ dbPassword }}';
$config["db_name"]             = '{{ dbName }}';
$config["db_codepage"]         = 'utf8mb4';
$config["global_table_prefix"] = '';

// misc
$config['develMode']           = TRUE;
$config['language']            = 'en';