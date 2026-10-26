<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Form;

use Ramsey\Uuid\Uuid;
use Slim\Psr7\Request;
use SvenHK\Maerquin\Exception\MaerquinEmailAlreadyInUseException;
use SvenHK\Maerquin\Exception\MaerquinRegistrationException;
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
     * @throws MaerquinRegistrationException
     * @throws MaerquinEmailAlreadyInUseException
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
     * @throws MaerquinRegistrationException
     * @throws MaerquinEmailAlreadyInUseException
     */
    private function guardRegistration(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $passwordRepeat,
    ): void {
        if ($firstName === '') {
            throw MaerquinRegistrationException::firstNameMissing();
        }

        if ($lastName === '') {
            throw MaerquinRegistrationException::lastNameMissing();
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw MaerquinRegistrationException::invalidEmail();
        }

        if (mb_strlen($password) < 8) {
            throw MaerquinRegistrationException::passwordTooShort();
        }

        if ($password !== $passwordRepeat) {
            throw MaerquinRegistrationException::passwordsNotMatching();
        }

        if (
            $this->userRepository->findByUsername($email) !== null ||
            $this->playerRepository->findByEmail($email) !== null
        ) {
            throw MaerquinEmailAlreadyInUseException::withEmail($email);
        }
    }
}
