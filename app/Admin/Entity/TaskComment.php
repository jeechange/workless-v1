<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TaskComment
 */
class TaskComment
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
     * Set tid
     *
     * @param integer $tid
     * @return TaskComment
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
     * Set aid
     *
     * @param integer $aid
     * @return TaskComment
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
     * @return TaskComment
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
     * @return TaskComment
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
     * @return TaskComment
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
     * @return TaskComment
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
     * @return TaskComment
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
