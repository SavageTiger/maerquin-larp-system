<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Ramsey\Uuid\Uuid;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Exception\Account\EmailAlreadyInUseException;
use SvenHK\Maerquin\Exception\Account\RegistrationException;
use SvenHK\Maerquin\Exception\Request\MissingFormFieldException;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\PlayerRepository;
use SvenHK\Maerquin\Repository\UserRepository;
use function Tamtamchik\NameCase\str_name_case;

readonly class RegistrationFormHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PlayerRepository $playerRepository,
    ) {
    }

    /**
     * @throws RegistrationException
     * @throws EmailAlreadyInUseException
     * @throws MissingFormFieldException
     */
    public function handle(Request $request): UserModel
    {
        $formResolver = FormResolver::createFromRequest($request);

        $firstName = trim($formResolver->getValue('firstName', 'registration'));
        $infix = trim($formResolver->getValue('infix', 'registration', ''));
        $lastName = trim($formResolver->getValue('lastName', 'registration'));

        $email = mb_strtolower(trim($formResolver->getValue('email', 'registration')));
        $password = $formResolver->getValue('password', 'registration');
        $passwordRepeat = $formResolver->getValue('passwordRepeat', 'registration');

        $this->guardRegistration($firstName, $lastName, $email, $password, $passwordRepeat);

        $name = str_name_case(
            $this->normalizeWhitespace(
                implode(' ', [$firstName, $infix, $lastName]),
            ),
        );

        $player = PlayerModel::create(Uuid::uuid4());
        $player->updatePlayer($name, $email);
        $this->playerRepository->save($player);

        $user = UserModel::register($player, $password);
        $this->userRepository->save($user);

        return $user;
    }

    private function normalizeWhitespace(string $value): string
    {
        return (string)preg_replace('/\s+/u', ' ', trim($value));
    }

    /**
     * @throws RegistrationException
     * @throws EmailAlreadyInUseException
     */
    private function guardRegistration(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $passwordRepeat,
    ): void {
        if ($firstName === '') {
            throw RegistrationException::firstNameMissing();
        }

        if ($lastName === '') {
            throw RegistrationException::lastNameMissing();
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw RegistrationException::invalidEmail();
        }

        if (mb_strlen($password) < 8) {
            throw RegistrationException::passwordTooShort();
        }

        if ($password !== $passwordRepeat) {
            throw RegistrationException::passwordsNotMatching();
        }

        if (
            $this->userRepository->findByUsername($email) !== null ||
            $this->playerRepository->findByEmail($email) !== null
        ) {
            throw EmailAlreadyInUseException::withEmail($email);
        }
    }
}
