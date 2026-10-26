<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Session;

use App\Application\Session\Session as FrameworkSession;
use App\Domain\Entity\User as FrameworkUser;
use Override;
use SvenHK\Maerquin\Model\Anonymous;
use SvenHK\Maerquin\Repository\UserRepository;

class Session implements FrameworkSession
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
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
