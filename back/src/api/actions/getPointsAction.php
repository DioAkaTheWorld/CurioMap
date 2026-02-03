<?php
namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;

class getPointsAction{
    private ServicePointInteretInterface $service;

    public function __construct(ServicePointInteretInterface $service){
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response{
        try {
            $points = $this->service->getAllPoints();

            $data = array_map(function($point) {
                return [
                    'id' => $point->getId(),
                    'iduser' => $point->getIdUser(),
                    'titre' => $point->getTitre(),
                    'categorie' => $point->getCategorie(),
                    'latitude' => $point->getLatitude(),
                    'longitude' => $point->getLongitude(),
                    'description' => $point->getDescription(),
                    'image' => $point->getImage(),
                    'adresse' => $point->getAdresse(),
                    'visibilite' => $point->getVisibilite(),
                    'date' => $point->getDate()->format('Y-m-d H:i:s')
                ];
            }, $points);

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e){
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        return $response;
    }
}
