<?php

namespace App\Command;

use App\Entity\Location;
use App\Entity\Sensor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-iot',
    description: 'Заповнює базу готовими IoT-локаціями та датчиками',
)]
class SeedIotCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $locationsData = [
            'server' => ['Серверна кімната №1', 'Корпус А · Поверх 2', 'Стійки з основними серверами та ДБЖ'],
            'greenhouse' => ['Розумна теплиця «Агро-2»', 'Сектор Південь', 'Автоматичний контроль мікроклімату'],
            'factory' => ['Цех автоматики Б', 'Виробнича зона', 'Контроль тиску, вібрації та енергоспоживання'],
        ];

        $locations = [];
        foreach ($locationsData as $key => [$name, $zone, $desc]) {
            $loc = new Location();
            $loc->setName($name)->setZone($zone)->setDescription($desc);
            $this->em->persist($loc);
            $locations[$key] = $loc;
        }

        $sensorsData = [
            ['DHT-22 Стійка А1', 'Температура', 34.8, '°C', 30.0, 'online', 'server'],
            ['APC Power Meter', 'Потужність', 4.2, 'кВт', 5.5, 'online', 'server'],
            ['Smoke-Optic S1', 'Задимленість', 12.0, 'ppm', 50.0, 'online', 'server'],
            ['MH-Z19B Повітря', 'CO2', 1180.0, 'ppm', 1000.0, 'online', 'greenhouse'],
            ['Soil-Moisture G2', 'Вологість ґрунту', 64.5, '%', 85.0, 'online', 'greenhouse'],
            ['Temp-Air G1', 'Температура', 24.3, '°C', 32.0, 'maintenance', 'greenhouse'],
            ['BMP-280 Магістраль', 'Тиск', 6.8, 'бар', 8.0, 'online', 'factory'],
            ['Vibro-Pro X4', 'Вібрація', 0.0, 'мм/с', 4.5, 'offline', 'factory'],
        ];

        foreach ($sensorsData as [$name, $type, $val, $unit, $threshold, $status, $locKey]) {
            $sensor = new Sensor();
            $sensor
                ->setName($name)
                ->setSensorType($type)
                ->setCurrentValue($val)
                ->setUnit($unit)
                ->setAlertThreshold($threshold)
                ->setStatus($status)
                ->setLocation($locations[$locKey]);
            $this->em->persist($sensor);
        }

        $this->em->flush();
        $io->success('Додано 3 локації та 8 IoT-датчиків (серед них є активні тривоги, offline та maintenance).');

        return Command::SUCCESS;
    }
}