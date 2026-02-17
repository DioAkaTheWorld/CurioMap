<?php
namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;

class getPointsAction{
    private ServicePointInteretInterface $service;
    private \CurioMap\src\api\providers\JWTManager $jwtManager;

    public function __construct(ServicePointInteretInterface $service, \CurioMap\src\api\providers\JWTManager $jwtManager){
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response{
        $userId = null;
        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader);
            try {
                $payload = $this->jwtManager->decodeToken($token);
                $userId = $payload['user_id'] ?? null;
            } catch (\Exception $e) {
                //token invalide ou expiré, on continue en mode public (userId null)
            }
        }

        try {
            $points = $this->service->getAllPoints($userId);

            $data = array_map(function($point) {
                return [
                    'id' => $point->getId(),
                    'iduser' => $point->getIdUser(),
                    'titre' => $point->getTitre(),
                    'categorie' => $point->getCategorie(),
                    'categorieLibelle' => $point->getCategorieLibelle(),
                    'latitude' => $point->getLatitude(),
                    'longitude' => $point->getLongitude(),
                    'description' => $point->getDescription(),
                    'image' => $point->getImage(),
                    'adresse' => $point->getAdresse(),
                    'visibilite' => $point->getVisibilite(),
                    'date' => $point->getDate()->format('Y-m-d H:i:s'),
                    'dateDebut' => $point->getDateDebut() ? $point->getDateDebut()->format('Y-m-d H:i:s') : null,
                    'dateFin' => $point->getDateFin() ? $point->getDateFin()->format('Y-m-d H:i:s') : null
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
