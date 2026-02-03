<?php

use CurioMap\src\api\actions\creeAgendaAction;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\GetAgendaAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\application_core\application\ports\api\ServiceAgendaInterface;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\application_core\application\ports\spi\AgendaRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\application\usecases\ServiceAgenda;
use CurioMap\src\application_core\application\usecases\ServicePointInteret;
use CurioMap\src\infrastructure\repositories\PDOAgendaRepository;
use CurioMap\src\infrastructure\repositories\PDOPointRepository;
use Psr\Container\ContainerInterface;

return [
    PDO::class => function (ContainerInterface $c) {
        $dbConfig = $c->get('settings')['db'];
        $pdo = new PDO(
            $dbConfig['dsn'],
            $dbConfig['user'],
            $dbConfig['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    },

    PointInteretRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPointRepository($c->get(PDO::class));
    },
    ServicePointInteretInterface::class => function (ContainerInterface $c) {
        return new ServicePointInteret($c->get(PointInteretRepositoryInterface::class));
    },
    AgendaRepositoryInterface::class => function(ContainerInterface $c) {
        return new PDOAgendaRepository($c->get(PDO::class));
    },
    ServiceAgendaInterface::class => function(ContainerInterface $c) {
        return new ServiceAgenda($c->get(AgendaRepositoryInterface::class));
    },
    creePointInteretAction::class => function (ContainerInterface $c) {
        return new creePointInteretAction($c->get(ServicePointInteretInterface::class));
    },
    creeAgendaAction::class => function (ContainerInterface $c) {
        return new creeAgendaAction($c->get(ServiceAgendaInterface::class));
    },
    GetAgendaAction::class => function (ContainerInterface $c) {
        return new GetAgendaAction($c->get(ServiceAgendaInterface::class));
    },
    getPointsAction::class => function (ContainerInterface $c) {
        return new getPointsAction($c->get(ServicePointInteretInterface::class));
    },
];