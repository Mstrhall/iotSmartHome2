<?php

namespace App\MessageHandler;

use App\Message\SensorDataMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SecurityAlertHandler
{
    public function __invoke(SensorDataMessage $message)
    {
        if ($message->getType() === 'security' && $message->getValue() === 1.0) {
            echo "🚨 ALERTE SÉCURITÉ ! Mouvement détecté !\n";
        }
    }
}
