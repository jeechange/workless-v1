<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskGroupDiscuss
 */
class TaskGroupDiscuss
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tgId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $addTime;

    /**
     * @var string
     */
    private $readIds;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $ruserId;

    /**
     * @var integer
     */
    private $types;

    /**
     * @var string
     */
    private $thumbs;


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
     * Set tgId
     *
     * @param integer $tgId
     * @return TaskGroupDiscuss
     */
    public function setTgId($tgId)
    {
        $this->tgId = $tgId;

        return $this;
    }

    /**
     * Get tgId
     *
     * @return integer 
     */
    public function getTgId()
    {
        return $this->tgId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return TaskGroupDiscuss
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return TaskGroupDiscuss
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TaskGroupDiscuss
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
     * Set readIds
     *
     * @param string $readIds
     * @return TaskGroupDiscuss
     */
    public function setReadIds($readIds)
    {
        $this->readIds = $readIds;

        return $this;
    }

    /**
     * Get readIds
     *
     * @return string 
     */
    public function getReadIds()
    {
        return $this->readIds;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TaskGroupDiscuss
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
     * Set ruserId
     *
     * @param integer $ruserId
     * @return TaskGroupDiscuss
     */
    public function setRuserId($ruserId)
    {
        $this->ruserId = $ruserId;

        return $this;
    }

    /**
     * Get ruserId
     *
     * @return integer 
     */
    public function getRuserId()
    {
        return $this->ruserId;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return TaskGroupDiscuss
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
     * Set thumbs
     *
     * @param string $thumbs
     * @return TaskGroupDiscuss
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
}
