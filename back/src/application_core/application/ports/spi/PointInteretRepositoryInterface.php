<?php
namespace CurioMap\src\application_core\application\ports\spi;

use CurioMap\src\application_core\domain\entities\PointInteret;

interface PointInteretRepositoryInterface {
    public function findAll(?int $userId = null): array;
    public function save(PointInteret $point): int;
    public function addFavorite(int $userId, int $pointId): void;
    public function removeFavorite(int $userId, int $pointId): void;
    public function findFavoritesByUser(int $userId): array;
    public function delete(int $id): bool;
    public function findById(int $id): ?PointInteret;
}