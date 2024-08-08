<?php

include(__DIR__ . '/config.php');
require_once '../../vendor/autoload.php';

use App\Amqp\MessageAdapter;
use App\Controllers\ProcessIncomingMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

$exchange = 'router';
$queue = 'msgs';
$consumerTag = 'consumer';

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
$channel->queue_bind($queue, $exchange);
$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

function process_message(\PhpAmqpLib\Message\AMQPMessage $message)
{
    $incommingMessage = new MessageAdapter($message);
    $controller = new ProcessIncomingMessage();
    $controller->handle($incommingMessage);

    $message->ack();
}

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

$channel->consume();