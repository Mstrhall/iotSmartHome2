<?php

// src/Message/SensorDataMessage.php
namespace App\Message;

class SensorDataMessage
{
    public function __construct(
        private int $sensorId,
        private string $type,
        private float $value
    ) {}

    public function getSensorId(): int
    {
        return $this->sensorId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
