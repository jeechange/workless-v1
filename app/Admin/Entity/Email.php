<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Email
 */
class Email
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
     * @var string
     */
    private $fromemail;

    /**
     * @var string
     */
    private $toemail;

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
     * @var \DateTime
     */
    private $sendTime;

    /**
     * @var string
     */
    private $sendCode;

    /**
     * @var string
     */
    private $template;

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
     * Set sid
     *
     * @param integer $sid
     * @return Email
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
     * Set fromemail
     *
     * @param string $fromemail
     * @return Email
     */
    public function setFromemail($fromemail)
    {
        $this->fromemail = $fromemail;

        return $this;
    }

    /**
     * Get fromemail
     *
     * @return string 
     */
    public function getFromemail()
    {
        return $this->fromemail;
    }

    /**
     * Set toemail
     *
     * @param string $toemail
     * @return Email
     */
    public function setToemail($toemail)
    {
        $this->toemail = $toemail;

        return $this;
    }

    /**
     * Get toemail
     *
     * @return string 
     */
    public function getToemail()
    {
        return $this->toemail;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Email
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
     * @return Email
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
     * @return Email
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
     * Set sendTime
     *
     * @param \DateTime $sendTime
     * @return Email
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
     * Set sendCode
     *
     * @param string $sendCode
     * @return Email
     */
    public function setSendCode($sendCode)
    {
        $this->sendCode = $sendCode;

        return $this;
    }

    /**
     * Get sendCode
     *
     * @return string 
     */
    public function getSendCode()
    {
        return $this->sendCode;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Email
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
     * Set status
     *
     * @param integer $status
     * @return Email
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
