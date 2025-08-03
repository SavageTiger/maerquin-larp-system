<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;

class UserRepository extends EntityRepository
{
    public function findById(string $uuid): null | User
    {
        return $this->findOneBy(['id' => $uuid]);
    }

    public function findByUsername(string $username): null | User
    {
        return $this->findOneBy(['username' => $username]);
    }
}
