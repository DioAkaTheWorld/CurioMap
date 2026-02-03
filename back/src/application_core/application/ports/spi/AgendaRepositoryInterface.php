<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Agenda;

interface AgendaRepositoryInterface{
    public function save(Agenda $agenda): int;
    /**
     * @return Agenda[]
     */
    public function findByUser(int $userId): array;
}
