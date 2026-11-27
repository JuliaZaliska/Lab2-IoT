<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/sensors', name: 'sensors_index', methods: ['GET'])]
    public function index(Request $request, SensorRepository $sensorRepository): JsonResponse
    {
        $query = $request->query->get('q');

        $locationRaw = $request->query->get('location');
        $locationId = (is_numeric($locationRaw) && (int) $locationRaw > 0) ? (int) $locationRaw : null;

        $status = $request->query->get('status');
        $onlyAlerts = $request->query->getBoolean('alerts', false);

        $sensors = $sensorRepository->findFiltered($query, $locationId, $status, $onlyAlerts);
        $data = array_map(fn (Sensor $s) => $this->serializeSensor($s), $sensors);

        return $this->json([
            'count' => count($data),
            'items' => $data,
        ]);
    }

    #[Route('/sensors/{id}', name: 'sensors_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, SensorRepository $sensorRepository): JsonResponse
    {
        $sensor = $sensorRepository->find($id);

        if (!$sensor) {
            return $this->json(['error' => 'Датчик не знайдено'], 404);
        }

        return $this->json($this->serializeSensor($sensor));
    }

    private function serializeSensor(Sensor $sensor): array
    {
        $location = $sensor->getLocation();

        return [
            'id' => $sensor->getId(),
            'name' => $sensor->getName(),
            'sensorType' => $sensor->getSensorType(),
            'currentValue' => $sensor->getCurrentValue(),
            'unit' => $sensor->getUnit(),
            'alertThreshold' => $sensor->getAlertThreshold(),
            'isAlert' => $sensor->isAlert(),
            'status' => $sensor->getStatus(),
            'updatedAt' => $sensor->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'location' => $location ? [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'zone' => $location->getZone(),
            ] : null,
        ];
    }
}