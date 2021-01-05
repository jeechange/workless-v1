<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Consoles\Controller;


use Admin\DModel\RbacAccessDModel;
use Admin\DModel\RbacMenuDModel;
use Admin\DModel\RbacRoleDModel;
use Admin\Entity\RbacRole;

/**
 * Description of RolesController
 *
 * @author river2liu <river2liu@jeechange.com>
 */
class RolesController extends CommonController {

    protected $moduleName = "Consoles";

    public function lists() {
        $roleDM = RbacRoleDModel::getInstance();

        $lists = $roleDM->name("r")->where("r.sid={$this->sid} and r.module = '{$this->moduleName}'")
            ->order("r.sort", "ASC")
            ->setPage()
            ->data_sort()->getArray();

        $this->assign("lists", $lists);


        return $this->display();
    }

    public function add() {
        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();
        $roleDM = RbacRoleDModel::getInstance();

        $old = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "names" => $post["names"]));
        if ($old) return $this->error("角色名称必须唯一");
        $old = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "roleName" => $post["roleName"]));
        if ($old) return $this->error("角色标识必须唯一");


        $role = $roleDM->newEntity();

        $roleDM->create($post, $role);

        $role->setModule($this->moduleName);
        $role->setWeight(0);
        $roleDM->add($role)->flush();

        return $this->success("添加成功", url("consoles_lists", "con=Roles"));

    }

    public function states($id, $status) {

        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "id" => $id));

        if (!$role) return $this->error("角色信息获取失败");

        $role->setStatus($status);

        $roleDM->flush($role);
        return $this->success("修改成功", url("consoles_lists", "con=Roles"));
    }


    public function modify($id) {
        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "id" => $id));

        if (!$role) return $this->error("角色信息获取失败");

        if (Q()->isGet()) {
            $this->assign("role", $role);
            return $this->display();
        }

        $post = Q()->post->all();

        $old = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "names" => $post["names"]));
        if ($old && $old->getId() != $id) return $this->error("角色名称必须唯一");
        $old = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "roleName" => $post["roleName"]));
        if ($old && $old->getId() != $id) return $this->error("角色标识必须唯一");


        $roleDM->create($post, $role);

        $role->setModule("Admin");
        $roleDM->add($role)->flush();

        return $this->success("修改成功", url("consoles_lists", "con=Roles"));
    }

    public function delete($id) {
        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "id" => $id));

        if (!$role) return $this->error("角色信息获取失败");

        if ($role->getWeight() > 0) return $this->error("内置角色不允许删除");

        $roleDM->remove($role)->flush();
        return $this->success("删除成功", url("consoles_lists", "con=Roles"));
    }

    public function accessLists() {
        $roleId = Q()->get->get("roleId") ?: 0;

        $accessDM = RbacAccessDModel::getInstance();
        $menuDM = RbacMenuDModel::getInstance();

        $roleDM = RbacRoleDModel::getInstance();

        $roles = $roleDM->getRoles($this->sid, $this->moduleName);


        $topMenus = $menuDM->name("m")->where("m.sid = 0 and m.module = '{$this->moduleName}' and m.visible = 1 and m.pid = 0")->order("m.sort", "ASC")->getArray();


        foreach ($topMenus as &$topMenu) {
            $topMenu["sub"] = $menuDM->name("m")
                ->where("m.sid = 0 and m.module = '{$this->moduleName}' and m.visible = 1 and m.pid = " . $topMenu["id"])
                ->order("m.sort", "ASC")->getArray();
        }

        $this->assign("menus", $topMenus);

        if ($roleId == 0) {
            if ($roles) {
                $access = $accessDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "roleId" => $roles[0]["id"]));
                $this->assign("accessList", $access ? explode(",", $access->getMenuIds()) : array());
            } else {
                $this->assign("accessList", array());
            }
        } else {
            $access = $accessDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "roleId" => $roleId));
            $this->assign("accessList", $access ? explode(",", $access->getMenuIds()) : array());
        }
        $this->assign("roles", $roles);
        $this->assign("roleId", $roleId);
        return $this->display();
    }

    public function accessModify() {

        $post = Q()->post->all();

        $roleDM = RbacRoleDModel::getInstance();

        $roleId = $post["roleId"];

        /** @var RbacRole $role */

        $role = $roleDM->find($roleId ?: 0);

        if (!$role) return $this->error("角色信息获取失败！");

        if ($role->getModule() != $this->moduleName || $role->getSid() != 0 || $role->getStatus() != 1) return $this->error("角色状态异常，请刷新页面重试！");

        $accessDM = RbacAccessDModel::getInstance();
        $access = $accessDM->findOneBy(array("sid" => 0, "module" => $this->moduleName, "roleId" => $roleId));

        if (!$access) {
            $access = $accessDM->newEntity();
            $access->setSid(0);
            $access->setModule("Admin");
            $access->setRoleId($roleId);
        }

        $access->setMenuIds(join(",", $post["menus"]));

        $accessDM->add($access)->flush();

        return $this->success("设置成功", url('consoles_roles_rolesAccess', array("roleId" => $roleId)));

    }

}
