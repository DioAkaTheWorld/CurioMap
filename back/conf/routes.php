<?php
declare(strict_types=1);

use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\getPointsAction;

return function(\Slim\App $app):\Slim\App {
    //Route des points d'intérêt
    $app->post('/points', creePointInteretAction::class);
    $app->get('/points', getPointsAction::class);

    //Routes pour l'agenda
    $app->post('/agenda', ajouterEventAction::class);
    $app->get('/agenda', getAgendaAction::class);
    $app->post('/notes', modifierNotesAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};