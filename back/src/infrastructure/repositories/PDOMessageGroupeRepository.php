<?php

namespace CurioMap\src\infrastructure\repositories;

use CurioMap\src\application_core\application\spi\MessageGroupeRepositoryInterface;
use CurioMap\src\application_core\domain\MessageGroupe;
use DateTime;
use PDO;

class PDOMessageGroupeRepository implements MessageGroupeRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addMessage(int $idGroupe, int $idUser, string $message): MessageGroupe
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO MessageGroupe (id_groupe, iduser, message, date_creation)
            VALUES (:id_groupe, :iduser, :message, NOW())
            RETURNING id, date_creation
        ");

        $stmt->execute([
            'id_groupe' => $idGroupe,
            'iduser' => $idUser,
            'message' => $message
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return new MessageGroupe(
            idGroupe: $idGroupe,
            idUser: $idUser,
            message: $message,
            dateCreation: new DateTime($result['date_creation']),
            id: $result['id']
        );
    }

    public function getMessagesByGroupe(int $idGroupe): array
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, u.nom as nom_utilisateur
            FROM MessageGroupe m
            JOIN Utilisateur u ON m.iduser = u.id
            WHERE m.id_groupe = :id_groupe
            ORDER BY m.date_creation ASC
        ");

        $stmt->execute(['id_groupe' => $idGroupe]);

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = new MessageGroupe(
                idGroupe: (int)$row['id_groupe'],
                idUser: (int)$row['iduser'],
                message: $row['message'],
                dateCreation: new DateTime($row['date_creation']),
                id: (int)$row['id'],
                nomUtilisateur: $row['nom_utilisateur']
            );
        }

        return $messages;
    }

    public function deleteMessage(int $idMessage): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM MessageGroupe WHERE id = :id
        ");

        return $stmt->execute(['id' => $idMessage]);
    }

    public function canDeleteMessage(int $idMessage, int $idUser): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT iduser FROM MessageGroupe WHERE id = :id
        ");

        $stmt->execute(['id' => $idMessage]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && (int)$result['iduser'] === $idUser;
    }
}

