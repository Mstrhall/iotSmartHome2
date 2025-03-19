<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SensorDataMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Psr\Log\LoggerInterface;  // N'oublie pas d'importer LoggerInterface

class SensorController extends AbstractController
{
    private LoggerInterface $logger;

    // Injection du service LoggerInterface via le constructeur
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/sensor', methods: ['POST'])]
    public function receiveSensorData(Request $request, MessageBusInterface $messageBus, HubInterface $hub): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['sensorId'], $data['type'], $data['value'])) {
            return $this->json(['error' => 'Données invalides'], 400);
        }

        // Publier l'événement dans RabbitMQ via Symfony Messenger
        $messageBus->dispatch(new SensorDataMessage(
            (int) $data['sensorId'],
            $data['type'],
            (float) $data['value']
        ));

        // Log avant de publier à Mercure
        $this->logger->info("📡 Envoi à Mercure: " . json_encode($data));
        $update = new Update(
            '/sensor-data',
            json_encode($data)
        );
        $hub->publish($update);

        return $this->json(['status' => 'Message envoyé à RabbitMQ et Mercure'], 200);
    }

    #[Route('/api/mercure-hub', methods: ['GET'])]
    public function mercureHub(HubInterface $hub): JsonResponse
    {
        return $this->json([
            'mercure_url' => $_ENV['MERCURE_PUBLIC_URL'],
        ]);
    }

}
