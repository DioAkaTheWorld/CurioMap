<?php

namespace CurioMap\src\api\providers;

use CurioMap\src\application_core\application\ports\api\dtos\AuthDTO;
use CurioMap\src\application_core\application\ports\api\dtos\CredentialsDTO;
use CurioMap\src\application_core\application\ports\api\dtos\ProfileDTO;
use CurioMap\src\application_core\application\usecases\ServiceUtilisateur;

class JWTAuthnProvider {
    private ServiceUtilisateur $serviceUtilisateur;
    private JWTManager $jwtManager;

    public function __construct(JWTManager $jwtManager, ServiceUtilisateur $serviceUtilisateur) {
        $this->jwtManager = $jwtManager;
        $this->serviceUtilisateur = $serviceUtilisateur;
    }

    /**
     * Connecte un utilisateur et génère access + refresh token
     */
    public function signin(CredentialsDTO $credentials): array {
        $utilisateur = $this->serviceUtilisateur->login($credentials->email, $credentials->password);

        $payload = [
            'iss' => 'http://curiomap.local', // ton application
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $utilisateur->getId(),
            'data' => [
                'email' => $utilisateur->getEmail(),
                'nom'   => $utilisateur->getNom(),
                'role'  => $utilisateur->getRole()
            ]
        ];

        $accessToken = $this->jwtManager->createAccessToken($payload);
        $refreshToken = $this->jwtManager->createRefreshToken($payload);

        return [
            new AuthDTO($accessToken, $refreshToken),
            new ProfileDTO($utilisateur->getId(), $utilisateur->getNom(),$utilisateur->getEmail())
        ];
    }

    /**
     * Inscrit un nouvel utilisateur et retourne son profil
     */
    public function register(CredentialsDTO $credentials): ProfileDTO {
        $utilisateur = $this->serviceUtilisateur->register($credentials->nom, $credentials->email, $credentials->password);
        return new ProfileDTO($utilisateur->getId(), $utilisateur->getEmail());
    }
}
