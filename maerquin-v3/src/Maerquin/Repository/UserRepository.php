<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Model\User as UserModel;

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

    public function findByPlayer(string $playerId): null | User
    {
        return $this->findOneBy(['player' => $playerId]);
    }

    public function save(UserModel $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
