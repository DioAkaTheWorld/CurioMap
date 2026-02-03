<?php
declare(strict_types=1);

use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\creeAgendaAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\getPointsAction;

return function(\Slim\App $app):\Slim\App {
    //Route des points d'intérêt
    $app->post('/api/points', creePointInteretAction::class);
    $app->get('/api/points', getPointsAction::class);

    //Routes pour l'agenda
    $app->post('/api/agenda', creeAgendaAction::class);
    $app->get('/api/agenda', getAgendaAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};