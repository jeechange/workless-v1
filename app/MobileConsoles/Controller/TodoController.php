<?php


namespace MobileConsoles\Controller;

use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/29
 * Time: 9:36
 */
class TodoController extends CommonController {
    private $menu = "task";

    public function _initialize() {
        parent::_initialize();
        $this->assign("menu", $this->menu);
    }

    public function lists() {
//        loginfo('/skdgjl', 'todo', ['todo' => 1]);
        $userId = $this->getUser('id') ?: 0;
        $this->assign("tabs_sub", "myTodo");
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";
        $params = array();
        $where = "t.sid = {$this->sid} and t.userId = $userId and t.status=0";
        if ($keywords) {
            $where .= " and (t.content LIKE :keywords or t.tags LIKE :keywords)";
            $params["keywords"] = "%" . $keywords . "%";
        }
        $todoDM = TodoDModel::getInstance();
        $lists = $todoDM->name("t")
            ->select("t")
            ->where($where)
            ->setParameter($params)
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray();
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        $this->assign("detailsPages", $todoDM->detailsMobilePages);

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            $this->assign("menu", $this->menu);
            return $this->display();
        }

        return $this->success(array(
            "html" => $this->fetch("myTodoItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));

    }

}
