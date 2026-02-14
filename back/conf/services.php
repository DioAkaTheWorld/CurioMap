<?php

use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\GetCategoriesAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\api\actions\AddCategorieAction;
use CurioMap\src\api\actions\LoginAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\actions\RegisterAction;
use CurioMap\src\application_core\application\ports\api\ServiceEvenementInterface;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use CurioMap\src\application_core\application\ports\spi\EvenementRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\UtilisateurRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\CategorieRepositoryInterface;
use CurioMap\src\application_core\application\usecases\ServiceEvenement;
use CurioMap\src\application_core\application\usecases\ServicePointInteret;
use CurioMap\src\application_core\application\usecases\ServiceUtilisateur;
use CurioMap\src\application_core\application\usecases\ServiceCategorie;
use CurioMap\src\infrastructure\repositories\PDOEvenementRepository;
use CurioMap\src\infrastructure\repositories\PDOPointRepository;
use CurioMap\src\infrastructure\repositories\PDOUtilisateurRepository;
use CurioMap\src\infrastructure\repositories\PDOCategorieRepository;
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
    EvenementRepositoryInterface::class => function(ContainerInterface $c) {
        return new PDOEvenementRepository($c->get(PDO::class));
    },
    ServiceEvenementInterface::class => function(ContainerInterface $c) {
        return new ServiceEvenement($c->get(EvenementRepositoryInterface::class));
    },
    creePointInteretAction::class => function (ContainerInterface $c) {
        return new creePointInteretAction($c->get(ServicePointInteretInterface::class));
    },
    ajouterEventAction::class => function (ContainerInterface $c) {
        return new ajouterEventAction($c->get(ServiceEvenementInterface::class));
    },
    getAgendaAction::class => function (ContainerInterface $c) {
        return new getAgendaAction($c->get(ServiceEvenementInterface::class));
    },
    getPointsAction::class => function (ContainerInterface $c) {
        return new getPointsAction($c->get(ServicePointInteretInterface::class));
    },
    modifierNotesAction::class => function (ContainerInterface $c) {
        return new modifierNotesAction($c->get(ServiceEvenementInterface::class));
    },

    UtilisateurRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOUtilisateurRepository($c->get(PDO::class));
    },
    ServiceUtilisateur::class => function (ContainerInterface $c) {
        return new ServiceUtilisateur($c->get(UtilisateurRepositoryInterface::class));
    },
    CategorieRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOCategorieRepository($c->get(PDO::class));
    },
    ServiceCategorieInterface::class => function (ContainerInterface $c) {
        return new ServiceCategorie($c->get(CategorieRepositoryInterface::class));
    },
    GetCategoriesAction::class => function (ContainerInterface $c) {
        return new GetCategoriesAction($c->get(ServiceCategorieInterface::class));
    },
    AddCategorieAction::class => function (ContainerInterface $c) {
        return new AddCategorieAction($c->get(ServiceCategorieInterface::class));
    },
    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(ServiceUtilisateur::class));
    },
    LoginAction::class => function (ContainerInterface $c) {
        return new LoginAction($c->get(ServiceUtilisateur::class));
    }
];