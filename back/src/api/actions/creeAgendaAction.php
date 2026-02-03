<?php
namespace CurioMap\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CurioMap\src\application_core\application\ports\api\ServiceAgendaInterface;

class creeAgendaAction{
    private ServiceAgendaInterface $service;

    public function __construct(ServiceAgendaInterface $service){
        $this->service = $service;
    }

    public function __invoke(Request $request, Response $response): Response{
        $data = json_decode($request->getBody()->getContents(), true);

        try{
            $agenda = $this->service->creeEvent($data);

            $json = json_encode([
                'status' => 'success',
                'message' => 'Événement ajouté à l\'agenda',
                'data' => [
                    'id' => $agenda->getId()
                ]
            ]);

            $response->getBody()->write($json);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        }catch (\InvalidArgumentException $e){
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }catch (\Exception $e){
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
