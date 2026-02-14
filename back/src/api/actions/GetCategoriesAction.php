<?php
namespace CurioMap\src\api\actions;

use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetCategoriesAction {
    private ServiceCategorieInterface $service;

    public function __construct(ServiceCategorieInterface $service) {
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response {
        $params = $request->getQueryParams();
        $idUser = isset($params['user_id']) ? (int)$params['user_id'] : null;

        $categories = $this->service->getCategories($idUser);

        $data = [];
        foreach ($categories as $cat) {
            $data[] = [
                'id' => $cat->getId(),
                'libelle' => $cat->getLibelle(),
                'iduser' => $cat->getIdUser()
            ];
        }

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
