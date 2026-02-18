<?php

namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Utilisateur;

interface UtilisateurRepositoryInterface {
    public function findByEmail(string $email): ?Utilisateur;

    public function findById(int $id): ?Utilisateur;

    public function save(Utilisateur $user): Utilisateur;

    public function updatePassword(int $userId, string $hashedPassword): void;

}
