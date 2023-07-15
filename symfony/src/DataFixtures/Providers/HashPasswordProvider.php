<?php

namespace App\DataFixtures\Providers;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Provider\Base;

class HashPasswordProvider extends Base
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function getHashedPassword(string $password): string
    {
        return $this->passwordHasher->hashPassword(new User(), $password);
    }

}