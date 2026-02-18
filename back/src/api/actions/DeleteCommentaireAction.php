<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServiceCommentaireInterface;

class DeleteCommentaireAction {
    private ServiceCommentaireInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServiceCommentaireInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $commentId = (int)($args['id'] ?? 0);

        if (!$commentId) {
            $response->getBody()->write(json_encode(['error' => 'ID commentaire manquant']));
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
            $this->service->deleteCommentaire($commentId, $userId);
            $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Commentaire supprimé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}

