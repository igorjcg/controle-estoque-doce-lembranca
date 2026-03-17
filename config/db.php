<?php

if (file_exists(__DIR__ . '/db-local.php')) {
    return require __DIR__ . '/db-local.php';
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=controle_estoque',
	'username' => getenv('DB_USER'),
	'password' => getenv('DB_PASS'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
