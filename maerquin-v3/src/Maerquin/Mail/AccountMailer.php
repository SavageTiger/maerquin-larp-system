<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Mail;

use Slim\Views\Twig;
use SvenHK\Maerquin\Entity\Token;
use SvenHK\Maerquin\Model\User;
use SvenHK\Maerquin\Repository\TokenRepository;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class AccountMailer
{
    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly Mailer $mailer,
    ) {
    }

    public function sendPasswordSetup(Twig $view, User $user): void
    {
        $token = Token::generateForPasswordReset($user);
        $this->tokenRepository->save($token);

        $this->send(
            $user,
            'Maerquin wachtwoord instellen',
            $view->fetch('Mail/reset_password.html.twig', [
                'name' => $user->getPlayer()->getName(),
                'hash' => $token->getUnhashedValue(),
            ]),
        );
    }

    public function sendEmailVerification(Twig $view, User $user): void
    {
        $token = Token::generateForEmailVerification($user);
        $this->tokenRepository->save($token);

        $this->send(
            $user,
            'Maerquin account bevestigen',
            $view->fetch('Mail/verify_email.html.twig', [
                'name' => $user->getPlayer()->getName(),
                'hash' => $token->getUnhashedValue(),
            ]),
        );
    }

    private function send(User $user, string $subject, string $html): void
    {
        $email = new Email();
        $email->subject($subject);
        $email->from('no-reply@maerquin.nl');
        $email->to($user->getPlayer()->getEmail());
        $email->html($html);

        $this->mailer->send($email);
    }
}
