<?php
namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServiceEvenementInterface;
use CurioMap\src\api\providers\JWTManager;

class DeleteEventAction {
    private ServiceEvenementInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServiceEvenementInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = (int)$args['id'];

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $token = str_replace('Bearer ', '', $authHeader);
            $payload = $this->jwtManager->decodeToken($token);
            $userId = $payload['user_id'] ?? null;

            if (!$userId) {
                throw new \Exception("ID utilisateur manquant dans le token");
            }

            $this->service->deleteEvent($id, $userId);

            $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Événement supprimé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }
    }
}
