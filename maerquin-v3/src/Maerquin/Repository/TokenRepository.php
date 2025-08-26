<?php

declare(strict_types=1);

namespace SvenHK\Maerquin\Repository;

use Doctrine\ORM\EntityRepository;
use SvenHK\Maerquin\Entity\Token;

class TokenRepository extends EntityRepository
{
    public function save(Token $token): void
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

    public function findByPasswordResetHash(string $hash): null | Token
    {
        return $this->findOneBy([
            'type' => Token::TYPE_RESET_PASSWORD,
            'value' => $hash,
        ]);
    }

    public function findByCookieValue(string $cookieValue): null | Token
    {
        return $this->findOneBy([
            'type' => Token::TYPE_REMEMBER_ME,
            'value' => hash('sha256', $cookieValue),
        ]);
    }

    public function delete(Token $token): void
    {
        $this->getEntityManager()->remove($token);
        $this->getEntityManager()->flush();
    }
}
