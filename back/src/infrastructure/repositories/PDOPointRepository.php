<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\domain\entities\PointInteret;
use PDO;
use PDOException;

class PDOPointRepository implements PointInteretRepositoryInterface{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function save(PointInteret $point): int{
        $sql = "INSERT INTO pointinteret (iduser, titre, image, description, categorie, date, latitude, longitude, adresse, visibilite) 
                VALUES (:iduser, :titre, :image, :description, :categorie, :date, :latitude, :longitude, :adresse, :visibilite)
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'iduser' => $point->getIdUser(),
            'titre' => $point->getTitre(),
            'image' => $point->getImage(),
            'description' => $point->getDescription(),
            'categorie' => $point->getCategorie(),
            'latitude' => $point->getLatitude(),
            'longitude' => $point->getLongitude(),
            'adresse' => $point->getAdresse(),
            'visibilite' => $point->getVisibilite(),
            'date' => $point->getDate()->format('Y-m-d H:i:s')
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }
}