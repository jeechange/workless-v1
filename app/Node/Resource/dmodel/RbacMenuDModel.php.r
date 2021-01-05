<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class RbacMenuDModel extends DModel {

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
        return new \Admin\Entity\RbacMenu();
    }

    public function getOptions($selectId, $supplyId, $modulename, $pid = 0, $depth = 0) {
        $where = "s.sid =:s_sid and s.pid=:s_pid and s.module=:s_modulename";
        $parame['s_pid'] = $pid;
        $parame['s_sid'] = $supplyId;
        $parame['s_modulename'] = $modulename;
        $section = $this->name('s')->where($where)->setParameter($parame)->order("s.sort", "asc")->getArray();
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= "<option value=\"{$sec["id"]}\"{$selected}>{$depthstr}{$sec["names"]}</option>";
            $options .= $this->getOptions($selectId, $supplyId, $modulename, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

    private function depth($i, $count, $depth) {
        if ($depth == 0) {
            return "";
        }
        $nbsp = '&nbsp;';
        $return = "";
        for ($j = 0; $j < $depth; $j++) {
            $return .= $nbsp;
        }
        return ($i + 1) < $count ? $return . "├" : $return . "└";
    }

    public function getMenus($sid, $pid, $module = "Admin", $loop = "&nbsp;&nbsp;") {

        $where = "m.sid =:m_sid and m.pid=:m_pid and m.module = :m_module";
        $parame['m_sid'] = $sid;
        $parame['m_pid'] = $pid;
        $parame['m_module'] = $module;
        $sub_menu = $this->name('m')->where($where)->setParameter($parame)->order("m.sort", "asc")->getArray();
        if (!$sub_menu)
            return '';
        $menu_str = '';
        $count = count($sub_menu);
        $i = 1;
        $_count = 0;
        foreach ($sub_menu as $sub) {

            $visible = ($sub["visible"] == 1) ? "可见" : "不可见";
            $default_status = ($sub["defaultStatus"] == 1) ? "启用" : "禁用";
            $status = ($sub["status"] == 1) ? "启用" : "禁用";

            $menu_str .= "<tr><td><input type=\"text\" name=\"ids[{$sub['id']}]\" class=\"sectionsort\" style=\"width:50px;margin:0;text-align:center;\" value=\"{$sub['sort']}\"></td>";
            $node = $count == $i ? "└─" : "├─";
            $menu_str .= "<td class='td-left wid-auto'>{$loop}{$node} {$sub['names']}</td>";
            $menu_str .= "<td>" . $this->buildNodes($sub['nodeIds']) . "</td>";
            $menu_str .= "<td>" . $this->buildMenus($module, $sub['menuIds']) . "</td>";
            $menu_str .= "<td>" . $visible . "</td>";
            $menu_str .= "<td>" . $default_status . "</td>";
            $menu_str .= "<td>" . $status . "</td>";
            $menu_str .= "<td>" . $this->actionsBuild($sub) . "</td>";

            $dt = $this->getMenus($sid, $sub['id'], $module, $loop . "&nbsp;&nbsp;");
            $menu_str .= $dt['menu_str'];
            $_count += $dt['count'];
            $i++;
        }
        $data['count'] = $_count;
        $data['menu_str'] = $menu_str;
        return $data;
    }

    public function buildNodes($ids) {

        if (!$ids) return "";

        $nodeDM = RbacNodeDModel::getInstance();

        $lists = $nodeDM->name("n")->where("n.id in($ids)")->order("n.controller", "asc")->order("n.action", "asc")->getArray();

        if (!$lists) return "";

        $data = "";

        foreach ($lists as $item) {
            $data .= sprintf("<span class='node-item'>%s::%s</span>", $item["controller"], $item["action"]);
        }
        return $data;
    }

    public function buildMenus($module, $menuIds) {

        $path = main()->getAppRoot() . "/$module/Conf/menu.yml";

        if (!is_file($path)) {
            return "";
        }
        $navbars = parseFile($path);

        $menuIdArr = explode(",", $menuIds);

        $menus = "";

        foreach ($menuIdArr as $id) {
            if (!isset($navbars[$id])) continue;
            $menu = "";
            $pid = $id;
            do {
                if (!isset($navbars[$pid])) break;
                $pnames = trim($navbars[$pid][1]) ?: "#";
                $menu = $pnames . "/" . $menu;
                $pid = $navbars[$pid][0];
            } while ($pid);
            $menus .= "<span class='menu-item'>" . trim($menu, "/") . "</span>";
        }
        return $menus;

    }

    public function menuLists($module) {
        $path = main()->getAppRoot() . "/$module/Conf/menu.yml";

        if (!is_file($path)) {
            return array();
        }
        $navbars = parseFile($path);

        if (!$navbars) return array();
        $lists = array();
        foreach ($navbars as $id => $nav) {
            $menu = "";
            $pid = $id;
            do {
                if (!isset($navbars[$pid])) break;
                $name = trim($navbars[$pid][1]);
                $name or $name = "#";

                $menu = $name . "/" . $menu;
                $pid = $navbars[$pid][0];
            } while ($pid);
            $subCount = 0;
            foreach ($navbars as $sid => $snav) {
                if ($snav[0] == $id) $subCount++;
            }
            $menu = trim($menu, "/") . ",";
            $lists[$id] = array(
                "id" => $id,
                "pid" => $nav[0],
                "name" => trim($menu, ","),
                "basename" => trim($nav[1]) ?: "#",
                "subCount" => $subCount,
            );


        }
        return $lists;

    }

    public function actionsBuild($menu) {

        $str = "<a data-side-form='800px' href=\"" . url("node_addNode_modifyMenu", array("id" => $menu["id"])) . "\">修改</a>";
        $actionsStr = "<li><a data-side-form='800px' href=\"" . url("node_addNode_addMenu", array("sid" => $menu["sid"], "module" => $menu["module"], "pid" => $menu["id"])) . "\">添加子菜单</a></li>";

        if ($menu["visible"])
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "visible", "id" => $menu["id"], "status" => 0)) . "\">隐藏</a></li>";
        else
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "visible", "id" => $menu["id"], "status" => 1)) . "\">显示</a></li>";
        if ($menu["defaultStatus"])
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "defaultStatus", "id" => $menu["id"], "status" => 0)) . "\">设置成默认停用</a></li>";
        else
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "defaultStatus", "id" => $menu["id"], "status" => 1)) . "\">设置成默认启用</a></li>";
        if ($menu["status"])
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "status", "id" => $menu["id"], "status" => 0)) . "\">停用</a></li>";
        else
            $actionsStr .= "<li><a href=\"" . url("node_addNode_states", array("names" => "status", "id" => $menu["id"], "status" => 1)) . "\">启用</a></li>";


        $actionsStr .= "<li><a data-confirm=\"是否删除此菜单\" href=\"" . url("node_addNode_delMenu", array("id" => $menu["id"])) . "\">删除菜单</a></li>";
        $str .= " |<div class=\"more-action\"><span class=\"arrows\">更多</span><ul>{$actionsStr}</ul></div>";
        return $str;

    }


}