<?php

namespace CurioMap\src\application_core\application\spi;

use CurioMap\src\application_core\domain\entities\User;

interface UserRepositoryInterface {
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function save(User $user): User;
}
