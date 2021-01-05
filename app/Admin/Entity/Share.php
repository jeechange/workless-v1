<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Share
 */
class Share
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $content1;

    /**
     * @var string
     */
    private $content2;

    /**
     * @var string
     */
    private $content3;

    /**
     * @var integer
     */
    private $userId;

    /**
     * @var string
     */
    private $toUser;

    /**
     * @var string
     */
    private $gobackUrl;

    /**
     * @var string
     */
    private $shareUrl;

    /**
     * @var string
     */
    private $operate;

    /**
     * @var string
     */
    private $template;


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
     * Set content1
     *
     * @param string $content1
     * @return Share
     */
    public function setContent1($content1)
    {
        $this->content1 = $content1;

        return $this;
    }

    /**
     * Get content1
     *
     * @return string 
     */
    public function getContent1()
    {
        return $this->content1;
    }

    /**
     * Set content2
     *
     * @param string $content2
     * @return Share
     */
    public function setContent2($content2)
    {
        $this->content2 = $content2;

        return $this;
    }

    /**
     * Get content2
     *
     * @return string 
     */
    public function getContent2()
    {
        return $this->content2;
    }

    /**
     * Set content3
     *
     * @param string $content3
     * @return Share
     */
    public function setContent3($content3)
    {
        $this->content3 = $content3;

        return $this;
    }

    /**
     * Get content3
     *
     * @return string 
     */
    public function getContent3()
    {
        return $this->content3;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Share
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
     * Set toUser
     *
     * @param string $toUser
     * @return Share
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
     * Set gobackUrl
     *
     * @param string $gobackUrl
     * @return Share
     */
    public function setGobackUrl($gobackUrl)
    {
        $this->gobackUrl = $gobackUrl;

        return $this;
    }

    /**
     * Get gobackUrl
     *
     * @return string 
     */
    public function getGobackUrl()
    {
        return $this->gobackUrl;
    }

    /**
     * Set shareUrl
     *
     * @param string $shareUrl
     * @return Share
     */
    public function setShareUrl($shareUrl)
    {
        $this->shareUrl = $shareUrl;

        return $this;
    }

    /**
     * Get shareUrl
     *
     * @return string 
     */
    public function getShareUrl()
    {
        return $this->shareUrl;
    }

    /**
     * Set operate
     *
     * @param string $operate
     * @return Share
     */
    public function setOperate($operate)
    {
        $this->operate = $operate;

        return $this;
    }

    /**
     * Get operate
     *
     * @return string 
     */
    public function getOperate()
    {
        return $this->operate;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Share
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }
    /**
     * @var integer
     */
    private $eventId;


    /**
     * Set eventId
     *
     * @param integer $eventId
     * @return Share
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->eventId;
    }
    /**
     * @var integer
     */
    private $sid;

    /**
     * @var integer
     */
    private $acornAdded;


    /**
     * Set sid
     *
     * @param integer $sid
     * @return Share
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
     * Set acornAdded
     *
     * @param integer $acornAdded
     * @return Share
     */
    public function setAcornAdded($acornAdded)
    {
        $this->acornAdded = $acornAdded;

        return $this;
    }

    /**
     * Get acornAdded
     *
     * @return integer 
     */
    public function getAcornAdded()
    {
        return $this->acornAdded;
    }
    /**
     * @var \DateTime
     */
    private $createTime;

    /**
     * @var integer
     */
    private $status;


    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Share
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Share
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
     * @var string
     */
    private $templateName;


    /**
     * Set templateName
     *
     * @param string $templateName
     * @return Share
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Get templateName
     *
     * @return string 
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }
}
