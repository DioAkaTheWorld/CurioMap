<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServiceGroupeInterface;
use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetUserGroupesAction {
    private ServiceGroupeInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServiceGroupeInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $decoded = $this->jwtManager->decodeToken($token);
            $userId = $decoded['user_id'] ?? null;
            if (!$userId) throw new \Exception("ID utilisateur manquant");
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $groupes = $this->service->getGroupesUtilisateur($userId);

            $data = array_map(function($g) {
                return [
                    'id' => $g->getId(),
                    'nom' => $g->getNom(),
                    'description' => $g->getDescription(),
                    'codeInvitation' => $g->getCodeInvitation(),
                    'idCreateur' => $g->getIdCreateur()
                ];
            }, $groupes);

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
