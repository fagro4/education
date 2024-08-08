<?php

namespace App\Amqp;

use App\Exceptions\IncommingMessageException;
use App\Interfaces\IncommingMessage;
use PhpAmqpLib\Message\AMQPMessage;

class MessageAdapter implements IncommingMessage
{
    private object $data;
    public function __construct(private AMQPMessage $message)
    {
        $this->data = JSON_DECODE($message->body);
        if (empty($this->data)) {
            throw new IncommingMessageException("Некорректный формат тела сообщения");
        }
    }

    public function getCommandCode(): string
    {
        if (empty($this->data->command))
            throw new IncommingMessageException("Не указан код команды");

        return $this->data->command;
    }

    public function getGameId(): string
    {
        if (empty($this->data->game))
            throw new IncommingMessageException("Не указан идентификатор игры");

        return $this->data->game;
    }

    public function getObjectId(): string
    {
        if (empty($this->data->object))
            throw new IncommingMessageException("Не указан идентификатор объекта");

        return $this->data->object;
    }

    public function getToken(): string
    {
        if (empty($this->data->token))
            throw new IncommingMessageException("Не указан Токен авторизации");

        return $this->data->token;
    }

    public function getParams(): array
    {
        return $this->data->params ?? [];
    }
}