<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/5/24
 * Time: 11:42
 */

namespace Node\Controller;


use Admin\DModel\RbacRoleDModel;

class RolesController extends CommonController {

    public function lists() {

        $sid = Q()->get->get("sid") ?: 0;
        $module = Q()->get->get("module") ?: "Admin";

        $mods = main()->getAppList();
        $this->assign('mods', $mods);
        $this->assign('defaultMod', $module);
        $this->assign('defaultSid', $sid);

        $rolesDM = RbacRoleDModel::getInstance();

        $lists = $rolesDM->name("r")->where("r.sid=$sid and r.module='$module'")->getArray();

        $this->assign("lists", $lists);
        $this->assign("newMenuUrl", url('node_roles_add', array("sid" => $sid, "module" => $module)));

        return $this->display();

    }

    public function states($id, $status) {

        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->findOneBy(array("sid" => 0, "module" => "Admin", "id" => $id));

        if (!$role) return $this->error("角色信息获取失败");

        $role->setStatus($status);

        $sid = $role->getSid();
        $module = $role->getModule();

        $roleDM->flush($role);
        return $this->success("修改成功", url("node_roles_lists", array("sid" => $sid, "module" => $module)));

    }

    public function delete($id) {


        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->find($id);

        if (!$role) return $this->error("角色信息获取失败");

        $sid = $role->getSid();
        $module = $role->getModule();

        $roleDM->remove($role)->flush();
        return $this->success("删除成功", url("node_roles_lists", array("sid" => $sid, "module" => $module)));

    }

    public function add() {

        $sid = Q()->get->get("sid") ?: 0;

        $module = Q()->get->get("module") ?: "Admin";

        if (Q()->isGet()) {
            $mods = main()->getAppList();
            $this->assign('mods', $mods);
            $this->assign('defaultMod', $module);
            $this->assign('defaultSid', $sid);
            $this->assign("newMenuUrl", url('node_roles_lists', array("sid" => $sid, "module" => $module)));
            return $this->display();
        }

        $post = Q()->post->all();
        $roleDM = RbacRoleDModel::getInstance();

        $old = $roleDM->findOneBy(array("sid" => $sid, "module" => $module, "names" => $post["names"]));
        if ($old) return $this->error("角色名称必须唯一");
        $old = $roleDM->findOneBy(array("sid" => $sid, "module" => $module, "roleName" => $post["roleName"]));
        if ($old) return $this->error("角色标识必须唯一");


        $role = $roleDM->newEntity();

        $roleDM->create($post, $role);

        $role->setModule($module);
        $role->setSid($module);
        $roleDM->add($role)->flush();

        return $this->success("添加成功", url('node_roles_lists', array("sid" => $sid, "module" => $module)));

    }


    public function modify($id) {

        $roleDM = RbacRoleDModel::getInstance();
        $role = $roleDM->findOneBy(array("id" => $id));

        if (!$role) return $this->error("角色信息获取失败");
        $sid = $role->getSid();
        $module = $role->getModule();

        $mods = main()->getAppList();
        $this->assign('mods', $mods);
        $this->assign('defaultMod', $module);
        $this->assign('defaultSid', $sid);
        if (Q()->isGet()) {


            $this->assign("role", $role);
            return $this->display();
        }

        $sid = $role->getSid();
        $module = $role->getModule();

        $post = Q()->post->all();

        $old = $roleDM->findOneBy(array("sid" => $sid, "module" => $module, "names" => $post["names"]));
        if ($old && $old->getId() != $id) return $this->error("角色名称必须唯一");
        $old = $roleDM->findOneBy(array("sid" => $sid, "module" => $module, "roleName" => $post["roleName"]));
        if ($old && $old->getId() != $id) return $this->error("角色标识必须唯一");


        $roleDM->create($post, $role);

        $role->setModule("Admin");
        $roleDM->add($role)->flush();

        return $this->success("修改成功", url("node_roles_lists", array("sid" => $sid, "module" => $module)));

    }

}
