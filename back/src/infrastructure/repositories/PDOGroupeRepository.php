<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\GroupeRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Groupe;
use PDO;

class PDOGroupeRepository implements GroupeRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Groupe $groupe): int {
        $sql = "INSERT INTO groupe (nom, description, id_createur, code_invitation) 
                VALUES (:nom, :description, :idCreateur, :codeInvitation) 
                RETURNING id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom' => $groupe->getNom(),
            'description' => $groupe->getDescription(),
            'idCreateur' => $groupe->getIdCreateur(),
            'codeInvitation' => $groupe->getCodeInvitation()
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function findByCode(string $code): ?Groupe {
        $sql = "SELECT * FROM groupe WHERE code_invitation = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Groupe(
            $row['nom'],
            $row['id_createur'],
            $row['description'],
            $row['code_invitation'],
            $row['id']
        );
    }

    public function isMembre(int $groupeId, int $userId): bool {
        $sql = "SELECT 1 FROM groupeutilisateur WHERE id_groupe = :groupeId AND id_utilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['groupeId' => $groupeId, 'userId' => $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function findAllByUser(int $userId): array {
        $sql = "SELECT g.* FROM groupe g
                INNER JOIN groupeutilisateur gu ON g.id = gu.id_groupe
                WHERE gu.id_utilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['userId' => $userId]);

        $groupes = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groupes[] = new Groupe(
                $row['nom'],
                $row['id_createur'],
                $row['description'],
                $row['code_invitation'],
                $row['id']
            );
        }
        return $groupes;
    }

    public function ajouterMembre(int $groupeId, int $userId): void {
        $sql = "INSERT INTO groupeutilisateur (id_groupe, id_utilisateur) VALUES (:groupeId, :userId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'groupeId' => $groupeId,
            'userId' => $userId
        ]);
    }

    public function retirerMembre(int $groupeId, int $userId): void {
        $sql = "DELETE FROM groupeutilisateur WHERE id_groupe = :groupeId AND id_utilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'groupeId' => $groupeId,
            'userId' => $userId
        ]);
    }

    public function findById(int $id): ?Groupe {
        $sql = "SELECT * FROM groupe WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Groupe(
            $row['nom'],
            $row['id_createur'],
            $row['description'],
            $row['code_invitation'],
            $row['id']
        );
    }

    public function delete(int $id): void {
        $sql = "DELETE FROM groupe WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
