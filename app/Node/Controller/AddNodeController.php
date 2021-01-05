<?php


namespace Node\Controller;

use Admin\DModel\RbacMenuDModel;
use Admin\DModel\RbacNodeDModel;
use Admin\Entity\RbacMenu;

class AddNodeController extends CommonController {

    public function index() {
        //extras[特殊]需要手动到数据库进行添加 此处仅添加公有类型
        $mods = main()->getAppList();
        if (Q()->isGet()) {
            $this->assign('mods', $mods);
            return $this->display();
        }
        $post = Q()->post->all();
        $module = $post['module'];
        $root = main()->getAppRoot() . "/" . $post['module'] . "/Controller";
        $lists = glob($root . "/*Controller.php");
        $base = $post['module'] . "\\Controller\\";
        foreach ($lists as $path) {
            $className = substr(basename($path), 0, -4);
            $refClass = new \ReflectionClass($base . $className);
            $nodeDM = new \Admin\DModel\RbacNodeDModel();
            if ($refClass->isAbstract()) continue;
            $classActions = $refClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($classActions as $action) {
                if ($action->class != $base . $className) continue;

                $classNames = substr($className, 0, -10);
                $actiones = $action->getName();
                $node = $nodeDM->findOneBy(array("module" => $module, "controller" => $classNames, "action" => $actiones));
                if (!$node) {
                    $nodes = array(
                        "module" => $module,
                        "controller" => $classNames,
                        "action" => $actiones,
                        "extras" => '',
                    );
                    $nodeEn = $nodeDM->newEntity();
                    $nodeDM->create($nodes, $nodeEn);
                    $nodeDM->add($nodeEn)->flush();
                }
            }
        }
        return $this->success("添加成功", url('node_addNode_index'));
    }

    public function menus() {
        $sid = Q()->get->get("sid") ?: 0;
        $module = Q()->get->get("module") ?: "Admin";

        $mods = main()->getAppList();
        $this->assign('mods', $mods);
        $this->assign('defaultMod', $module);
        $this->assign('defaultSid', $sid);

        $menuDM = RbacMenuDModel::getInstance();

        $this->assign("menuStr", $menuDM->getMenus($sid, 0, $module));

        $this->assign("newMenuUrl", url('node_addNode_addMenu', array("sid" => $sid, "module" => $module)));

        return $this->display();

    }

    public function addMenu() {
        $sid = Q()->get->get("sid") ?: 0;

        $module = Q()->get->get("module") ?: "Admin";
        $menuDM = RbacMenuDModel::getInstance();
        $nodeDM = RbacNodeDModel::getInstance();
        if (Q()->isGet()) {
            $mods = main()->getAppList();
            $this->assign('mods', $mods);
            $this->assign('defaultMod', $module);
            $this->assign('defaultSid', $sid);
            $pid = Q()->get->get("pid") ?: 0;
            $this->assign("options", $menuDM->getOptions($pid, $sid, $module));

            $nodes = $nodeDM->name("n")->where("n.module = '$module'")->order("n.controller", "asc")->getArray();

            $this->assign("nodes", $nodes);

            $this->assign("menus", $menuDM->menuLists($module));
            $this->assign("newMenuUrl", url('node_addNode_menu', array("sid" => $sid, "module" => $module)));
            return $this->display();
        }
        $post = Q()->post->all();

        $menu = $menuDM->newEntity();

        $menu->setNames($post["names"]);
        $menu->setSid($sid);
        $menu->setModule($module);
        $menu->setSort(0);
        $menu->setPid($post["pid"]);
        $menu->setNodeIds(join(",", $post["node"]));
        $menu->setMenuIds(join(",", $post["menu"]));
        $menu->setVisible($post["visible"] == 1 ? 1 : 0);
        $menu->setDefaultStatus($post["defaultStatus"] == 1 ? 1 : 0);
        $menu->setStatus($post["status"] == 1 ? 1 : 0);

        $menuDM->add($menu)->flush($menu);

        return $this->success("添加成功");
    }

    public function modifyMenu($id) {
        $menuDM = RbacMenuDModel::getInstance();

        /** @var RbacMenu $menu */
        $menu = $menuDM->find($id);


        if (!$menu) return $this->error("获取信息获取失败");


        $sid = $menu->getSid() ?: 0;

        $module = $menu->getModule() ?: "Admin";

        $nodeDM = RbacNodeDModel::getInstance();

        if (Q()->isGet()) {
            $mods = main()->getAppList();
            $this->assign('mods', $mods);
            $this->assign('defaultMod', $module);
            $this->assign('defaultSid', $sid);
            $pid = $menu->getPid() ?: 0;
            $this->assign("options", $menuDM->getOptions($pid, $sid, $module));

            $nodes = $nodeDM->name("n")->where("n.module = '$module'")->order("n.controller", "asc")->getArray();

            $this->assign("nodes", $nodes);

            //dump($menuDM->menuLists($module));exit;

            $this->assign("menus", $menuDM->menuLists($module));
            $this->assign("newMenuUrl", url('node_addNode_menu', array("sid" => $sid, "module" => $module)));

            $this->assign("menu", $menu);

            $this->assign("menuList", explode(",", $menu->getMenuIds()));
            $this->assign("nodeList", explode(",", $menu->getNodeIds()));


            return $this->display();
        }

        $post = Q()->post->all();

        $menu->setNames($post["names"]);
        $menu->setPid($post["pid"]);
        $menu->setNodeIds(join(",", $post["node"]));
        $menu->setMenuIds(join(",", $post["menu"]));
        $menu->setVisible($post["visible"] == 1 ? 1 : 0);
        $menu->setDefaultStatus($post["defaultStatus"] == 1 ? 1 : 0);
        $menu->setStatus($post["status"] == 1 ? 1 : 0);

        $menuDM->add($menu)->flush($menu);

        return $this->success("修改成功");
    }

    public function states($names, $status, $id) {
        $menuDM = RbacMenuDModel::getInstance();

        /** @var RbacMenu $menu */
        $menu = $menuDM->find($id);


        if (!$menu) return $this->error("获取信息获取失败");

        if (!in_array($names, array("visible", "defaultStatus", "status"))) return $this->error("非法操作");

        if (!in_array($status, array(1, 0))) return $this->error("非法操作");

        $method = "set" . ucfirst($names);

        $menu->{$method}($status);
        $menuDM->save($menu)->flush();

        return $this->redirect(url('node_addNode_menu', array("sid" => $menu->getSid(), "module" => $menu->getModule())));

    }

    public function delMenu($id) {
        $menuDM = RbacMenuDModel::getInstance();

        /** @var RbacMenu $menu */
        $menu = $menuDM->find($id);


        if (!$menu) return $this->error("获取信息获取失败");


        $sid = $menu->getSid() ?: 0;

        $module = $menu->getModule() ?: "Admin";

        $count = $menuDM->name("m")->where("m.pid=$id and m.sid=$sid and m.module = '$module'")->count();

        if ($count > 0) return $this->error("请先删除子菜单或者将子菜单移到其他菜单再进行操作");

        $menuDM->remove($menu)->flush($menu);

        return $this->redirect(url('node_addNode_menu', array("sid" => $sid, "module" => $module)));
    }

}