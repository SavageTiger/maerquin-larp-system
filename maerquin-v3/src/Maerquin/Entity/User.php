<?php

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User extends \App\Domain\User\User
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
}
