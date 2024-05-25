<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\Finder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    
    /**
     * See https://symfony.com/doc/current/security/user_providers.html#using-a-custom-query-to-load-the-user
     *
     * @param string $identifier
     *
     * @return UserInterface|null
     */
    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $entityManager = $this->getEntityManager();
        
        return $entityManager
            ->createQuery(
                'SELECT u
                FROM    App\Entity\User u
                WHERE   u.username = :query
                        OR u.email = :query'
            )
            ->setParameter('query', $identifier)
            ->getOneOrNullResult();
    }
    
    public function findAllQuery(Finder $finder): Query
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC');
        
        if (null !== $finder->getSearch()) {
            $qb
                ->andWhere('
                    u.email LIKE :search
                    OR u.lastName LIKE :search
                    OR u.firstName LIKE :search
                    OR CONCAT(u.lastName, \' \', u.firstName) LIKE :search
                ')
                ->setParameter('search', '%' . $finder->getSearch() . '%');
        }
        
        return $qb->getQuery();
    }
}
