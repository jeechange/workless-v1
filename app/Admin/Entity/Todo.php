<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Todo
 */
class Todo
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $types;

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
    private $issueId;

    /**
     * @var string
     */
    private $executorsId;

    /**
     * @var integer
     */
    private $acceptId;

    /**
     * @var integer
     */
    private $issueTypes;

    /**
     * @var integer
     */
    private $codeNo;

    /**
     * @var integer
     */
    private $subCodeNo;

    /**
     * @var integer
     */
    private $groupId;

    /**
     * @var integer
     */
    private $relateId;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $tags;

    /**
     * @var string
     */
    private $resolves;

    /**
     * @var integer
     */
    private $informTypes;

    /**
     * @var string
     */
    private $inform;

    /**
     * @var \DateTime
     */
    private $informTime;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var \DateTime
     */
    private $deadline;

    /**
     * @var \DateTime
     */
    private $doneTime;

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
     * Set types
     *
     * @param integer $types
     * @return Todo
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
     * Set sid
     *
     * @param integer $sid
     * @return Todo
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
     * @return Todo
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
     * Set issueId
     *
     * @param integer $issueId
     * @return Todo
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId
     *
     * @return integer 
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set executorsId
     *
     * @param string $executorsId
     * @return Todo
     */
    public function setExecutorsId($executorsId)
    {
        $this->executorsId = $executorsId;

        return $this;
    }

    /**
     * Get executorsId
     *
     * @return string 
     */
    public function getExecutorsId()
    {
        return $this->executorsId;
    }

    /**
     * Set acceptId
     *
     * @param integer $acceptId
     * @return Todo
     */
    public function setAcceptId($acceptId)
    {
        $this->acceptId = $acceptId;

        return $this;
    }

    /**
     * Get acceptId
     *
     * @return integer 
     */
    public function getAcceptId()
    {
        return $this->acceptId;
    }

    /**
     * Set issueTypes
     *
     * @param integer $issueTypes
     * @return Todo
     */
    public function setIssueTypes($issueTypes)
    {
        $this->issueTypes = $issueTypes;

        return $this;
    }

    /**
     * Get issueTypes
     *
     * @return integer 
     */
    public function getIssueTypes()
    {
        return $this->issueTypes;
    }

    /**
     * Set codeNo
     *
     * @param integer $codeNo
     * @return Todo
     */
    public function setCodeNo($codeNo)
    {
        $this->codeNo = $codeNo;

        return $this;
    }

    /**
     * Get codeNo
     *
     * @return integer 
     */
    public function getCodeNo()
    {
        return $this->codeNo;
    }

    /**
     * Set subCodeNo
     *
     * @param integer $subCodeNo
     * @return Todo
     */
    public function setSubCodeNo($subCodeNo)
    {
        $this->subCodeNo = $subCodeNo;

        return $this;
    }

    /**
     * Get subCodeNo
     *
     * @return integer 
     */
    public function getSubCodeNo()
    {
        return $this->subCodeNo;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return Todo
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set relateId
     *
     * @param integer $relateId
     * @return Todo
     */
    public function setRelateId($relateId)
    {
        $this->relateId = $relateId;

        return $this;
    }

    /**
     * Get relateId
     *
     * @return integer 
     */
    public function getRelateId()
    {
        return $this->relateId;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Todo
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Todo
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Todo
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
     * Set resolves
     *
     * @param string $resolves
     * @return Todo
     */
    public function setResolves($resolves)
    {
        $this->resolves = $resolves;

        return $this;
    }

    /**
     * Get resolves
     *
     * @return string 
     */
    public function getResolves()
    {
        return $this->resolves;
    }

    /**
     * Set informTypes
     *
     * @param integer $informTypes
     * @return Todo
     */
    public function setInformTypes($informTypes)
    {
        $this->informTypes = $informTypes;

        return $this;
    }

    /**
     * Get informTypes
     *
     * @return integer 
     */
    public function getInformTypes()
    {
        return $this->informTypes;
    }

    /**
     * Set inform
     *
     * @param string $inform
     * @return Todo
     */
    public function setInform($inform)
    {
        $this->inform = $inform;

        return $this;
    }

    /**
     * Get inform
     *
     * @return string 
     */
    public function getInform()
    {
        return $this->inform;
    }

    /**
     * Set informTime
     *
     * @param \DateTime $informTime
     * @return Todo
     */
    public function setInformTime($informTime)
    {
        $this->informTime = $informTime;

        return $this;
    }

    /**
     * Get informTime
     *
     * @return \DateTime 
     */
    public function getInformTime()
    {
        return $this->informTime;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Todo
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
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Todo
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set doneTime
     *
     * @param \DateTime $doneTime
     * @return Todo
     */
    public function setDoneTime($doneTime)
    {
        $this->doneTime = $doneTime;

        return $this;
    }

    /**
     * Get doneTime
     *
     * @return \DateTime 
     */
    public function getDoneTime()
    {
        return $this->doneTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Todo
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
}
