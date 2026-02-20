<?php

namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ServiceMessageGroupe;
use CurioMap\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetMessagesGroupeAction
{
    private ServiceMessageGroupe $serviceMessageGroupe;
    private JWTManager $jwtManager;

    public function __construct(ServiceMessageGroupe $serviceMessageGroupe, JWTManager $jwtManager)
    {
        $this->serviceMessageGroupe = $serviceMessageGroupe;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            if (empty($authHeader)) {
                throw new \Exception('Token manquant');
            }

            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = $this->jwtManager->decodeToken($token);
            $userId = $decoded['user_id'];

            $idGroupe = (int)$args['id'];

            $messages = $this->serviceMessageGroupe->getMessagesByGroupe($idGroupe);

            $result = array_map(fn($msg) => $msg->toArray(), $messages);

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}

