<?php

namespace App\Domain\User;

use App\Domain\User\User;

/**
 * Interface UserRepositoryInterface
 */
interface UserRepositoryInterface
{
    /**
     * @param User $entity
     * @param bool $flush
     * @return void
     */
    public function add(User $entity, bool $flush = false): void;

    /**
     * @param User $entity
     * @param bool $flush
     * @return void
     */
    public function remove(User $entity, bool $flush = false): void;

    /**
     * @param string $email
     * @param string $username
     * @return User|null
     */
    public function findUserByEmailOrUsername(string $email, string $username): ?User;
}

