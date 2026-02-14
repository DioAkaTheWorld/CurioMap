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
        $sql = "SELECT * FROM Categorie WHERE iduser IS NULL";
        if ($idUser !== null) {
            $sql .= " OR iduser = :iduser";
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
