<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(SensorCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('IoT Telemetry Hub — Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('← На дашборд', 'fa fa-chart-line', 'app_sensor_index');
        yield MenuItem::linkTo(SensorCrudController::class, 'Датчики', 'fa fa-microchip');
        yield MenuItem::linkTo(LocationCrudController::class, 'Локації', 'fa fa-map-marker-alt');
    }
}