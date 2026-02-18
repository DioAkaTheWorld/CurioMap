<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\api\providers\JWTManager;
use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetFavoritesByUserAction {
    private ServicePointInteretInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServicePointInteretInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
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

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        try {
            $favorites = $this->service->getFavoritesByUser($userId);

            $data = array_map(fn($point) => [
                'id' => $point->getId(),
                'iduser' => $point->getIdUser(),
                'titre' => $point->getTitre(),
                'categorie' => $point->getCategorie(),
                'latitude' => $point->getLatitude(),
                'longitude' => $point->getLongitude(),
                'image' => $point->getImage(),
                'description' => $point->getDescription(),
                'adresse' => $point->getAdresse(),
                'visibilite' => $point->getVisibilite(),
                'date' => $point->getDate()->format('Y-m-d H:i:s'),
                'dateDebut' => $point->getDateDebut()?->format('Y-m-d'),
                'dateFin' => $point->getDateFin()?->format('Y-m-d')
            ], $favorites);

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur récupération favoris', 'details' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
