<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Controller\API;

use App\Application\Actions\Action;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Player;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Entity\User;
use SvenHK\Maerquin\Exception\MaerquinEntityNotFoundException;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\TokenRepository;
use SvenHK\Maerquin\Repository\UserRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class PlayerResetPasswordEmailController extends Action
{
    /**
     * @var PlayerRepository
     */
    private EntityRepository $playerRepository;

    /**
     * @var TokenRepository
     */
    private EntityRepository $tokenRepository;

    /**
     * @var UserRepository
     */
    private EntityRepository $userRepository;

    public function __construct(
        EntityManager $entityManager,
        private Mailer $mailer,
    ) {
        $this->playerRepository = $entityManager->getRepository(Player::class);
        $this->tokenRepository = $entityManager->getRepository(Token::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function action(): ResponseInterface
    {
        $view = Twig::fromRequest($this->request);

        $playerId = $this->request->getAttribute('playerId');
        $player = $this->playerRepository->getById($playerId);
        $playerAccount = $this->userRepository->findByPlayer($player->getId());

        if ($playerAccount === null) {
            throw MaerquinEntityNotFoundException::withType(User::class);
        }

        $token = Token::generateForPasswordReset(
            $this->userRepository->findByPlayer($player->getId()),
        );

        $this->tokenRepository->save($token);

        $emailContent = $view->fetch(
            'Mail/reset_password.html.twig',
            [
                'name' => $player->getName(),
                'hash' => $token->getUnhashedValue(),
            ],
        );

        $email = new Email();
        $email->subject('Maerquin wachtwoord instellen');
        $email->from('no-reply@maerquin.nl');
        $email->to($player->getEmail());
        $email->html($emailContent);

        $this->mailer->send($email);

        return new Response();
    }
}
