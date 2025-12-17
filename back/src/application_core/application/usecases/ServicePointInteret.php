<?php
namespace CurioMap\src\back\application_core\application\usecases;

use CurioMap\src\back\application_core\application\ports\api\ServicePointInteretInterface;
use CurioMap\src\back\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\back\application_core\domain\entities\PointInteret;

class ServicePointInteret implements ServicePointInteretInterface{
    private PointInteretRepositoryInterface $pointRepository;

    public function __construct(PointInteretRepositoryInterface $pointRepository){
        $this->pointRepository = $pointRepository;
    }

    public function getAllPoints(): array{
        //à faire après
        return [];
    }

    public function creePoint(array $data): PointInteret{
        //champs obligatoires
        if (empty($data['titre']) || empty($data['latitude']) || empty($data['longitude'])) {
            throw new \InvalidArgumentException("Titre, latitude et longitude obligatoires");
        }

        //NOTE : pour l'instant, on met des valeurs par défaut pour id_user et categorie
        //ya pas encore la gestion des users connectés
        $point = new PointInteret(
            iduser: $data['iduser'] ?? 1, //user 1 si pas précisé
            titre: $data['titre'],
            categorie: $data['categorie'] ?? 1, //catégorie 1
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            image: $data['image'] ?? null,
            description: $data['description'] ?? null,
            adresse: $data['adresse'] ?? null,
            visibilite: $data['visibilite'] ?? 1 //public par défaut
        );

        //sauvegarde le point en base
        $id = $this->pointRepository->save($point);
        $point->setId($id);

        return $point;
    }
}