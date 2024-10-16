<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use SvenHK\Maerquin\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    private string $username;

    #[ORM\Column(name: 'first_name', type: 'string', length: 255, options: ['default' => ''])]
    private string $firstName = '';

    #[ORM\Column(name: 'last_name', type: 'string', length: 255, options: ['default' => ''])]
    private string $lastName = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $salt;

    #[ORM\Column(type: 'string', length: 255)]
    private string $hash;

    #[ORM\Column(type: 'integer')]
    private int $rights;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'id', nullable: true)]
    private ?Player $player = null;

    public function checkPassword(string $password): bool
    {
        $algorithm = 'sha512';
        $iterations = 1000;

        $derivedHash = hash_pbkdf2(
            $algorithm,
            $password,
            base64_decode($this->salt),
            $iterations,
            strlen(base64_decode($this->hash)),
            true
        );

        return hash_equals(base64_decode($this->hash), $derivedHash);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
