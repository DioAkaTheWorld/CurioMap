<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddFavoriteAction {
    private ServicePointInteretInterface $service;

    public function __construct(ServicePointInteretInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $userId = (int)($args['userId'] ?? 0);
        $pointId = (int)($args['pointId'] ?? 0);

        if (!$userId || !$pointId) {
            $response->getBody()->write(json_encode(['error' => 'ID utilisateur ou point manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->service->addFavorite($userId, $pointId);
            $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Favori ajouté']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur ajout favori', 'details' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
