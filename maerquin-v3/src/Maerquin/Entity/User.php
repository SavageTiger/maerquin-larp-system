<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User extends UserModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected string $username;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $salt;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $hash;

    #[ORM\OneToOne(
        targetEntity: Player::class,
    )]
    #[ORM\JoinColumn(name: 'player_id', referencedColumnName: 'id')]
    protected Player $player;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected null | string $rememberToken;
}
