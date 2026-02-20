<?php
declare(strict_types=1);

use CurioMap\src\api\actions\AddFavoriteAction;
use CurioMap\src\api\actions\ajouterEventAction;
use CurioMap\src\api\actions\AjouterCommentaireAction;
use CurioMap\src\api\actions\DeleteCommentaireAction;
use CurioMap\src\api\actions\DeleteEventAction;
use CurioMap\src\api\actions\GetCommentairesAction;
use CurioMap\src\api\actions\GetFavoritesByUserAction;
use CurioMap\src\api\actions\modifierNotesAction;
use CurioMap\src\api\actions\RemoveFavoriteAction;
use CurioMap\src\api\actions\UpdatePasswordAction;
use CurioMap\src\api\middlewares\CorsMiddleware;
use CurioMap\src\api\actions\creePointInteretAction;
use CurioMap\src\api\actions\getAgendaAction;
use CurioMap\src\api\actions\getPointsAction;
use CurioMap\src\api\actions\RegisterAction;
use CurioMap\src\api\actions\LoginAction;
use CurioMap\src\api\actions\GetCategoriesAction;
use CurioMap\src\api\actions\AddCategorieAction;
use CurioMap\src\api\actions\DeletePointAction;
use CurioMap\src\api\actions\CreateGroupeAction;
use CurioMap\src\api\actions\JoinGroupeAction;
use CurioMap\src\api\actions\GetUserGroupesAction;
use CurioMap\src\api\actions\LeaveGroupeAction;
use CurioMap\src\api\actions\GetMessagesGroupeAction;
use CurioMap\src\api\actions\AddMessageGroupeAction;
use CurioMap\src\api\actions\DeleteMessageGroupeAction;

return function(\Slim\App $app):\Slim\App {
    // Routes d'authentification
    $app->post('/auth/register', RegisterAction::class);
    $app->post('/auth/login', LoginAction::class);
    $app->post('/auth/password', UpdatePasswordAction::class);

    //Route des points d'intérêt
    $app->post('/points', creePointInteretAction::class);
    $app->get('/points', getPointsAction::class);
    $app->delete('/points/{id}', DeletePointAction::class);

    //Routes pour les favoris
    $app->get('/favorites', GetFavoritesByUserAction::class);
    $app->post('/favorites/{pointId}', AddFavoriteAction::class);
    $app->delete('/favorites/{pointId}', RemoveFavoriteAction::class);

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
    $app->post('/points/{id}/commentaires', AjouterCommentaireAction::class);
    $app->delete('/commentaires/{id}', DeleteCommentaireAction::class);

    //Routes pour les groupes
    $app->post('/groupes', CreateGroupeAction::class);
    $app->post('/groupes/join', JoinGroupeAction::class);
    $app->get('/groupes', GetUserGroupesAction::class);
    $app->post('/groupes/{id}/leave', LeaveGroupeAction::class);

    //Routes pour la messagerie des groupes
    $app->get('/groupes/{id}/messages', GetMessagesGroupeAction::class);
    $app->post('/groupes/{id}/messages', AddMessageGroupeAction::class);
    $app->delete('/messages/{idMessage}', DeleteMessageGroupeAction::class);

    //Route OPTIONS pour CORS preflight (catch-all)
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    return $app;
};
