<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcornAudit
 */
class AcornAudit
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $fromUser;

    /**
     * @var string
     */
    private $toUser;

    /**
     * @var string
     */
    private $auditor;

    /**
     * @var integer
     */
    private $scId;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $acorn;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $auditTime;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var string
     */
    private $sysMemo;

    /**
     * @var string
     */
    private $codeId;


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
     * Set sid
     *
     * @param integer $sid
     * @return AcornAudit
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer 
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return AcornAudit
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
     * Set fromUser
     *
     * @param integer $fromUser
     * @return AcornAudit
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return integer 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param string $toUser
     * @return AcornAudit
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return string 
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set auditor
     *
     * @param string $auditor
     * @return AcornAudit
     */
    public function setAuditor($auditor)
    {
        $this->auditor = $auditor;

        return $this;
    }

    /**
     * Get auditor
     *
     * @return string 
     */
    public function getAuditor()
    {
        return $this->auditor;
    }

    /**
     * Set scId
     *
     * @param integer $scId
     * @return AcornAudit
     */
    public function setScId($scId)
    {
        $this->scId = $scId;

        return $this;
    }

    /**
     * Get scId
     *
     * @return integer 
     */
    public function getScId()
    {
        return $this->scId;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return AcornAudit
     */
    public function setNames($names)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get names
     *
     * @return string 
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set acorn
     *
     * @param string $acorn
     * @return AcornAudit
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return string 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return AcornAudit
     */
    public function setAddTime($addTime)
    {
        $this->addTime = $addTime;

        return $this;
    }

    /**
     * Get addTime
     *
     * @return \DateTime 
     */
    public function getAddTime()
    {
        return $this->addTime;
    }

    /**
     * Set auditTime
     *
     * @param \DateTime $auditTime
     * @return AcornAudit
     */
    public function setAuditTime($auditTime)
    {
        $this->auditTime = $auditTime;

        return $this;
    }

    /**
     * Get auditTime
     *
     * @return \DateTime 
     */
    public function getAuditTime()
    {
        return $this->auditTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return AcornAudit
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
     * Set types
     *
     * @param integer $types
     * @return AcornAudit
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

    /**
     * Set memo
     *
     * @param string $memo
     * @return AcornAudit
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set sysMemo
     *
     * @param string $sysMemo
     * @return AcornAudit
     */
    public function setSysMemo($sysMemo)
    {
        $this->sysMemo = $sysMemo;

        return $this;
    }

    /**
     * Get sysMemo
     *
     * @return string 
     */
    public function getSysMemo()
    {
        return $this->sysMemo;
    }

    /**
     * Set codeId
     *
     * @param string $codeId
     * @return AcornAudit
     */
    public function setCodeId($codeId)
    {
        $this->codeId = $codeId;

        return $this;
    }

    /**
     * Get codeId
     *
     * @return string 
     */
    public function getCodeId()
    {
        return $this->codeId;
    }
    /**
     * @var string
     */
    private $tags;


    /**
     * Set tags
     *
     * @param string $tags
     * @return AcornAudit
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }
    /**
     * @var string
     */
    private $cPerson;


    /**
     * Set cPerson
     *
     * @param string $cPerson
     * @return AcornAudit
     */
    public function setCPerson($cPerson)
    {
        $this->cPerson = $cPerson;

        return $this;
    }

    /**
     * Get cPerson
     *
     * @return string 
     */
    public function getCPerson()
    {
        return $this->cPerson;
    }
    /**
     * @var string
     */
    private $thumbs;


    /**
     * Set thumbs
     *
     * @param string $thumbs
     * @return AcornAudit
     */
    public function setThumbs($thumbs)
    {
        $this->thumbs = $thumbs;

        return $this;
    }

    /**
     * Get thumbs
     *
     * @return string 
     */
    public function getThumbs()
    {
        return $this->thumbs;
    }
    /**
     * @var string
     */
    private $explain;


    /**
     * Set explain
     *
     * @param string $explain
     * @return AcornAudit
     */
    public function setExplain($explain)
    {
        $this->explain = $explain;

        return $this;
    }

    /**
     * Get explain
     *
     * @return string 
     */
    public function getExplain()
    {
        return $this->explain;
    }
    /**
     * @var integer
     */
    private $superior;


    /**
     * Set superior
     *
     * @param integer $superior
     * @return AcornAudit
     */
    public function setSuperior($superior)
    {
        $this->superior = $superior;

        return $this;
    }

    /**
     * Get superior
     *
     * @return integer 
     */
    public function getSuperior()
    {
        return $this->superior;
    }
}
