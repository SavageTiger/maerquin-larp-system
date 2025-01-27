<?php

namespace SvenHK\Maerquin\Controller;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Player;

class PlayersController extends Action
{
    private EntityRepository $playerRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->playerRepository = $entityManager->getRepository(Player::class);
    }

    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        return $view->render(
            $this->response,
            'players.html.twig',
            ['players' => $this->playerRepository->findBy([], ['name' => 'ASC'])]
        );
    }
}
