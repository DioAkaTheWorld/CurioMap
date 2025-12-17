<?php
namespace CurioMap\src\back\application_core\application\ports\api;

use CurioMap\src\back\application_core\domain\entities\PointInteret;

interface ServicePointInteretInterface{
    public function getAllPoints(): array;
    public function creePoint(array $data): PointInteret;
}