<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\Groupe;

interface ServiceGroupeInterface {
    public function creerGroupe(string $nom, string $description, int $idCreateur): Groupe;
    public function rejoindreGroupe(string $codeInvitation, int $userId): Groupe;
    public function getGroupesUtilisateur(int $userId): array;
}
