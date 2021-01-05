<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RbacAccess
 */
class RbacAccess
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
    private $module;

    /**
     * @var integer
     */
    private $roleId;

    /**
     * @var string
     */
    private $menuIds;


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
     * @return RbacAccess
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
     * Set module
     *
     * @param string $module
     * @return RbacAccess
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
     * Set roleId
     *
     * @param integer $roleId
     * @return RbacAccess
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * Get roleId
     *
     * @return integer 
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set menuIds
     *
     * @param string $menuIds
     * @return RbacAccess
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
}
