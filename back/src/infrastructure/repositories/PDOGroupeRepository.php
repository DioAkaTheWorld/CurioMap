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
        $sql = "INSERT INTO groupe (nom, description, id_createur) VALUES (:nom, :description, :idCreateur) RETURNING id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom' => $groupe->getNom(),
            'description' => $groupe->getDescription(),
            'idCreateur' => $groupe->getIdCreateur()
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function ajouterMembre(int $groupeId, int $userId): void {
        $sql = "INSERT INTO groupeutilisateur (id_groupe, id_utilisateur) VALUES (:groupeId, :userId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'groupeId' => $groupeId,
            'userId' => $userId
        ]);
    }
}
