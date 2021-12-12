<?php

namespace App\Entity;

use App\Traits\CreationTimeTrait;
use App\Traits\IdTrait;
use App\Traits\UserAwareTrait;

/**
 * Class Voucher.
 */
class Voucher
{
    use IdTrait;
    use CreationTimeTrait;
    use UserAwareTrait;
    /**
     * @var \DateTime
     */
    protected $redeemedTime = null;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var User|null
     */
    protected $invitedUser = null;

    public function __construct()
    {
        $currentDateTime = new \DateTime();
        $this->creationTime = $currentDateTime;
        $this->updatedTime = $currentDateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedeemedTime()
    {
        return $this->redeemedTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setRedeemedTime(\DateTime $redeemedTime)
    {
        $this->redeemedTime = $redeemedTime;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedeemed()
    {
        return null !== $this->getRedeemedTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return User|null
     */
    public function getInvitedUser()
    {
        return $this->invitedUser;
    }

    /**
     * @param User $invitedUser
     */
    public function setInvitedUser(User $invitedUser = null)
    {
        $this->invitedUser = $invitedUser;
    }
}
