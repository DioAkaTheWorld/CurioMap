<?php

namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeletePointAction {
    private ServicePointInteretInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServicePointInteretInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        //auth
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $decoded = $this->jwtManager->decodeToken($token);
            //vérif structure token
            $userId = $decoded['user_id'] ?? null;
            if (!$userId) {
                throw new \Exception("ID utilisateur manquant dans le token");
            }
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        //récup id point
        $pointId = (int)$args['id'];

        //supp
        try {
            $this->service->deletePoint($pointId, $userId);
            $response->getBody()->write(json_encode(['message' => 'Point supprimé avec succès']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $status = $e->getMessage() === "Unauthorized: You can only delete your own points" ? 403 : 404;
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}
