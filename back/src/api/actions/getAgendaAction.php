<?php
namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServiceEvenementInterface;

class getAgendaAction{
    private ServiceEvenementInterface $service;

    public function __construct(ServiceEvenementInterface $service){
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response{
        $queryParams = $request->getQueryParams();
        $userId = $queryParams['user_id'] ?? null;

        if (!$userId){
             $response->getBody()->write(json_encode(['error' => 'user_id param est requis']));
             return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try{
            $events = $this->service->getUserEvents((int)$userId);

            $data = array_map(function($e) {
                return [
                    'id' => $e->getId(),
                    'iduser' => $e->getIdUser(),
                    'idpoint' => $e->getIdPoint(),
                    'titre_evenement' => $e->getTitreEvenement(),
                    'date_debut' => $e->getDateDebut()->format('Y-m-d H:i:s'),
                    'date_fin' => $e->getDateFin()->format('Y-m-d H:i:s'),
                    'notes' => $e->getNotes()
                ];
            }, $events);

            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e){
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
