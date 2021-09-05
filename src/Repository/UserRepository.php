<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getUserFromDiscordOAuth(string $discordID, string $discordUsername , string $email) : ?User
    {
        $user = $this->findOneBy(['email' =>$email]);
        if (!$user) {
            return null;
        }
        if ($user->getDiscordId() !== $discordID) {
            $user = $this->updateUserWithDiscord($discordID,$discordUsername,$user);
        }
        return $user;
    }

    public function updateUserWithDiscord (string $discordID, string $discordUsername , User $user) : User {
        $user->setDiscordId($discordID)
            ->setDiscordUsername($discordUsername);

            $this->_em->flush();
            return $user;
    }

    public function createUserWithDiscord(string $discordID, string $discordUsername , string $email, string $randomPassword) : User {
        $user = new User();
        $user->setDiscordId($discordID)
            ->setDiscordUsername($discordUsername)
            ->setEmail($email)
            ->setPassword($randomPassword);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
