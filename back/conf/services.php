<?php

use CurioMap\src\api\actions\AddFavoriteAction;
use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\CreateGroupeAction;
use CurioMap\src\api\actions\JoinGroupeAction;
use CurioMap\src\api\actions\GetUserGroupesAction;
use CurioMap\src\api\actions\LeaveGroupeAction;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\GetCategoriesAction;
use CurioMap\src\api\actions\GetCommentairesAction;
use CurioMap\src\api\actions\GetFavoritesByUserAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\api\actions\DeletePointAction;
use CurioMap\src\api\actions\AddCategorieAction;
use CurioMap\src\api\actions\LoginAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\actions\RegisterAction;
use CurioMap\src\api\actions\RemoveFavoriteAction;
use CurioMap\src\api\providers\JWTManager;
use CurioMap\src\application_core\application\ports\api\ServiceEvenementInterface;
use CurioMap\src\application_core\application\ports\api\ServiceGroupeInterface;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use CurioMap\src\application_core\application\ports\api\ServiceCommentaireInterface;
use CurioMap\src\application_core\application\ports\spi\EvenementRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\UtilisateurRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\CategorieRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\CommentaireRepositoryInterface;
use CurioMap\src\application_core\application\ports\spi\GroupeRepositoryInterface;
use CurioMap\src\application_core\application\usecases\ServiceEvenement;
use CurioMap\src\application_core\application\usecases\ServicePointInteret;
use CurioMap\src\application_core\application\usecases\ServiceUtilisateur;
use CurioMap\src\application_core\application\usecases\ServiceCategorie;
use CurioMap\src\application_core\application\usecases\ServiceCommentaire;
use CurioMap\src\application_core\application\usecases\ServiceGroupe;
use CurioMap\src\infrastructure\repositories\PDOEvenementRepository;
use CurioMap\src\infrastructure\repositories\PDOPointRepository;
use CurioMap\src\infrastructure\repositories\PDOUtilisateurRepository;
use CurioMap\src\infrastructure\repositories\PDOCategorieRepository;
use CurioMap\src\infrastructure\repositories\PDOCommentaireRepository;
use CurioMap\src\infrastructure\repositories\PDOGroupeRepository;
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
        return new creePointInteretAction($c->get(ServicePointInteretInterface::class), $c->get(JWTManager::class));
    },
    ajouterEventAction::class => function (ContainerInterface $c) {
        return new ajouterEventAction($c->get(ServiceEvenementInterface::class));
    },
    getAgendaAction::class => function (ContainerInterface $c) {
        return new getAgendaAction($c->get(ServiceEvenementInterface::class));
    },
    getPointsAction::class => function (ContainerInterface $c) {
        return new getPointsAction($c->get(ServicePointInteretInterface::class), $c->get(JWTManager::class));
    },
    DeletePointAction::class => function (ContainerInterface $c) {
        return new DeletePointAction($c->get(ServicePointInteretInterface::class), $c->get(JWTManager::class));
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
    CommentaireRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOCommentaireRepository($c->get(PDO::class));
    },
    ServiceCommentaireInterface::class => function (ContainerInterface $c) {
        return new ServiceCommentaire($c->get(CommentaireRepositoryInterface::class));
    },
    GetCategoriesAction::class => function (ContainerInterface $c) {
        return new GetCategoriesAction($c->get(ServiceCategorieInterface::class));
    },
    AddCategorieAction::class => function (ContainerInterface $c) {
        return new AddCategorieAction($c->get(ServiceCategorieInterface::class));
    },
    JWTManager::class => function (ContainerInterface $c) {
        $jwtKey = $c->get('settings')['jwt']['key'];
        return new JWTManager($jwtKey);
    },
    RegisterAction::class => function (ContainerInterface $c) {
        return new RegisterAction($c->get(ServiceUtilisateur::class), $c->get(JWTManager::class));
    },
    LoginAction::class => function (ContainerInterface $c) {
        return new LoginAction($c->get(ServiceUtilisateur::class), $c->get(JWTManager::class));
    },
    AddFavoriteAction::class => function(ContainerInterface $c) {
        return new AddFavoriteAction($c->get(ServicePointInteretInterface::class));
    },
    RemoveFavoriteAction::class => function(ContainerInterface $c) {
        return new RemoveFavoriteAction($c->get(ServicePointInteretInterface::class));
    },
    CurioMap\src\api\actions\DeleteEventAction::class => function(ContainerInterface $c) {
        return new CurioMap\src\api\actions\DeleteEventAction($c->get(ServiceEvenementInterface::class), $c->get(JWTManager::class));
    },
    GetFavoritesByUserAction::class => function(ContainerInterface $c) {
        return new GetFavoritesByUserAction($c->get(ServicePointInteretInterface::class));
    },
    GetCommentairesAction::class => function(ContainerInterface $c) {
        return new GetCommentairesAction($c->get(ServiceCommentaireInterface::class));
    },
    GroupeRepositoryInterface::class => function(ContainerInterface $c) {
        return new PDOGroupeRepository($c->get(PDO::class));
    },
    ServiceGroupeInterface::class => function(ContainerInterface $c) {
        return new ServiceGroupe($c->get(GroupeRepositoryInterface::class));
    },
    CreateGroupeAction::class => function(ContainerInterface $c) {
        return new CreateGroupeAction($c->get(ServiceGroupeInterface::class), $c->get(JWTManager::class));
    },
    JoinGroupeAction::class => function(ContainerInterface $c) {
        return new JoinGroupeAction($c->get(ServiceGroupeInterface::class), $c->get(JWTManager::class));
    },
    GetUserGroupesAction::class => function(ContainerInterface $c) {
        return new GetUserGroupesAction($c->get(ServiceGroupeInterface::class), $c->get(JWTManager::class));
    },
    LeaveGroupeAction::class => function(ContainerInterface $c) {
        return new LeaveGroupeAction($c->get(ServiceGroupeInterface::class), $c->get(JWTManager::class));
    }
];
