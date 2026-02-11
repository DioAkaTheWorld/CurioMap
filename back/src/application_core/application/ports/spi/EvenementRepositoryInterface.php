<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Evenement;

interface EvenementRepositoryInterface{
    public function save(Evenement $agenda): int;
    /**
     * @return Evenement[]
     */
    public function findByUser(int $userId): array;
    public function findById(int $id): ?Evenement;
}
