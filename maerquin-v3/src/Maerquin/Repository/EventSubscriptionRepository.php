<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;

class EventSubscriptionRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
}
