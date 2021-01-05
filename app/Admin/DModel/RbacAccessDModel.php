<?php

namespace Admin\DModel;

use Admin\Entity\RbacAccess;
use Admin\Entity\RbacRole;
use Node\Service\IMenuService;
use phpex\Access\Access;
use phpex\DModel\DModel;

class RbacAccessDModel extends DModel {

    public static $access_session_name = "access";

    /**
     * 自动填充规则
     */
    public function _fill() {
        //$this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\RbacAccess();
    }


    public static function accredit(Access $access, $module, $sid, $user, $roleNameStr) {
        $access->setUser($user);

        $session = Q()->getSession();

        if (!$session) return;

        $rolenames = explode(",", $roleNameStr);

        $roleDM = RbacRoleDModel::getInstance();
        $menuDM = RbacMenuDModel::getInstance();
        $nodeDM = RbacNodeDModel::getInstance();

        $self = new self();
        $nodes = $menus = array();
        foreach ($rolenames as $rolename) {
            /** @var RbacRole $role */
            $role = $roleDM->findOneBy(array("sid" => $sid, "module" => $module, "roleName" => $rolename));
            if (!$role) continue;

            /** @var RbacAccess $accessEN */

            $accessEN = $self->findOneBy(array("sid" => $sid, "module" => $module, "roleId" => $role->getId()));

            if (!$accessEN || !$accessEN->getMenuIds()) continue;
            $menuIds = $accessEN->getMenuIds();
            $menuList = $menuDM->name("m")->where("m.id in ($menuIds)")->getArray();
            if (!$menuList) continue;
            foreach ($menuList as $menu) {
                if ($menu["menuIds"]) $menus = array_merge($menus, explode(",", $menu["menuIds"]));
                if (!$menu["nodeIds"]) continue;
                $nodeIds = $menu["nodeIds"];
                $nodeLists = $nodeDM->name("n")->where("n.id in ($nodeIds)")->getArray();
                if (!$nodeLists) continue;
                foreach ($nodeLists as $node) {
                    $nodesKey = sprintf("%s.%s.%s", $node["module"], $node["controller"], $node["action"]);
                    if ($node["extras"]) $nodesKey .= "?" . $node["extras"];
                    if (isset($nodes[$nodesKey])) continue;
                    $nodes[$nodesKey] = array(
                        "name" => $node["action"],
                        "accredit" => 1,
                        "guest" => 0,
                    );
                }
            }
        }


        $access_session_name = new \ReflectionProperty(get_class($access), "access_session_name");
        $access_session_name->setAccessible(true);
        $accessInfo = array(
            "rolename" => $roleNameStr,
            "weight" => 1,
            "user" => $user,
            "nodes" => $nodes,
            "menus" => array_unique($menus)
        );
        $session->set($access_session_name->getValue($access), $accessInfo);
        $session->set("sid", $sid);
        $session->save();
    }


    public static function BuildMenu(IMenuService $menuService, $isSuper = false, $controlPath = null, $menuName = "menu.yml") {
        $lastCall = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        if (!$controlPath) {
            $controlPath = dirname(dirname($lastCall[0]["file"]));
        }

        $appName = basename($controlPath);


        $confRootPath = $controlPath . "/Conf/";

        $menus = parseFile($confRootPath . $menuName);

        foreach ($menus as $k => &$nav) {
            $nav["1"] = $menuService->_names($nav["1"]); //菜单名称转换
            $nav["5"] = $menuService->_enable($nav["5"]); //启用禁用转换

            if ($nav[3] == "non-route") $nav["url"] = $nav[2];
            else $nav["url"] = $nav[2] ? url($nav[2], $nav[3]) : "##";
            $nav["menu_id"] = $k;
        }
        $user_id = $menuService->getUser("id");

        $sid = $menuService->getUser("sid") ?: 0;
        $role_name = $menuService->getUser("roleName") ?: "role_0";


        $role = RbacRoleDModel::getInstance()->findOneBy(array("sid" => $sid, "module" => $appName, 'roleName' => $role_name));


        if (!$user_id) {
            return array(
                "roleName" => $role ? $role->getNames() : $role_name,
                "menus" => array()
            );
        }


        $access = $menuService->getAccess();

        $access_session_name = new \ReflectionProperty(get_class($access), "access_session_name");
        $access_session_name->setAccessible(true);
        $session = Q()->getSession();

        $info = $session->get($access_session_name->getValue($access));


        $rMenus = self::getMenus($menus, $isSuper, $info["menus"] ?: array());

        return array(
            "roleName" => $role ? $role->getNames() : $role_name,
            "menus" => $rMenus
        );

    }

    protected static function searchpid(array $array, $isSuper, $nodelists, $pid = 0) {
        $item = array();
        foreach ($array as $key => $val) {
            if (!$isSuper && !in_array($key, $nodelists)) {
                continue; //会员无节点权限,进入下一次循环
            }
            if ($val["5"] != 1 || $val["6"] != 1) {
                continue; //如果此节点已停用或者不显示在导航栏，进入下一次循环
            }
            if ($val[0] == $pid) {
                $item[$key] = $val;
            }
        }
        return $item;
    }

    protected static function sortarray(array $array, $sort = 4) {
        $primary_sort = array();
        $second_sort = array();
        $return = array();
        $tmp = array();
        foreach ($array as $key => $val) {
            $primary_sort[] = $val[$sort];
            $second_sort[] = $key;
            $tmp[] = $val;
        }
        array_multisort($primary_sort, $second_sort, $tmp);
        foreach ($tmp as $key => $val) {
            $return[$val["menu_id"]] = $val;
        }
        unset($primary_sort, $second_sort, $array, $tmp);
        return $return;
    }

    protected static function getMenus($navbars, $isSuper, $nodelists, $now_id = 0) {
        $finds = self::searchpid($navbars, $isSuper, $nodelists, $now_id);
        if (!$finds)
            return null;
        $finds = self::sortarray($finds);
        $item = array();
        foreach ($finds as $key => $v) {
            $item[$key]["nav"] = $v;
            $sub_nav = self::getMenus($navbars, $isSuper, $nodelists, $key);
            if ($sub_nav) {
                $item[$key]["sub_nav"] = $sub_nav;
            } else {
                $item[$key]["sub_nav"] = null;
            }
        }
        return $item;
    }

    public static function hasAccredit($node, $isSuper) {
        if ($isSuper) return true;

        $accessInfo = Q()->getSession()->has(self::$access_session_name) ? Q()->getSession()->get(self::$access_session_name) : array();
        if (!$accessInfo || !isset($accessInfo["nodes"])) return false;

        $node = str_replace(":", ".", $node);
        return isset($accessInfo["nodes"][$node]);

    }

}
