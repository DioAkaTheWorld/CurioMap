<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\CommentaireRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Commentaire;
use PDO;
use DateTime;

class PDOCommentaireRepository implements CommentaireRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Commentaire $commentaire): int {
        $sql = "INSERT INTO Commentaire (iduser, idpoint, commentaire, note) 
                VALUES (:iduser, :idpoint, :commentaire, :note)
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'iduser' => $commentaire->getIdUser(),
            'idpoint' => $commentaire->getIdPoint(),
            'commentaire' => $commentaire->getCommentaire(),
            'note' => $commentaire->getNote()
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function findByPointId(int $pointId): array {
        $sql = "SELECT c.*, u.nom as nom_utilisateur, u.email as email_utilisateur 
                FROM Commentaire c
                LEFT JOIN Utilisateur u ON c.iduser = u.id
                WHERE c.idpoint = :idpoint
                ORDER BY c.date_creation DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idpoint' => $pointId]);

        $commentaires = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commentaire = new Commentaire(
                iduser: $row['iduser'],
                idpoint: $row['idpoint'],
                commentaire: $row['commentaire'],
                note: $row['note'],
                dateCreation: new DateTime($row['date_creation']),
                id: $row['id']
            );
            $commentaires[] = [
                'commentaire' => $commentaire,
                'nom_utilisateur' => $row['nom_utilisateur'] ?? null,
                'email_utilisateur' => $row['email_utilisateur'] ?? null
            ];
        }
        return $commentaires;
    }

    public function findById(int $id): ?Commentaire {
        $sql = "SELECT * FROM Commentaire WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Commentaire(
            iduser: $row['iduser'],
            idpoint: $row['idpoint'],
            commentaire: $row['commentaire'],
            note: $row['note'],
            dateCreation: new DateTime($row['date_creation']),
            id: $row['id']
        );
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM Commentaire WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findByUserId(int $userId): array {
        $sql = "SELECT c.*, p.titre as titre_point 
                FROM Commentaire c
                LEFT JOIN PointInteret p ON c.idpoint = p.id
                WHERE c.iduser = :iduser
                ORDER BY c.date_creation DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['iduser' => $userId]);

        $commentaires = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $commentaire = new Commentaire(
                iduser: $row['iduser'],
                idpoint: $row['idpoint'],
                commentaire: $row['commentaire'],
                note: $row['note'],
                dateCreation: new DateTime($row['date_creation']),
                id: $row['id']
            );
            $commentaires[] = [
                'commentaire' => $commentaire,
                'titre_point' => $row['titre_point'] ?? null
            ];
        }
        return $commentaires;
    }
}

