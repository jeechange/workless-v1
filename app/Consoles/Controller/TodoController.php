<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/11
 * Time: 10:00
 */

namespace Consoles\Controller;

use Admin\DModel\TodoDModel;

class TodoController extends CommonController {

    public function lists() {

        $this->assign("one", "测试");

        $this->assign("active", "myTodo");
        $userId = $this->getUser("id");

        $todoDM = TodoDModel::getInstance();
        $search = $this->search();

        $search->labelType("placeholder");
        $search->addKeyword("t.tags,t.content", "任务名/任务编号/姓名");
        $search->bindData(Q()->get->all());
        $where = "t.sid = {$this->sid} and t.userId = $userId and t.status=0";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        if ($params) {
            $this->assign("params", 1);
        }

        $lists = $todoDM->name("t")->select("t")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray();

        $this->assign("searchForm", $searchForm);
        $this->assign("lists", $lists);
        $this->assign("detailsPages", $todoDM->detailsPages);

        return $this->display();
    }

}