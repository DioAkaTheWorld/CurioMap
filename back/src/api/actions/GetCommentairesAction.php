<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServiceCommentaireInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCommentairesAction {
    private ServiceCommentaireInterface $service;

    public function __construct(ServiceCommentaireInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $pointId = (int)$args['id'];

        try {
            $commentaires = $this->service->getCommentairesByPoint($pointId);

            $formattedCommentaires = array_map(function($item) {
                return [
                    'id' => $item['commentaire']->getId(),
                    'iduser' => $item['commentaire']->getIdUser(),
                    'idpoint' => $item['commentaire']->getIdPoint(),
                    'commentaire' => $item['commentaire']->getCommentaire(),
                    'note' => $item['commentaire']->getNote(),
                    'date_creation' => $item['commentaire']->getDateCreation()->format('Y-m-d H:i:s'),
                    'nom_utilisateur' => $item['nom_utilisateur'],
                    'email_utilisateur' => $item['email_utilisateur']
                ];
            }, $commentaires);

            $response->getBody()->write(json_encode($formattedCommentaires));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur serveur']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}

