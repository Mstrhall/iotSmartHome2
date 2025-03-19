<?php
namespace App\MessageHandler;

use App\Message\SensorDataMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ThermostatHandler
{
    public function __invoke(SensorDataMessage $message)
    {
        if ($message->getType() === 'temperature' && $message->getValue() > 25) {
            echo "🔥 Température élevée : Ajustement du thermostat...\n";
        }
    }
}
