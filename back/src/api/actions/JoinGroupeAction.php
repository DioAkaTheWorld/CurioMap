<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServiceGroupeInterface;
use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class JoinGroupeAction {
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

        $data = json_decode($request->getBody()->getContents(), true);
        if (empty($data['codeInvitation'])) {
            $response->getBody()->write(json_encode(['error' => 'Code invitation manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $groupe = $this->service->rejoindreGroupe($data['codeInvitation'], $userId);

            $response->getBody()->write(json_encode([
                'id' => $groupe->getId(),
                'nom' => $groupe->getNom(),
                'description' => $groupe->getDescription(),
                'codeInvitation' => $groupe->getCodeInvitation()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
