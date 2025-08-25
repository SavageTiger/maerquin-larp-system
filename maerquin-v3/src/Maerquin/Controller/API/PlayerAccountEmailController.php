<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;

class PlayerAccountEmailController extends Action
{
    public function __construct(EntityManager $entityManager)
    {
    }

    public function action(): ResponseInterface
    {
        $playerId = $this->request->getAttribute('playerId');

        exit($playerId);
    }
}
