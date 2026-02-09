<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\domain\entities\PointInteret;
use PDO;
use PDOException;
use DateTime;

class PDOPointRepository implements PointInteretRepositoryInterface{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function save(PointInteret $point): int{
        $sql = "INSERT INTO pointinteret (iduser, titre, image, description, categorie, date, latitude, longitude, adresse, visibilite, date_event, heure_event) 
                VALUES (:iduser, :titre, :image, :description, :categorie, :date, :latitude, :longitude, :adresse, :visibilite, :date_event, :heure_event)
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
            'date' => $point->getDate()->format('Y-m-d H:i:s'),
            'date_event' => $point->getDateEvent() ? $point->getDateEvent()->format('Y-m-d') : null,
            'heure_event' => $point->getHeureEvent()
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function findAll(): array{
        $sql = "SELECT * FROM pointinteret";
        $stmt = $this->pdo->query($sql);
        $points = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $points[] = new PointInteret(
                iduser: $row['iduser'],
                titre: $row['titre'],
                categorie: $row['categorie'],
                latitude: (float)$row['latitude'],
                longitude: (float)$row['longitude'],
                image: $row['image'],
                description: $row['description'],
                adresse: $row['adresse'],
                visibilite: $row['visibilite'],
                date: new DateTime($row['date']),
                dateEvent: !empty($row['date_event']) ? new DateTime($row['date_event']) : null,
                heureEvent: $row['heure_event'] ?? null,
                id: $row['id']
            );
        }
        return $points;
    }
}