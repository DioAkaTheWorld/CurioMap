<?php
namespace CurioMap\src\application_core\application\usecases;

use CurioMap\src\application_core\application\ports\api\ServiceCategorieInterface;
use CurioMap\src\application_core\application\ports\spi\CategorieRepositoryInterface;
use CurioMap\src\application_core\domain\entities\Categorie;

class ServiceCategorie implements ServiceCategorieInterface {
    private CategorieRepositoryInterface $repository;

    public function __construct(CategorieRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function getCategories(?int $idUser): array {
        return $this->repository->getCategories($idUser);
    }

    public function createCategorie(Categorie $categorie): int {
        return $this->repository->save($categorie);
    }
}
