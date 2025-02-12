<?php

namespace SvenHK\Maerquin\Form;

use Doctrine\ORM\EntityManager;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Exception\Event\EventDateUnparsableException;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;

class EventFormHandler
{

    public function __construct(EntityManager $entityManager)
    {
    }

    /**
     * @throws MissingFormFieldException
     * @throws MaerquinEntityNotFoundException
     */
    public function handle(string $skillId, Request $request)
    {
        throw new EventDateUnparsableException('Sorry :(');
    }
}
