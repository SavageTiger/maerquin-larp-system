<?php

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;

class UserRepository extends EntityRepository
{
    public function findById(string $uuid): ?User
    {
        return $this->findOneBy(['id' => $uuid]);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }
}
