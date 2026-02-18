<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\api\providers\JWTManager;
use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use CurioMap\src\application_core\domain\entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddCategorieAction {
    private ServiceCategorieInterface $service;
    private JWTManager $jwtManager;

    public function __construct(ServiceCategorieInterface $service, JWTManager $jwtManager) {
        $this->service = $service;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        $libelle = $data['libelle'] ?? null;

        if (!$libelle) {
            $response->getBody()->write(json_encode(['error' => 'Libellé manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            $response->getBody()->write(json_encode(['error' => 'Token manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $payload = $this->jwtManager->decodeToken($token);
            $idUser = $payload['user_id'] ?? null;
            if (!$idUser) throw new \Exception("ID utilisateur manquant dans le token");

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $categorie = new Categorie($libelle, $idUser);

        try {
            $id = $this->service->createCategorie($categorie);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'id' => $id,
                'libelle' => $libelle,
                'iduser' => $idUser
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur création categorie', 'details' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
