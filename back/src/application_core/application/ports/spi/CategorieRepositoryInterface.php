<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\Categorie;

interface CategorieRepositoryInterface {
    public function getCategories(?int $idUser): array;
    public function save(Categorie $categorie): int;
}
