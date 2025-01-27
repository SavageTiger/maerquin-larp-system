<?php

namespace SvenHK\Maerquin\Session;

use App\Application\Session\Session as FrameworkSession;
use App\Domain\Entity\User as FrameworkUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Model\Anonymous;

class Session implements FrameworkSession
{
    private EntityRepository $userRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function setUser(FrameworkUser $user): void
    {
        $this->write('userId', $user->getId());
    }

    private function write(string $key, string|int|bool $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function getUser(): FrameworkUser
    {
        $user = $this->userRepository->findOneBy(['id' => $this->read('userId', false)]);

        if ($user === null) {
            return new Anonymous();
        }

        return $user;
    }

    private function read(string $key, string|int|bool $defaultValue): string|int|bool
    {
        return $_SESSION[$key] ?? $defaultValue;
    }

}
