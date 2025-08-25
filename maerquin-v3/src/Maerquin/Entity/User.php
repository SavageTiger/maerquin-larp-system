<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Player as PlayerModel;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User extends UserModel
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    protected UuidInterface $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected string $username;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $salt;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $hash;

    #[ORM\Column(options: ['default' => false])]
    protected bool $admin;

    #[ORM\OneToOne(
        targetEntity: Player::class,
    )]
    #[ORM\JoinColumn(
        name: 'player_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected PlayerModel $player;

    #[ORM\Column(options: ['default' => '1970-1-1'])]
    protected DateTimeImmutable $lastLogin;
}
