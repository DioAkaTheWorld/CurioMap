<?php
declare(strict_types=1);

use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\api\actions\RegisterAction;
use CurioMap\src\api\actions\LoginAction;
use CurioMap\src\api\actions\GetCategoriesAction;
use CurioMap\src\api\actions\AddCategorieAction;

return function(\Slim\App $app):\Slim\App {
    // Routes d'authentification
    $app->post('/auth/register', RegisterAction::class);
    $app->post('/auth/login', LoginAction::class);

    //Route des points d'intérêt
    $app->post('/points', creePointInteretAction::class);
    $app->get('/points', getPointsAction::class);

    //Routes pour l'agenda
    $app->post('/agenda', ajouterEventAction::class);
    $app->get('/agenda', getAgendaAction::class);
    $app->post('/notes', modifierNotesAction::class);

    //Routes pour les catégories
    $app->get('/categories', GetCategoriesAction::class);
    $app->post('/categories', AddCategorieAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};