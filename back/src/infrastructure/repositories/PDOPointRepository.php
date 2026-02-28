<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\PointInteretRepositoryInterface;
use CurioMap\src\application_core\domain\entities\PointInteret;
use PDO;
use PDOException;
use DateTime;

class PDOPointRepository implements PointInteretRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(PointInteret $point): int {
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

    public function findAll(?int $userId = null): array {
        $sql = "SELECT DISTINCT p.*, c.libelle as categorie_libelle 
                FROM pointinteret p 
                LEFT JOIN categorie c ON p.categorie = c.id
                WHERE p.visibilite = 1"; //points publics

        $params = [];

        if ($userId !== null) {
            //points créés par l'utilisateur
            $sql .= " OR p.iduser = :userId";

            //points partagés dans les groupes où l'utilisateur est membre
            $sql .= " OR p.id IN (
                        SELECT mg.id_point 
                        FROM MessageGroupe mg
                        JOIN GroupeUtilisateur gu ON mg.id_groupe = gu.id_groupe
                        WHERE gu.id_utilisateur = :userId2 AND mg.id_point IS NOT NULL
                      )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':userId2', $userId);

            try {
                $stmt->execute();
            } catch (PDOException $e) {
                error_log("Erreur SQL PDOPointRepository: " . $e->getMessage());
                throw $e;
            }
        } else {
             $stmt = $this->pdo->prepare($sql);
             $stmt->execute();
        }

        $points = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

    public function findFavoritesByUser(int $userId): array {
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

    public function addFavorite(int $userId, int $pointId): void {
        $sql = "INSERT INTO favoris (iduser, idpoint) VALUES (:userId, :pointId)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'pointId' => $pointId
        ]);
    }

    public function removeFavorite(int $userId, int $pointId): void {
        $sql = "DELETE FROM favoris WHERE iduser = :userId AND idpoint = :pointId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'pointId' => $pointId
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM pointinteret WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findById(int $id): ?PointInteret {
        $sql = "SELECT * FROM pointinteret WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new PointInteret(
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
}
