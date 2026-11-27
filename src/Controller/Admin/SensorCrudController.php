<?php

namespace App\Controller\Admin;

use App\Entity\Sensor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SensorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sensor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Назва датчика'),
            AssociationField::new('location', 'Локація'),
            TextField::new('sensorType', 'Тип (Температура, CO2...)'),
            NumberField::new('currentValue', 'Поточне значення'),
            TextField::new('unit', 'Одиниця (°C, %, ppm)'),
            NumberField::new('alertThreshold', 'Поріг тривоги'),
            ChoiceField::new('status', 'Статус')->setChoices([
                'Online' => 'online',
                'Offline' => 'offline',
                'Maintenance' => 'maintenance',
            ]),
        ];
    }
}