<?php
namespace CurioMap\src\application_core\application\ports\api;

use CurioMap\src\application_core\domain\entities\PointInteret;

interface ServicePointInteretInterface{
    public function getAllPoints(?int $userId = null): array;
    public function creePoint(array $data): PointInteret;
    public function getFavoritesByUser(int $userId): array;
    public function addFavorite(int $userId, int $pointId): void;
    public function removeFavorite(int $userId, int $pointId): void;

}