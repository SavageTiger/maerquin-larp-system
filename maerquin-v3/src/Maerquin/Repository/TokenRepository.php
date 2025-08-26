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

    public function findByHash(string $hash): null | Token
    {
        return $this->findOneBy(['value' => $hash]);
    }

    public function findByCookieValue(string $cookieValue): null | Token
    {
        return $this->findOneBy(['value' => hash('sha256', $cookieValue)]);
    }
}
