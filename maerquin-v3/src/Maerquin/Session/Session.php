<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Session;

use Override;
use App\Application\Session\Session as FrameworkSession;
use App\Domain\Entity\User as FrameworkUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Model\Anonymous;
use SvenHK\Maerquin\Repository\UserRepository;

class Session implements FrameworkSession
{
    /**
     * @var UserRepository
     */
    private readonly EntityRepository $userRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    #[Override]
    public function setUser(FrameworkUser $user): void
    {
        $this->write('userId', $user->getId());
    }

    private function write(string $key, null | bool | int | string $value): void
    {
        $_SESSION[$key] = $value;
    }

    #[Override]
    public function getUser(): FrameworkUser
    {
        $user = $this->userRepository->findById($this->read('userId', ''));

        if ($user === null) {
            return new Anonymous();
        }

        return $user;
    }

    private function read(string $key, bool | int | string $defaultValue): bool | int | string
    {
        return $_SESSION[$key] ?? $defaultValue;
    }

    public function unsetUser(): void
    {
        $this->write('userId', null);
    }
}
