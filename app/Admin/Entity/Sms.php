<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sms
 */
class Sms
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
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $tel;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $sendTime;

    /**
     * @var string
     */
    private $mobileids;

    /**
     * @var integer
     */
    private $stat;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $addTime;

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
     * Set sid
     *
     * @param integer $sid
     * @return Sms
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
     * @return Sms
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
     * Set code
     *
     * @param string $code
     * @return Sms
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Sms
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Sms
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
     * Set sendTime
     *
     * @param \DateTime $sendTime
     * @return Sms
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    /**
     * Get sendTime
     *
     * @return \DateTime 
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * Set mobileids
     *
     * @param string $mobileids
     * @return Sms
     */
    public function setMobileids($mobileids)
    {
        $this->mobileids = $mobileids;

        return $this;
    }

    /**
     * Get mobileids
     *
     * @return string 
     */
    public function getMobileids()
    {
        return $this->mobileids;
    }

    /**
     * Set stat
     *
     * @param integer $stat
     * @return Sms
     */
    public function setStat($stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return integer 
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Sms
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
     * Set addTime
     *
     * @param \DateTime $addTime
     * @return Sms
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
     * Set template
     *
     * @param string $template
     * @return Sms
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
}
