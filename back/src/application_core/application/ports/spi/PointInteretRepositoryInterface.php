<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\PointInteret;

interface PointInteretRepositoryInterface{
    //public function findAll(): array;

    public function save(PointInteret $point): int;
}