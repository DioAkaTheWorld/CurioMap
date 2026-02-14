<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\Categorie;

interface ServiceCategorieInterface {
    public function getCategories(?int $idUser): array;
    public function createCategorie(Categorie $categorie): int;
}
