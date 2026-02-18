<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\api\providers\JWTManager;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RemoveFavoriteAction {
    private ServicePointInteretInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServicePointInteretInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $pointId = (int)($args['pointId'] ?? 0);

        if (!$pointId) {
            $response->getBody()->write(json_encode(['error' => 'ID point manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $payload = $this->jwtManager->decodeToken($token);
            $userId = $payload['user_id'] ?? null;
            if (!$userId) throw new \Exception("ID utilisateur manquant dans le token");

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $this->service->removeFavorite($userId, $pointId);
            $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Favori supprimé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur suppression favori', 'details' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
