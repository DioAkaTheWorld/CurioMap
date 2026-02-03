<?php
declare(strict_types=1);

use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\creeAgendaAction;
use CurioMap\src\api\actions\getAgendaAction;

return function(\Slim\App $app):\Slim\App {
    //Route pour créer un point d'intérêt
    $app->post('/api/points', creePointInteretAction::class);

    //Routes pour l'agenda
    $app->post('/api/agenda', creeAgendaAction::class);
    $app->get('/api/agenda', getAgendaAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};