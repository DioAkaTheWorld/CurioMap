<?php
namespace CurioMap\src\api\middlewares;

use CurioMap\src\api\providers\JWTManager;
use CurioMap\src\application_core\infrastructure\repositories\PDOUserRepository;
use PDO;

class AuthMiddleware {
    private JWTManager $jwtManager;
    private PDOUserRepository $userRepository;

    public function __construct(JWTManager $jwtManager, PDO $pdo) {
        $this->jwtManager = $jwtManager;
        $this->userRepository = new PDOUserRepository($pdo);
    }

    public function handle(): int {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Token manquant']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $payload = $this->jwtManager->decodeToken($token);

            $userId = $payload['sub'] ?? null;
            if (!$userId) {
                throw new \Exception("Utilisateur non trouvé dans le token");
            }

            $utilisateur = $this->userRepository->findById($userId);
            if (!$utilisateur) {
                throw new \Exception("Utilisateur introuvable");
            }
            return $utilisateur;

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}
