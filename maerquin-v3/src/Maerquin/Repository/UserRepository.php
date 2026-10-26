<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Model\User as UserModel;

class UserRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findById(string $uuid): null | User
    {
        return $this->entities()->findOneBy(['id' => $uuid]);
    }

    public function findByUsername(string $username): null | User
    {
        if (strlen(trim($username)) === 0) {
            return null;
        }

        return $this->entities()->findOneBy(['username' => $username]);
    }

    public function findByPlayer(string $playerId): null | User
    {
        return $this->entities()->findOneBy(['player' => $playerId]);
    }

    public function save(UserModel $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<User>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(User::class);
    }
}
