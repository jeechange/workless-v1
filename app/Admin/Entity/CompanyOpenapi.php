<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompanyOpenapi
 */
class CompanyOpenapi
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
    private $names;

    /**
     * @var string
     */
    private $namesEn;

    /**
     * @var string
     */
    private $agentid;

    /**
     * @var string
     */
    private $corpid;

    /**
     * @var string
     */
    private $corpsecret;

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
     * @return CompanyOpenapi
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
     * Set names
     *
     * @param string $names
     * @return CompanyOpenapi
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
     * Set namesEn
     *
     * @param string $namesEn
     * @return CompanyOpenapi
     */
    public function setNamesEn($namesEn)
    {
        $this->namesEn = $namesEn;

        return $this;
    }

    /**
     * Get namesEn
     *
     * @return string 
     */
    public function getNamesEn()
    {
        return $this->namesEn;
    }

    /**
     * Set agentid
     *
     * @param string $agentid
     * @return CompanyOpenapi
     */
    public function setAgentid($agentid)
    {
        $this->agentid = $agentid;

        return $this;
    }

    /**
     * Get agentid
     *
     * @return string 
     */
    public function getAgentid()
    {
        return $this->agentid;
    }

    /**
     * Set corpid
     *
     * @param string $corpid
     * @return CompanyOpenapi
     */
    public function setCorpid($corpid)
    {
        $this->corpid = $corpid;

        return $this;
    }

    /**
     * Get corpid
     *
     * @return string 
     */
    public function getCorpid()
    {
        return $this->corpid;
    }

    /**
     * Set corpsecret
     *
     * @param string $corpsecret
     * @return CompanyOpenapi
     */
    public function setCorpsecret($corpsecret)
    {
        $this->corpsecret = $corpsecret;

        return $this;
    }

    /**
     * Get corpsecret
     *
     * @return string 
     */
    public function getCorpsecret()
    {
        return $this->corpsecret;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return CompanyOpenapi
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
