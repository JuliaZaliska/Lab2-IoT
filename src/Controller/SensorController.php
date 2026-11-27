<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\LocationRepository;
use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SensorController extends AbstractController
{
    #[Route('/', name: 'app_sensor_index', methods: ['GET'])]
    public function index(
        Request $request,
        SensorRepository $sensorRepository,
        LocationRepository $locationRepository
    ): Response {
        $query = $request->query->get('q');

        $locationRaw = $request->query->get('location');
        $locationId = (is_numeric($locationRaw) && (int) $locationRaw > 0) ? (int) $locationRaw : null;

        $status = $request->query->get('status');
        $onlyAlerts = $request->query->getBoolean('alerts', false);

        $sensors = $sensorRepository->findFiltered($query, $locationId, $status, $onlyAlerts);
        $allSensors = $sensorRepository->findAll();
        $locations = $locationRepository->findBy([], ['name' => 'ASC']);

        $stats = [
            'total' => count($allSensors),
            'online' => count(array_filter($allSensors, fn (Sensor $s) => $s->getStatus() === 'online')),
            'offline' => count(array_filter($allSensors, fn (Sensor $s) => $s->getStatus() !== 'online')),
            'alerts' => count(array_filter($allSensors, fn (Sensor $s) => $s->isAlert())),
        ];

        return $this->render('sensor/index.html.twig', [
            'sensors' => $sensors,
            'locations' => $locations,
            'stats' => $stats,
            'currentQuery' => $query,
            'currentLocation' => $locationId,
            'currentStatus' => $status,
            'onlyAlerts' => $onlyAlerts,
        ]);
    }

    #[Route('/sensor/{id}', name: 'app_sensor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Sensor $sensor): Response
    {
        return $this->render('sensor/show.html.twig', [
            'sensor' => $sensor,
        ]);
    }
}