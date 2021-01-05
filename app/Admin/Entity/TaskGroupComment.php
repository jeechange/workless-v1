<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskGroupComment
 */
class TaskGroupComment
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
     * @var integer
     */
    private $aid;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var integer
     */
    private $replyId;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $files;

    /**
     * @var \DateTime
     */
    private $addTime;


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
     * @return TaskGroupComment
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
     * Set aid
     *
     * @param integer $aid
     * @return TaskGroupComment
     */
    public function setAid($aid)
    {
        $this->aid = $aid;

        return $this;
    }

    /**
     * Get aid
     *
     * @return integer 
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return TaskGroupComment
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
     * Set replyId
     *
     * @param integer $replyId
     * @return TaskGroupComment
     */
    public function setReplyId($replyId)
    {
        $this->replyId = $replyId;

        return $this;
    }

    /**
     * Get replyId
     *
     * @return integer 
     */
    public function getReplyId()
    {
        return $this->replyId;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return TaskGroupComment
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
     * Set files
     *
     * @param string $files
     * @return TaskGroupComment
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return string 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return TaskGroupComment
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
}
