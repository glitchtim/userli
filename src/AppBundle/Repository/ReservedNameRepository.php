<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ReservedName;
use Doctrine\ORM\EntityRepository;

/**
 * Class ReservedNameRepository.
 */
class ReservedNameRepository extends EntityRepository
{
    /**
     * @param string $name
     *
     * @return null|object|ReservedName
     */
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
