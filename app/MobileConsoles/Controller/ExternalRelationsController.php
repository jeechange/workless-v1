<?php
/**
 * 外部联系人
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-10
 * Time: 11:23
 */

namespace MobileConsoles\Controller;

use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\Task;

class ExternalRelationsController extends CommonController {

    /**
     * 与我相关的外部任务
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $get = Q()->get->all();
        $tabs_sub = $get['tabs_sub'] ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";
        $offset = Q()->get->get("offset") ?: 0;
        $externalRelationsDM = new \Admin\DModel\ExternalRelationsDModel();
        $taskDM = new \Admin\DModel\TaskDModel();

        $externalRelationsDM->UserId($this->getUser('id'), $this->getUser('phone'));
        $lists = $externalRelationsDM->name("e")
            ->select("e")
            ->where("e.userId=" . $this->getUser('id'))
            ->getArray();

        $tgId = $sid = array();
        foreach ($lists as $key => $item) {
            $sid[] = $item['sid'];
            $tgId = array_merge($tgId, explode(',', $item['tgId']));
        }
        $tId = implode(",", $tgId) ?: -1;
        $sid = implode(",", $sid) ?: 0;
        if ($tId != -1) {
            $where = "t.pid in ({$tId})  AND t.status = " . $tabs_sub ;
        } else {
            $where = "t.pid={$tId}  AND t.status = " . $tabs_sub;
        }
        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            $whereSelect = $taskDM->userSelect("primordial", $this->getUser('id'), $sid, $where, $params);
        }

        if ($whereSelect) {
            $where2 = "{$whereSelect} OR tg.names like :search";
            $this->assign("params", 1);
        } else {
            $where2 = $where;
        }

        $where2 = "({$where2}) and t.visibility = 1";

        $lists = $taskDM->name("t")->select("t,u,u2")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->where($where2)->setParameter($params)
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);

        $this->assign("lists", $lists);
        $this->assign("tabs_sub", $tabs_sub);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));
        $this->assign("priorityMemo", array(1 => "A", 2 => "B", 3 => "C", 4 => "D"));

        $this->assign("keywords", $keywords);
        $this->assign("offset", $offset + $this->listsSize);
        $this->assign("infinite", count($lists) == $this->listsSize);
        return $this->display();
    }

    public function addDynamic($id) {
        /** @var Task $task */
        $userId = $this->getUser("id");
        $post = Q()->post->all();

        $dynamicDM = TaskDynamicDModel::getInstance();

        $dynamic = $dynamicDM->newEntity();

        $dynamic->setContent($post["content"]);
        $dynamic->setTid($id);
        $dynamic->setUserId($userId);
        $dynamic->setRuserId(0);
        $dynamic->setTypes($post["types"]);
        $dynamic->setAddTime(nowTime());
        $dynamic->setThumbs($post["thumbs"]);
        $dynamicDM->add($dynamic)->flush();
        return $this->success("发布成功");
    }

}