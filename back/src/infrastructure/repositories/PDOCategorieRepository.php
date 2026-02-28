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
        if ($idUser !== null) {
            $sql = "SELECT DISTINCT c.* FROM Categorie c 
                    WHERE c.iduser IS NULL 
                    OR c.id IN (SELECT DISTINCT categorie FROM PointInteret WHERE visibilite = 1)
                    OR c.iduser = :iduser1
                    OR c.id IN (
                        SELECT p.categorie 
                        FROM pointinteret p
                        JOIN messagegroupe mg ON mg.id_point = p.id
                        JOIN GroupeUtilisateur gu ON mg.id_groupe = gu.id_groupe
                        WHERE gu.id_utilisateur = :iduser2
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':iduser1', $idUser);
            $stmt->bindValue(':iduser2', $idUser);
        } else {
            $sql = "SELECT DISTINCT c.* FROM Categorie c 
                    WHERE c.iduser IS NULL 
                    OR c.id IN (SELECT DISTINCT categorie FROM PointInteret WHERE visibilite = 1)";
            $stmt = $this->pdo->prepare($sql);
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
