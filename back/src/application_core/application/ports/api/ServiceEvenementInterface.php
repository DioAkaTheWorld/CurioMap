<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\Evenement;

interface ServiceEvenementInterface{
    //ajouter un événement à l'agenda
    public function creeEvent(array $data): Evenement;
    public function getUserEvents(int $userId): array;
    public function modifierNotes(array $data): void;
    public function deleteEvent(int $id, int $userId): void;
}
