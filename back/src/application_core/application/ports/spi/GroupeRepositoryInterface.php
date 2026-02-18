<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Groupe;

interface GroupeRepositoryInterface {
    public function save(Groupe $groupe): int;
    public function ajouterMembre(int $groupeId, int $userId): void;
    public function findByCode(string $code): ?Groupe;
    public function isMembre(int $groupeId, int $userId): bool;
    public function findAllByUser(int $userId): array;
    public function retirerMembre(int $groupeId, int $userId): void;
}
