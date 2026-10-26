<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Mail\AccountMailer;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;

class PlayerResetPasswordEmailController extends Action
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly UserRepository $userRepository,
        private readonly AccountMailer $accountMailer,
    ) {
    }

    public function action(): ResponseInterface
    {
        $player = $this->playerRepository->getById($this->request->getAttribute('playerId'));
        $playerAccount = $this->userRepository->findByPlayer($player->getId());

        if ($playerAccount === null) {
            throw MaerquinEntityNotFoundException::withType(User::class);
        }

        $this->accountMailer->sendPasswordSetup(Twig::fromRequest($this->request), $playerAccount);

        return new Response();
    }
}
