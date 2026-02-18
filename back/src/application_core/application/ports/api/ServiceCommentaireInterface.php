<?php

namespace CurioMap\src\application_core\application\ports\api;
use CurioMap\src\application_core\domain\entities\Commentaire;

interface ServiceCommentaireInterface {
    public function deleteCommentaire(int $id, int $userId): bool;
    public function getCommentairesByUser(int $userId): array;
    public function getCommentairesByPoint(int $pointId): array;
    public function createCommentaire(array $data): Commentaire;
}

