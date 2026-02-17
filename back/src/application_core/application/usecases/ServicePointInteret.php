<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\domain\entities\PointInteret;
use DateTime;

class ServicePointInteret implements ServicePointInteretInterface{
    private PointInteretRepositoryInterface $pointRepository;

    public function __construct(PointInteretRepositoryInterface $pointRepository){
        $this->pointRepository = $pointRepository;
    }

    public function getAllPoints(?int $userId = null): array{
        return $this->pointRepository->findAll($userId);
    }

    public function creePoint(array $data): PointInteret{
        //champs obligatoires
        if (empty($data['titre']) || empty($data['latitude']) || empty($data['longitude'])) {
            throw new \InvalidArgumentException("Titre, latitude et longitude obligatoires");
        }
        $dateDebut = !empty($data['dateDebut']) ? new DateTime($data['dateDebut']) : null;
        $dateFin = !empty($data['dateFin']) ? new DateTime($data['dateFin']) : null;

        $point = new PointInteret(
            iduser: $data['iduser'] ?? 1, //user 1 si pas précisé (devrait être géré avant)
            titre: $data['titre'],
            categorie: $data['categorie'] ?? 1,
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            image: $data['image'] ?? null,
            description: $data['description'] ?? null,
            adresse: $data['adresse'] ?? null,
            visibilite: isset($data['visibilite']) ? (int)$data['visibilite'] : 0, //0 (privé) par défaut
            dateDebut: $dateDebut,
            dateFin: $dateFin
        );

        //sauvegarde le point en base
        $id = $this->pointRepository->save($point);
        $point->setId($id);

        return $point;
    }
}