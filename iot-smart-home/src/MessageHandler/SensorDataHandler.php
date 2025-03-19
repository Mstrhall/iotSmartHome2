<?php

namespace App\MessageHandler;

use App\Message\SensorDataMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class SensorDataHandler
{
    private LoggerInterface $logger;
    private HubInterface $hub;

    public function __construct(LoggerInterface $logger, HubInterface $hub)
    {
        $this->logger = $logger;
        $this->hub = $hub;
    }

    public function __invoke(SensorDataMessage $message)
    {
        $sensorId = $message->getSensorId();
        $type = $message->getType();
        $value = $message->getValue();

        // Journalisation de la réception des données
        $this->logger->info("📡 Donnée reçue : Capteur ID {$sensorId}, Type : {$type}, Valeur : {$value}");

        // Optionnel : Validation minimale des données
        if (!is_numeric($value)) {
            $this->logger->warning("La valeur reçue pour le capteur {$sensorId} n'est pas numérique.");
            return;
        }

        // Préparer le contenu de l'update
        $data = [
            'sensorId' => $sensorId,
            'type' => $type,
            'value' => $value,
            'timestamp' => time(),
        ];

        $jsonData = json_encode($data);

        // Création de la mise à jour à publier via Mercure
        $update = new Update(
            '/sensor-data', // Le topic sur lequel publier
            $jsonData
        );

        try {
            $this->hub->publish($update);
            $this->logger->info("Mise à jour envoyée via Mercure pour le capteur ID {$sensorId}.");
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la publication via Mercure pour le capteur ID {$sensorId}: " . $e->getMessage());
            // Relancer l'exception si besoin pour que le message soit re-traité par Messenger
            throw $e;
        }
    }
}
