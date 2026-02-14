<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use CurioMap\src\application_core\domain\entities\Categorie;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AddCategorieAction {
    private ServiceCategorieInterface $service;

    public function __construct(ServiceCategorieInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        $libelle = $data['libelle'] ?? null;
        $idUser = $data['iduser'] ?? null;

        if (!$libelle) {
            $response->getBody()->write(json_encode(['error' => 'Libellé manquant']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if (!$idUser) {
             $response->getBody()->write(json_encode(['error' => 'ID User manquant']));
             return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
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
