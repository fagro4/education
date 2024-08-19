<?php

require_once __DIR__ . '/../../vendor/autoload.php';

define('HOST', getenv('TEST_RABBITMQ_HOST') ? getenv('TEST_RABBITMQ_HOST') : 'localhost');
define('PORT', getenv('TEST_RABBITMQ_PORT') ? getenv('TEST_RABBITMQ_PORT') : 5672);
define('USER', getenv('RABBITMQ_DEFAULT_USER') ? getenv('RABBITMQ_DEFAULT_USER') : 'quest');
define('PASS', getenv('RABBITMQ_DEFAULT_PASS') ? getenv('RABBITMQ_DEFAULT_PASS') : 'quest');
define('VHOST', '/');
define('AMQP_DEBUG', getenv('TEST_AMQP_DEBUG') !== false ? (bool)getenv('TEST_AMQP_DEBUG') : false);