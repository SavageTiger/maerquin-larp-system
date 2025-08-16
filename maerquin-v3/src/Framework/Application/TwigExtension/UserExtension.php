<?php

declare(strict_types=1);

namespace App\Application\TwigExtension;

use App\Application\Session\Session;
use App\Domain\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    public function __construct(private Session $session)
    {

    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getUser', [$this, 'getUser']),
        ];
    }

    public function getUser(): User
    {
        return $this->session->getUser();
    }
}
