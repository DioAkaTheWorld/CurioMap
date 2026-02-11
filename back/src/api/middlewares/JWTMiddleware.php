<?php

namespace CurioMap\back\src\api\middlewares;

use CurioMap\back\src\api\providers\JWTManager;
use CurioMap\src\application_core\application\usecases\ServiceUtilisateur;
use CurioMap\src\application_core\domain\entities\Utilisateur;

class JWTMiddleware {
    private JWTManager $jwtManager;
    private ServiceUtilisateur $serviceUtilisateur;

    public function __construct(JWTManager $jwtManager, ServiceUtilisateur $serviceUtilisateur) {
        $this->jwtManager = $jwtManager;
        $this->serviceUtilisateur = $serviceUtilisateur;
    }

    public function handle(): Utilisateur {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }

        $authHeader = $headers['Authorization'];
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid Authorization header']);
            exit;
        }

        $token = $matches[1];

        try {
            $payload = $this->jwtManager->decodeToken($token);

            $userId = $payload['sub'] ?? null;
            if (!$userId) {
                throw new \Exception("Token invalide : user_id manquant");
            }

            $utilisateur = $this->serviceUtilisateur->getById($userId); 
            if (!$utilisateur) {
                throw new \Exception("Utilisateur introuvable");
            }
            $GLOBALS['currentUser'] = $utilisateur;

            return $utilisateur;

        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token invalide ou expiré', 'message' => $e->getMessage()]);
            exit;
        }
    }
}
