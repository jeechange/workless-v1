<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RbacMenu
 */
class RbacMenu
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $pid;

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
    private $module;

    /**
     * @var string
     */
    private $nodeIds;

    /**
     * @var string
     */
    private $menuIds;

    /**
     * @var integer
     */
    private $visible;

    /**
     * @var integer
     */
    private $defaultStatus;

    /**
     * @var integer
     */
    private $sort;

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
     * Set pid
     *
     * @param integer $pid
     * @return RbacMenu
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     * @return RbacMenu
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
     * @return RbacMenu
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
     * Set module
     *
     * @param string $module
     * @return RbacMenu
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string 
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set nodeIds
     *
     * @param string $nodeIds
     * @return RbacMenu
     */
    public function setNodeIds($nodeIds)
    {
        $this->nodeIds = $nodeIds;

        return $this;
    }

    /**
     * Get nodeIds
     *
     * @return string 
     */
    public function getNodeIds()
    {
        return $this->nodeIds;
    }

    /**
     * Set menuIds
     *
     * @param string $menuIds
     * @return RbacMenu
     */
    public function setMenuIds($menuIds)
    {
        $this->menuIds = $menuIds;

        return $this;
    }

    /**
     * Get menuIds
     *
     * @return string 
     */
    public function getMenuIds()
    {
        return $this->menuIds;
    }

    /**
     * Set visible
     *
     * @param integer $visible
     * @return RbacMenu
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return integer 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set defaultStatus
     *
     * @param integer $defaultStatus
     * @return RbacMenu
     */
    public function setDefaultStatus($defaultStatus)
    {
        $this->defaultStatus = $defaultStatus;

        return $this;
    }

    /**
     * Get defaultStatus
     *
     * @return integer 
     */
    public function getDefaultStatus()
    {
        return $this->defaultStatus;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return RbacMenu
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return RbacMenu
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
