<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\Groupe;

interface ServiceGroupeInterface {
    public function creerGroupe(string $nom, string $description, int $idCreateur): Groupe;
}
