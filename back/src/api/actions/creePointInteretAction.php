<?php

namespace CurioMap\src\api\actions;

use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;

class creePointInteretAction {
    private ServicePointInteretInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServicePointInteretInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response{
        //recup le corps de la requête json
        $data = json_decode($request->getBody()->getContents(), true);

        //recup de l'utilisateur via le token
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

            //on force l'iduser avec celui du token
            $data['iduser'] = $userId;

        } catch (\Exception $e) {
             $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
             return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $point = $this->service->creePoint($data);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'id' => $point->getId(),
                'message' => 'Point créé avec succès'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        } catch (\InvalidArgumentException $e) {
            //erreur client (données manquantes)
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        } catch (\Exception $e) {
            //erreur serv
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
