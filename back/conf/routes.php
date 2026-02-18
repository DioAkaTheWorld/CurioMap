<?php
declare(strict_types=1);

use CurioMap\src\api\actions\AddFavoriteAction;
use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\DeleteEventAction;
use CurioMap\src\api\actions\GetCommentairesAction;
use CurioMap\src\api\actions\GetFavoritesByUserAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\actions\RemoveFavoriteAction;
use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\api\actions\RegisterAction;
use CurioMap\src\api\actions\LoginAction;
use CurioMap\src\api\actions\GetCategoriesAction;
use CurioMap\src\api\actions\AddCategorieAction;
use CurioMap\src\api\actions\DeletePointAction;

return function(\Slim\App $app):\Slim\App {
    // Routes d'authentification
    $app->post('/auth/register', RegisterAction::class);
    $app->post('/auth/login', LoginAction::class);

    //Route des points d'intérêt
    $app->post('/points', creePointInteretAction::class);
    $app->get('/points', getPointsAction::class);
    $app->delete('/points/{id}', DeletePointAction::class);

    //Routes pour les favoris
    $app->get('/users/{userId}/favorites', GetFavoritesByUserAction::class);
    $app->post('/users/{userId}/favorites/{pointId}', AddFavoriteAction::class);
    $app->delete('/users/{userId}/favorites/{pointId}', RemoveFavoriteAction::class);

    //Routes pour l'agenda
    $app->post('/agenda', ajouterEventAction::class);
    $app->get('/agenda', getAgendaAction::class);
    $app->delete('/agenda/{id}', DeleteEventAction::class);
    $app->post('/notes', modifierNotesAction::class);

    //Routes pour les catégories
    $app->get('/categories', GetCategoriesAction::class);
    $app->post('/categories', AddCategorieAction::class);

    //Routes pour les commentaires
    $app->get('/points/{id}/commentaires', GetCommentairesAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};