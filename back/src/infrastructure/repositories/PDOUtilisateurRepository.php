<?php

namespace CurioMap\src\application_core\infrastructure\repositories;

use PDO;
use CurioMap\src\application_core\application\spi\UtilisateurRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Utilisateur;

class PDOUtilisateurRepository implements UtilisateurRepositoryInterface {
    
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, email, motdepasse, role
            FROM Utilisateur
            WHERE email = :email
        ");

        $stmt->execute(["email" => $email]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Utilisateur(
            (int)$data["id"],
            $data["nom"],
            $data["email"],
            $data["motdepasse"],
            (int)$data["role"]
        );
    }

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, email, motdepasse, role
            FROM Utilisateur
            WHERE id = :id
        ");

        $stmt->execute(["id" => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Utilisateur(
            (int)$data["id"],
            $data["nom"],
            $data["email"],
            $data["motdepasse"],
            (int)$data["role"]
        );
    }

    public function save(Utilisateur $user): Utilisateur
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO Utilisateur (nom, email, motdepasse, role)
            VALUES (:nom, :email, :motdepasse, :role)
        ");

        $stmt->execute([
            "nom" => $user->getNom(),
            "email" => $user->getEmail(),
            "motdepasse" => $user->getMotdepasse(),
            "role" => $user->getRole()
        ]);

        $id = (int)$this->pdo->lastInsertId();

        return new Utilisateur(
            $id,
            $user->getNom(),
            $user->getEmail(),
            $user->getMotdepasse(),
            $user->getRole()
        );
    }
}
