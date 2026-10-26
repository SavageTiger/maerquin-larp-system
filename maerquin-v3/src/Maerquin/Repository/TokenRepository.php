<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Token;

class TokenRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function save(Token $token): void
    {
        $this->entityManager->persist($token);
        $this->entityManager->flush();
    }

    public function findByPasswordResetHash(string $hash): null | Token
    {
        return $this->entities()->findOneBy([
            'type' => Token::TYPE_RESET_PASSWORD,
            'value' => $hash,
        ]);
    }

    public function findByCookieValue(string $cookieValue): null | Token
    {
        return $this->entities()->findOneBy([
            'type' => Token::TYPE_REMEMBER_ME,
            'value' => hash('sha256', $cookieValue),
        ]);
    }

    public function delete(Token $token): void
    {
        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Token>
     */
    private function entities(): EntityRepository
    {
        return $this->entityManager->getRepository(Token::class);
    }
}
