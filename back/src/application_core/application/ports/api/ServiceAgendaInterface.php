<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\Agenda;

interface ServiceAgendaInterface{
    //ajouter un événement à l'agenda
    public function creeEvent(array $data): Agenda;
    public function getUserEvents(int $userId): array;
}
