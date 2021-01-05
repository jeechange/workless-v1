<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskDynamic
 */
class TaskDynamic
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $tid;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tid
     *
     * @param integer $tid
     * @return TaskDynamic
     */
    public function setTid($tid)
    {
        $this->tid = $tid;

        return $this;
    }

    /**
     * Get tid
     *
     * @return integer 
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return TaskDynamic
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
     * @return TaskDynamic
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
     * @return TaskDynamic
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
     * @return TaskDynamic
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
     * Set userId
     *
     * @param integer $userId
     * @return TaskDynamic
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
     * @return TaskDynamic
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
     * @return TaskDynamic
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
     * @var string
     */
    private $thumbs;


    /**
     * Set thumbs
     *
     * @param string $thumbs
     * @return TaskDynamic
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
