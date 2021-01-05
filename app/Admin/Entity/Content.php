<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 */
class Content
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
    private $issueId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $author;

    /**
     * @var integer
     */
    private $categoryId;

    /**
     * @var integer
     */
    private $types;

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
    private $fromWhere;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $auditor;

    /**
     * @var \DateTime
     */
    private $auditTime;

    /**
     * @var string
     */
    private $code;

    /**
     * @var integer
     */
    private $acorn;


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
     * @return Content
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
     * Set issueId
     *
     * @param integer $issueId
     * @return Content
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
     * Set title
     *
     * @param string $title
     * @return Content
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
     * Set author
     *
     * @param string $author
     * @return Content
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set categoryId
     *
     * @param integer $categoryId
     * @return Content
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set types
     *
     * @param integer $types
     * @return Content
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
     * Set content
     *
     * @param string $content
     * @return Content
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
     * @return Content
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
     * Set fromWhere
     *
     * @param string $fromWhere
     * @return Content
     */
    public function setFromWhere($fromWhere)
    {
        $this->fromWhere = $fromWhere;

        return $this;
    }

    /**
     * Get fromWhere
     *
     * @return string 
     */
    public function getFromWhere()
    {
        return $this->fromWhere;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Content
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
     * Set auditor
     *
     * @param string $auditor
     * @return Content
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
     * Set auditTime
     *
     * @param \DateTime $auditTime
     * @return Content
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
     * Set code
     *
     * @param string $code
     * @return Content
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
     * Set acorn
     *
     * @param integer $acorn
     * @return Content
     */
    public function setAcorn($acorn)
    {
        $this->acorn = $acorn;

        return $this;
    }

    /**
     * Get acorn
     *
     * @return integer 
     */
    public function getAcorn()
    {
        return $this->acorn;
    }
}
