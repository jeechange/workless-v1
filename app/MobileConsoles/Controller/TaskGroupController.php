<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/8/29
 * Time: 10:21
 */

namespace MobileConsoles\Controller;


use Admin\DModel\TaskDModel;
use Admin\DModel\TaskGroupDModel;

class TaskGroupController extends CommonController {
    private $menu = "task";

    public function _initialize() {
        parent::_initialize();
        $this->assign("menu", $this->menu);
    }

    public function add() {

        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post["names"]) return $this->error("项目名称必须填写");
        if (!$post["addTime"]) return $this->error("请选择日期");

        if (!$this->sid) {
            return $this->error("请先加入一个企业，再进行任务发布等相关操作");
        }


        $groupDM = TaskGroupDModel::getInstance();
        $has = $groupDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($has) {
            return $this->error("任务项目名称必须唯一");
        }

        $group = $groupDM->newEntity();

        $group->setNames($post["names"]);
        $group->setAddTime(\DateTime::createFromFormat("Y-m-d", $post["addTime"]));
        $group->setSid($this->sid);
        $group->setMemo($post["memo"]);
        $group->setStatus(1);

        $groupDM->add($group)->flush($group);

        $this->assign("group", $groupDM->toArray($group));

        return $this->success(array("id" => $group->getId(), "html" => $this->fetch("Task/groupListsItem")));

    }


    public function choiceLists($names) {
        $taskDM = TaskDModel::getInstance();
        $taskGroupDM = TaskGroupDModel::getInstance();

        $taskEN = $taskGroupDM->find($names);
        if (!$taskEN) {
            return $this->error("查询小组信息失败！");
        }

        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";


        $where = "t.pid = {$names} and t.sid =" . $this->sid;
        if (!$this->isSuper()) {
            $where .= " and REGEXP(t.executors,'(^|\,)" . $this->getUser('id') . "(\,|$)')=1";
        }
        $params = array();
        if ($keywords) {
            $where .= " and t.names like :keywords";
            $params["keywords"] = "%" . $keywords . "%";
        }

        $lists = $taskDM->name('t')->select("t,(t.nums - t.executor) as tremain")
            ->where($where)->setParameter($params)
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);

        if (!Q()->isAjax()) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display("taskMe/myChoiceLists");
        }

        return $this->success(array(
            "html" => $this->fetch("taskMe/myChoiceListsItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }


}
