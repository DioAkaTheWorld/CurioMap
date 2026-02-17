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
        $sql = "INSERT INTO pointinteret (iduser, titre, image, description, categorie, date, latitude, longitude, adresse, visibilite, date_debut, date_fin) 
                VALUES (:iduser, :titre, :image, :description, :categorie, :date, :latitude, :longitude, :adresse, :visibilite, :date_debut, :date_fin)
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
            'date_debut' => $point->getDateDebut() ? $point->getDateDebut()->format('Y-m-d H:i:s') : null,
            'date_fin' => $point->getDateFin() ? $point->getDateFin()->format('Y-m-d H:i:s') : null
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function findAll(?int $userId = null): array{
        $sql = "SELECT p.*, c.libelle as categorie_libelle 
                FROM pointinteret p 
                INNER JOIN categorie c ON p.categorie = c.id
                WHERE p.visibilite = 1";
        $params = [];

        if ($userId !== null) {
            $sql .= " OR p.iduser = :userId";
            $params['userId'] = $userId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $points = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $point = new PointInteret(
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
                dateDebut: !empty($row['date_debut']) ? new DateTime($row['date_debut']) : null,
                dateFin: !empty($row['date_fin']) ? new DateTime($row['date_fin']) : null,
                id: $row['id']
            );
            if (!empty($row['categorie_libelle'])) {
                $point->setCategorieLibelle($row['categorie_libelle']);
            }
            $points[] = $point;
        }
        return $points;
    }

    public function findFavoritesByUser(int $userId): array
    {
        $sql = "SELECT p.* FROM pointinteret p
            INNER JOIN favoris f ON p.id = f.idpoint
            WHERE f.iduser = :userId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        $points = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                dateDebut: !empty($row['date_debut']) ? new DateTime($row['date_debut']) : null,
                dateFin: !empty($row['date_fin']) ? new DateTime($row['date_fin']) : null,
                id: $row['id']
            );
        }

        return $points;
    }

    public function addFavorite(int $userId, int $pointId): void
    {
        $sql = "INSERT INTO favoris (iduser, idpoint) VALUES (:userId, :pointId)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'pointId' => $pointId
        ]);
    }

    public function removeFavorite(int $userId, int $pointId): void
    {
        $sql = "DELETE FROM favoris WHERE iduser = :userId AND idpoint = :pointId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'pointId' => $pointId
        ]);
    }

}