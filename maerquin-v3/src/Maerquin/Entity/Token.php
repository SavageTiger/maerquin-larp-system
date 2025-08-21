<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use SvenHK\Maerquin\Model\Token as TokenModel;
use SvenHK\Maerquin\Model\User as UserModel;
use SvenHK\Maerquin\Repository\TokenRepository;

#[ORM\Table(name: '`Token`')]
#[ORM\Entity(repositoryClass: TokenRepository::class)]
class Token extends TokenModel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected UuidInterface $id;

    #[ORM\OneToOne(
        targetEntity: User::class,
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected UserModel $user;

    #[ORM\Column(type: 'string', length: 16)]
    protected string $type;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $value;

    #[ORM\Column(length: 255)]
    protected DateTimeImmutable $createdAt;
}
