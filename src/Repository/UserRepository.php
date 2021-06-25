<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Common\Collections\Criteria;

/**
 * Class UserRepository.
 */
class UserRepository extends AbstractRepository
{
    /**
     * @param $email
     *
     * @return object|User|null
     */
    public function findByEmail($email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function findUsersSince(\DateTime $dateTime)
    {
        return $this->matching(Criteria::create()->where(Criteria::expr()->gte('creationTime', $dateTime)));
    }

    /**
     * @param int $days
     *
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function findInactiveUsers(int $days)
    {
        $dateTime = new \DateTime();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('deleted', 0));
        if ($days > 0) {
            $criteria = $criteria->andWhere(
                Criteria::expr()->lte('lastLoginTime', $dateTime->sub(new \DateInterval('P' . $days . 'D')))
            );
        }

        return $this->matching($criteria);
    }

    /**
     * @return User[]|array
     */
    public function findDeletedUsers()
    {
        return $this->findBy(['deleted' => true]);
    }

    /**
     * @return int
     */
    public function countUsers()
    {
        return $this->matching(Criteria::create()
            ->where(Criteria::expr()->eq('deleted', false)))->count();
    }

    /**
     * @return int
     */
    public function countDeletedUsers()
    {
        return $this->matching(Criteria::create()
            ->where(Criteria::expr()->eq('deleted', true)))->count();
    }

    /**
     * @return int
     */
    public function countUsersWithRecoveryToken()
    {
        return $this->matching(Criteria::create()
            ->where(Criteria::expr()->eq('deleted', false))
            ->andWhere(Criteria::expr()->neq('recoverySecretBox', null))
        )->count();
    }

    /**
     * @return int
     */
    public function countUsersWithMailCrypt()
    {
        return $this->matching(Criteria::create()
            ->where(Criteria::expr()->eq('deleted', false))
            ->andWhere(Criteria::expr()->eq('mailCrypt', true))
        )->count();
    }
}
