<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\PointInteret;

interface ServicePointInteretInterface{
    public function getAllPoints(): array;
    public function creePoint(array $data): PointInteret;
}