<?php

namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;

class creePointInteretAction{
    private ServicePointInteretInterface $service;

    public function __construct(ServicePointInteretInterface $service){
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response{
        //recup le corps de la requête json
        $data = json_decode($request->getBody()->getContents(), true);

        try{
            $this->service->creePoint($data);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Point créé avec succès'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);

        }catch (\InvalidArgumentException $e){
            //erreur client (données manquantes)
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }catch (\Exception $e){
            //erreur serv
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}