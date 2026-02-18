<?php

namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\spi\UtilisateurRepositoryInterface;
use CurioMap\src\api\providers\JWTManager;

class UpdatePasswordAction
{
    private UtilisateurRepositoryInterface $userRepository;
    private JWTManager $jwtManager;

    public function __construct(UtilisateurRepositoryInterface $userRepository, JWTManager $jwtManager)
    {
        $this->userRepository = $userRepository;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withStatus(401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Format du token invalide']));
            return $response->withStatus(401);
        }
        $token = $matches[1];

        try {
            $payload = $this->jwtManager->decodeToken($token);
            $userId = $payload['user_id'] ?? null;

            if (!$userId) {
                throw new \Exception("ID utilisateur introuvable dans le token");
            }

            $data = $request->getParsedBody();
            $newPassword = $data['password'] ?? null;

            if (empty($newPassword)) {
                $response->getBody()->write(json_encode(['error' => 'Mot de passe requis']));
                return $response->withStatus(400);
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userRepository->updatePassword($userId, $hashedPassword);

            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Mot de passe mis à jour']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide ou expiré', 'details' => $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}