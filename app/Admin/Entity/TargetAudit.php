<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TargetAudit
 */
class TargetAudit
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tId;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $status;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tId
     *
     * @param integer $tId
     * @return TargetAudit
     */
    public function setTId($tId)
    {
        $this->tId = $tId;

        return $this;
    }

    /**
     * Get tId
     *
     * @return integer 
     */
    public function getTId()
    {
        return $this->tId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TargetAudit
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return TargetAudit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var integer
     */
    private $types;


    /**
     * Set types
     *
     * @param integer $types
     * @return TargetAudit
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Get types
     *
     * @return integer 
     */
    public function getTypes()
    {
        return $this->types;
    }
}
