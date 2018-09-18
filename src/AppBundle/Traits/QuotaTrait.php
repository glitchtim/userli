<?php

namespace AppBundle\Traits;

/**
 * @author louis <louis@systemli.org>
 */
trait QuotaTrait
{
    /**
     * @var null|int
     */
    private $quota;

    /**
     * @return int|null
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * @param int|null $quota
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;
    }
}
