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

    public function save(UserModel $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByValidRememberMeToken(string $rememberMeToken): null | User
    {
        $hashedToken = hash('sha256', $rememberMeToken);

        $rememberMeTokenParts = explode(':', $rememberMeToken);
        $rememberMeTokenTime = (int)$rememberMeTokenParts[0] ?? 0;

        if (time() - (60 * 60 * 24 * 30) < $rememberMeTokenTime) {
            return $this->findOneBy(['rememberToken' => $hashedToken]);
        }

        return null;
    }
}
