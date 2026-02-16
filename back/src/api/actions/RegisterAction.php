<?php

namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\usecases\ServiceUtilisateur;
use CurioMap\src\api\providers\JWTManager;

class RegisterAction {
    private ServiceUtilisateur $serviceUtilisateur;
    private JWTManager $jwtManager;

    public function __construct(ServiceUtilisateur $serviceUtilisateur, JWTManager $jwtManager) {
        $this->serviceUtilisateur = $serviceUtilisateur;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        try {
            if (empty($data['email']) || empty($data['password'])) {
                throw new \InvalidArgumentException("Email et mot de passe requis");
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException("Email invalide");
            }

            if (strlen($data['password']) < 6) {
                throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 6 caractères");
            }

            //si le nom d'utilisateur n'est pas renseigné, on utilise l'email comme tel
            $nom = $data['nom'] ?? $data['email'];

            $utilisateur = $this->serviceUtilisateur->register(
                $nom,
                $data['email'],
                $data['password']
            );

            $token = $this->jwtManager->createAccessToken([
                'user_id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
            ]);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Inscription réussie',
                'token' => $token,
                'user' => [
                    'id' => $utilisateur->getId(),
                    'nom' => $utilisateur->getNom(),
                    'email' => $utilisateur->getEmail(),
                    'createdAt' => date('c')
                ]
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(409); // Conflict
        }
    }
}
