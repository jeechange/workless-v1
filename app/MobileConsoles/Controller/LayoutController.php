<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/8/17
 * Time: 11:09
 */

namespace MobileConsoles\Controller;

use phpex\Foundation\Response;

class LayoutController extends CommonController {

    public function _initialize() {

    }


    private function tabItems() {
        return array(
            "home" => array("class" => "", "icon" => "al-icon al-icon-shouye", "name" => "首页", "url" => url("mobileConsoles_index_index")),
            "task" => array("class" => "", "icon" => "al-icon al-icon-renwu", "name" => "任务", "url" => url("mobileConsoles_todo_lists")),
            "added" => array("class" => "added", "icon" => "", "name" => "", "url" => url("mobileConsoles_task")),
            "acorn" => array("class" => "", "icon" => "al-icon al-icon-influence", "name" => "积分", "url" => url("mobileConsoles_acorn_apply","tabs_two=Action")),
            "me" => array("class" => "", "icon" => "al-icon al-icon-personalcenter", "name" => "我的", "url" => url("mobileConsoles_user_me")),
        );
    }

    public function footer() {
        $configs = parseFile(__DIR__ . "/../Conf/route.yml");
        $config = $configs[R()->getRunRoute()];
        if (!$config["tabs"]) {
            return new Response();
        }
        $tabItems = $this->tabItems();
        if (isset($tabItems[$config["tabs"]])) {
            $tabItems[$config["tabs"]]["class"] .= " active";
        }

        $this->assign("tabItems", $tabItems);
        return $this->display("Layout:footer", null, false);
    }

}