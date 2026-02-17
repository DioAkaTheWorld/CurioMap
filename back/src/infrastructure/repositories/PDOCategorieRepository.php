<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\CategorieRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Categorie;
use PDO;

class PDOCategorieRepository implements CategorieRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getCategories(?int $idUser): array {
        //il faut recup
        //les catés globales (iduser NULL = musée, monument...), éventuellement les mettre en publique de base
        //les catés de l'utilisateur connecté (si idUser fourni)
        //les catés utilisées par des points publics (même si elles appartiennent à d'autres users)

        $sql = "SELECT DISTINCT c.* FROM Categorie c 
                WHERE c.iduser IS NULL 
                OR c.id IN (SELECT DISTINCT categorie FROM PointInteret WHERE visibilite = 1)";

        if ($idUser !== null) {
            $sql .= " OR c.iduser = :iduser";
        }

        $stmt = $this->pdo->prepare($sql);
        if ($idUser !== null) {
            $stmt->bindValue(':iduser', $idUser);
        }
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categorie = new Categorie($row['libelle'], $row['iduser']);
            $categorie->setId($row['id']);
            $categories[] = $categorie;
        }
        return $categories;
    }

    public function save(Categorie $categorie): int {
        $sql = "INSERT INTO Categorie (libelle, iduser) VALUES (:libelle, :iduser) RETURNING id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':libelle', $categorie->getLibelle());
        $stmt->bindValue(':iduser', $categorie->getIdUser());

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
