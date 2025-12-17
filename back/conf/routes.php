<?php
declare(strict_types=1);

use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;

return function(\Slim\App $app):\Slim\App {
    //Route pour créer un point d'intérêt
    $app->post('/api/points', creePointInteretAction::class);

    // Route OPTIONS pour CORS preflight ?????
    $app->options('/api/points', function ($request, $response) {
        return $response;
    });

    //Middleware CORS
    $app->add(new CorsMiddleware());
    return $app;
};