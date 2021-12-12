<?php

namespace App\Entity;

use App\Traits\CreationTimeTrait;
use App\Traits\DeleteTrait;
use App\Traits\DomainAwareTrait;
use App\Traits\IdTrait;
use App\Traits\RandomTrait;
use App\Traits\UpdatedTimeTrait;
use App\Traits\UserAwareTrait;

class Alias implements SoftDeletableInterface
{
    use IdTrait;
    use CreationTimeTrait;
    use UpdatedTimeTrait;
    use DeleteTrait;
    use DomainAwareTrait;
    use UserAwareTrait;
    use RandomTrait;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    /**
     * Alias constructor.
     */
    public function __construct()
    {
        $this->deleted = false;
        $this->random = false;
        $currentDateTime = new \DateTime();
        $this->creationTime = $currentDateTime;
        $this->updatedTime = $currentDateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    public function clearSensitiveData()
    {
        $this->user = null;
        $this->destination = null;
    }
}
