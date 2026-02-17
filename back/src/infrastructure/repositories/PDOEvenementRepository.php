<?php
namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\ports\spi\EvenementRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Evenement;
use PDO;
use DateTime;

class PDOEvenementRepository implements EvenementRepositoryInterface{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function save(Evenement $event): int{
        $sql = "INSERT INTO evenement (iduser, idpoint, titre_evenement, date_debut, date_fin, notes) 
                VALUES (:iduser, :idpoint, :titre, :debut, :fin, :notes)
                RETURNING id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':iduser', $event->getIdUser(), PDO::PARAM_INT);
        $stmt->bindValue(':idpoint', $event->getIdPoint(), $event->getIdPoint() ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':titre', $event->getTitreEvenement(), $event->getTitreEvenement() ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':debut', $event->getDateDebut()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':fin', $event->getDateFin()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':notes', $event->getNotes(), $event->getNotes() ? PDO::PARAM_STR : PDO::PARAM_NULL);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['id'];
    }

    public function updateNotes(int $id, ?string $notes): void
    {
        $sql = "UPDATE evenement SET notes = :notes WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':notes' => $notes
        ]);
    }



    public function findByUser(int $userId): array{
        $sql = "SELECT * FROM evenement WHERE iduser = :iduser ORDER BY date_debut ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['iduser' => $userId]);

        $events = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $events[] = new Evenement(
                iduser: $row['iduser'],
                idpoint: $row['idpoint'],
                titre_evenement: $row['titre_evenement'],
                date_debut: new DateTime($row['date_debut']),
                date_fin: new DateTime($row['date_fin']),
                notes: $row['notes'],
                id: $row['id']
            );
        }
        return $events;
    }

    public function findById(int $id): ?Evenement{
        $sql = "SELECT * FROM evenement WHERE id = :id ORDER BY date_debut ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Evenement(
            iduser: $row['iduser'],
            idpoint: $row['idpoint'],
            titre_evenement: $row['titre_evenement'],
            date_debut: new DateTime($row['date_debut']),
            date_fin: new DateTime($row['date_fin']),
            notes: $row['notes'],
            id: $row['id']
        );
    }

    public function delete(int $id): void{
        $stmt = $this->pdo->prepare("DELETE FROM evenement WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
