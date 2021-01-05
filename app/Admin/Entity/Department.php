<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Department
 */
class Department
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
    private $parentid;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $director;

    /**
     * @var string
     */
    private $directors;

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
     * @return Department
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
     * Set parentid
     *
     * @param integer $parentid
     * @return Department
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * Get parentid
     *
     * @return integer 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * Set names
     *
     * @param string $names
     * @return Department
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
     * Set phone
     *
     * @param string $phone
     * @return Department
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Department
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Department
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set directors
     *
     * @param string $directors
     * @return Department
     */
    public function setDirectors($directors)
    {
        $this->directors = $directors;

        return $this;
    }

    /**
     * Get directors
     *
     * @return string 
     */
    public function getDirectors()
    {
        return $this->directors;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Department
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
     * @var integer
     */
    private $directorId;

    /**
     * @var string
     */
    private $directorsId;


    /**
     * Set directorId
     *
     * @param integer $directorId
     * @return Department
     */
    public function setDirectorId($directorId)
    {
        $this->directorId = $directorId;

        return $this;
    }

    /**
     * Get directorId
     *
     * @return integer 
     */
    public function getDirectorId()
    {
        return $this->directorId;
    }

    /**
     * Set directorsId
     *
     * @param string $directorsId
     * @return Department
     */
    public function setDirectorsId($directorsId)
    {
        $this->directorsId = $directorsId;

        return $this;
    }

    /**
     * Get directorsId
     *
     * @return string 
     */
    public function getDirectorsId()
    {
        return $this->directorsId;
    }
}
