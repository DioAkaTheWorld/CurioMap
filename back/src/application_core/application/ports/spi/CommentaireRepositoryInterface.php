<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Commentaire;

interface CommentaireRepositoryInterface {
    public function save(Commentaire $commentaire): int;
    public function findByPointId(int $pointId): array;
    public function findById(int $id): ?Commentaire;
    public function delete(int $id): bool;
    public function findByUserId(int $userId): array;
}

