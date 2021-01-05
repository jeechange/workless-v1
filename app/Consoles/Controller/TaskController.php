<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/7/17
 * Time: 10:03
 */

namespace Consoles\Controller;


use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\RedDotDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCommentDModel;

use Admin\DModel\TaskCycleDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TaskSettingDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\CompanyOpenapi;
use Admin\Entity\Task;
use Admin\Entity\TaskCycle;
use Admin\Entity\TaskDynamic;
use Admin\Entity\TaskGroup;
use Admin\Entity\User;

class TaskController extends CommonController {

    public function lists() {
        $types = Q()->get->get("types") ?: "temp";
        $search = Q()->get->get("search") ?: "";

        $this->assign("active", $types);

        if ($search) {
            $this->assign("params", 1);
        }

        if ($types == "temp") return $this->tempLists();
        if ($types == "cycle") return $this->cycleLists();
        if ($types == "reward") return $this->rewardLists();

        $taskDM = TaskDModel::getInstance();

        $taskDM->setRewardId($this->getUser("id"));
        $where = "t.types=1 and t.sid =" . $this->sid;
        $lists = $taskDM->name("t")->select("t,s")
            ->leftJoin("Standard", "s", "t.standardId = s.id")
            ->where($where)
            ->setPage()
            ->data_sort()
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);

        return $this->display();
    }


    public function details($id) {
//        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'Task');
//        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'TaskCBA');
        $shareDM = new \Admin\DModel\ShareDModel();
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task) {
            return $this->error("获取任务信息失败");
        }
        //分享
        $share = $shareDM->name("sh")->select("sh.id")->where("sh.eventId=" . $id . " AND sh.sid=" . $this->sid . " AND sh.template='RELEASE_TASK'")->setMax(1)->getOneArray();
        $share = $share['id'] ? $share['id'] : 0;

        $pid = $task->getPid() ?: 0;
        $this->assign("groupName", $taskDM->getGroupName($pid));
        $this->assign("share", $share);

        $staffDM = StaffDModel::getInstance();

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        $executors = $staffDM->workerList($this->sid, "executors", $task->getExecutors(), 20, true);
        $executors1 = $staffDM->workerList($this->sid, "executors1", array(), 1);
        $executors2 = $staffDM->workerList($this->sid, "executors2", array(), 1);
        $executors0 = $staffDM->workerList($this->sid, "executors0", array(), 1);

        $accept = $staffDM->workerList($this->sid, "accept", $task->getAcceptId(), 1, true);
        $this->assign("accept", $accept);
        $this->assign("executors2", $executors2);
        $this->assign("executors1", $executors1);
        $this->assign("executors", $executors);
        $this->assign("executors0", $executors0);
        $taskSettingDM = TaskSettingDModel::getInstance();
        $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

        $this->assign("priorityMemo", $taskDM->priorityMemo);
        $this->assign("lists2", $lists2);
        $this->assign("task", $task);
        $this->assign("issueUser", $taskDM->getIssueUser($task->getIssueId()));
        $this->assign("executorslist", $taskDM->executorsList($task->getExecutors(), $id, "0"));

        $dynamics = $dynamicDM->name("d")->where("d.tid=$id")->order("d.id")->getArray();

        $comments = $commentDM->getComments($id);


        $this->assign("dynamics", $dynamics);
        $this->assign("comments", $comments);
        $this->assign("different", Q()->get->get("different"));

        $userId = $this->getUser("id");

        $this->assign("userId", $userId);
        $this->assign("taskStatusMemo", $taskDM->statusMemo[$task->getStatus()]);
        $this->assign("taskTypesMemo", $taskDM->typesMemo[$task->getTypes()]);
        if ($task->getTypes() == 3) {
            $taskCycleDM = TaskCycleDModel::getInstance();
            $cycleInfo = $taskCycleDM->getCycleInfo($task);
            $this->assign("taskCycleMemo", $taskDM->cycleMemo($cycleInfo->getCycleTypes(), $cycleInfo->getCycleStart(), $cycleInfo->getCycleEnd()));
            $codeNo = $task->getCodeNo();
            $listsCount = $taskDM->name("t")
                ->where("t.codeNo={$codeNo} and t.sid={$task->getSid()}")
                ->count();
            $this->assign("listsCount", $listsCount);
        }

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);
        $this->assign("standard", $standard);

        if ($userId == $task->getAcceptId()) $this->getAcceptList($task);

        if (in_array($userId, explode(",", $task->getExecutors()))) {
            $allotDM = TaskAllotDModel::getInstance();
            $allotCount = $allotDM->name("a")->where("a.tid={$id} and a.userId={$userId} and a.status=0")->count();
            $this->assign("executorsCanDo", $allotCount > 0);
            $myAllots = $allotDM->name("a")->where("a.tid={$id} and a.userId={$userId} and a.status>0")->getArray();

            $this->assign("myAllots", $myAllots);
        } else {
            $this->assign("executorsCanDo", false);
            $this->assign("myAllots", false);
        }

        if ($task->getIssueId() == $userId && $task->getStatus() > 0) {
            $allotDM = TaskAllotDModel::getInstance();
            $doneAllots = $allotDM->name("a")->where("a.tid={$id} and a.userId<>{$userId} and a.status>0")->getArray();
            $this->assign("doneAllots", $doneAllots);
        }

        if ($userId != $task->getAcceptId()) $this->getRecheckList($task);

        $this->assign("taskWorkload", explode(",", $task->getWorkload() ?: "0,0,0"));

        $thumbs = json_decode($task->getThumbs(), true);
        $parseThumbs = array();
        if ($thumbs) {
            foreach ($thumbs as $thumb) {
                if (!$thumb[0]) continue;
                $parseThumbs[] = array(
                    "name" => $thumb[1] ?: basename($thumb[0]),
                    "src" => $this->cdnBase . $thumb[0],
                    "val" => $thumb[0],
                    "type" => $this->getThumbType($thumb[0])
                );
            }
        }
        $this->assign("thumbs", $parseThumbs);
        $this->assign("cdnThumbBase", $this->cdnThumbBase);
        $this->assign("workloadMemo", $taskDM->getWorkloadMemo($task->getWorkload(), $task->getStandardTypes()));

        if ($task->getTypes() == 1) {
            $taskDM->setRewardId($userId);
            $rewardMemo = $taskDM->rewardMemo($id, $task->getStatus(), $task->getNums(), $task->getExecutor(), false);
            $this->assign("rewardMemo", $rewardMemo);
        } else {
            $this->assign("rewardMemo", false);
        }

        if ($_GET['different'] == 'yes') {
            return $this->display("ExternalRelations:details");
        }
        return $this->display();

    }

    public function history($id) {
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */
        $task = $taskDM->find($id);

        if (!$task || $task->getTypes() != 3) return $this->error("获取记录失败");

        $codeNo = $task->getCodeNo();

        $lists = $taskDM->name("t")
            ->where("t.codeNo={$codeNo} and t.sid={$task->getSid()}")
            ->order("t.cycleUse")
            ->getArray(true);
        $this->assign("lists", $lists);
        return $this->display();

    }

    private function getThumbType($name) {
        $ext = strtolower(substr(strrchr($name, "."), 1));

        if (in_array($ext, array("png", "jpg", "gif", "jpeg", "bmp"))) return "img";

        return "file";
    }

    private function getAcceptList(Task $task) {

        $allotDM = TaskAllotDModel::getInstance();

        $allots = $allotDM->name("a")->where("a.tid=" . $task->getId())->getArray();
        $taskSettingDM = TaskSettingDModel::getInstance();

        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $lists2 = $taskSettingDM->getLists($this->sid, 2);
        $lists3 = $taskSettingDM->getLists($this->sid, 3);
        $lists4 = $taskSettingDM->getLists($this->sid, 4);
        $lists5 = $taskSettingDM->getLists($this->sid, 5);
        //$lists5 = $studySettingDM->name("s")->where("s.types=1 and s.sid=" . $this->sid)->order("s.sort")->getArray();
        $this->assign("allots", $allots ?: array());

        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("lists3", $lists3);
        $this->assign("lists4", $lists4);
        $this->assign("lists5", $lists5);
        $this->assign("cdnThumb", $this->cdnThumbBase);

        $defaultAcceptHard = round(($lists4[0]["names"] + $lists4[1]["names"]) / 2 * 10) / 10;
        $defaultAcceptQuality = round(($lists5[0]["names"] + $lists5[1]["names"]) / 2);
        $this->assign("defaultAcceptHard", $defaultAcceptHard);
        $this->assign("defaultAcceptQuality", $defaultAcceptQuality);

        $userId = $this->getUser("id");

        $sid = $task->getSid();

        $staffDM = StaffDModel::getInstance();
        $myMaxAcorn = $staffDM->getMaxAcorn($userId, $sid);
        $this->assign("myMaxAcorn", $myMaxAcorn);
    }

    public function getRecheckList(Task $task) {
        $userId = $this->getUser("id");

        $allotDM = TaskAllotDModel::getInstance();
        $allots = $allotDM->name("a")->where("a.tid=" . $task->getId() . " and a.recheckId=$userId")->getArray();

        if (!$allots) return;

        $taskSettingDM = TaskSettingDModel::getInstance();

        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $lists2 = $taskSettingDM->getLists($this->sid, 2);
        $lists3 = $taskSettingDM->getLists($this->sid, 3);
        $lists4 = $taskSettingDM->getLists($this->sid, 4);
        $lists5 = $taskSettingDM->getLists($this->sid, 5);
        //$lists5 = $studySettingDM->name("s")->where("s.types=1 and s.sid=" . $this->sid)->order("s.sort")->getArray();
        $this->assign("recheckAllots", $allots);

        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("lists3", $lists3);
        $this->assign("lists4", $lists4);
        $this->assign("lists5", $lists5);
        $this->assign("cdnThumb", $this->cdnThumbBase);

        $defaultAcceptHard = round(($lists4[0]["names"] + $lists4[1]["names"]) / 2 * 10) / 10;
        $defaultAcceptQuality = round(($lists5[0]["names"] + $lists5[1]["names"]) / 2);
        $this->assign("defaultAcceptHard", $defaultAcceptHard);
        $this->assign("defaultAcceptQuality", $defaultAcceptQuality);
        $sid = $task->getSid();
        $staffDM = StaffDModel::getInstance();

        $myMaxAcorn = $staffDM->getMaxAcorn($userId, $sid);
        $this->assign("myMaxAcorn", $myMaxAcorn);

    }

    public function rewardLists() {
        $userId = $this->getUser("id") ?: 0;

        $taskDM = TaskDModel::getInstance();
        $departmentDM = DepartmentDModel::getInstance();
        //查询管理人数
        $position = $departmentDM->position($this->sid, $this->getUser("phone"));
        $position[] = $this->getUser("id");
        $position = array_unique($position);

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("t.status", "", $taskDM->statusMemo, "=全部=");
        $search->addKeyword("search", "任务名/任务编号/姓名");

        $search->bindData(Q()->get->all());
        $where = "t.types=1 and t.sid =" . $this->sid;
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        if ($params) {
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params);
        }

        $where = "t.types = 1 and t.sid = {$this->sid}";
        if (!$this->isSuperTypes()) {
            $one = "REGEXP(t.executors, '(^|\,)(" . implode("|", $position) . ")(\,|$)') = 1";
            $two = "t.issueId in (" . implode(",", $position) . ")";
            $three = "t.acceptId in (" . implode(",", $position) . ")";
            $where3 = $one . ' or ' . $two . ' or ' . $three;
            $where = "{$where} and ({$where3})";
        }

        //查询拼接
        if ($whereSelect) {
            $where = "({$where}) and {$whereSelect}";
        }

        $list = $taskDM->name("t")->select("u,u2,t,s")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->leftJoin("Standard", "s", "t.standardId = s.id")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);
        $lists = array();
        foreach ($list as $v) {
            $executors = explode(',', $v['t_executors']);
            if (in_array($userId, $executors)) {
                $lists[] = $v;
            }
        }
        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);

        return $this->display("rewardLists");
    }


    public function tempLists() {
        $userId = $this->getUser("id") ?: 0;

        $taskDM = TaskDModel::getInstance();
        $departmentDM = DepartmentDModel::getInstance();
        //查询管理人数
        $position = $departmentDM->position($this->sid, $this->getUser("phone"));
        $position[] = $this->getUser("id");
        $position = array_unique($position);

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("t.status", "", $taskDM->statusMemo, "=全部=");
        $search->addKeyword("search", "任务名/任务编号/姓名");

        $search->bindData(Q()->get->all());
        $where = "t.types=2 and t.sid =" . $this->sid;
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        if ($params) {
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params);
        }

        $where = "t.types = 2 and t.sid = {$this->sid}";
        if (!$this->isSuperTypes()) {
            $one = "REGEXP(t.executors, '(^|\,)(" . implode("|", $position) . ")(\,|$)') = 1";
            $two = "t.issueId in (" . implode(",", $position) . ")";
            $three = "t.acceptId in (" . implode(",", $position) . ")";
            $where3 = $one . ' or ' . $two . ' or ' . $three;
            $where = "{$where} and ({$where3})";
        }

        //查询拼接
        if ($whereSelect) {
            $where = "({$where}) and {$whereSelect}";
        }

        $lists = $taskDM->name("t")->select("u,u2,t,s")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->leftJoin("Standard", "s", "t.standardId = s.id")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);

        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        return $this->display("tempLists");
    }


    public function cycleLists() {
        $userId = $this->getUser("id") ?: 0;
        $taskDM = TaskDModel::getInstance();
        $departmentDM = DepartmentDModel::getInstance();
        //查询管理人数
        $position = $departmentDM->position($this->sid, $this->getUser("phone"));
        $position[] = $this->getUser("id");
        $position = array_unique($position);

        $search = $this->search();
        $search->labelType("placeholder");
//        $search->addChoiceKeyword(array("t.names" => "任务名称", "t.codeNo" => "任务编号", "u.fullName" => "发布人"));
        $search->addSelect("t.status", "状态", array(0 => "执行中", 1 => "等待验收", 2 => "已取消", 3 => "已完成"), "=全部=");
        $search->addKeyword("search", "任务名/任务编号/姓名");

        $search->bindData(Q()->get->all());
        $where = "t.types=3 and t.sid =" . $this->sid;
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        if ($params) {
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params);
        }

        $where = "t.types = 3 and t.sid = {$this->sid}" . " and t.cycleUse <=1";
        if (!$this->isSuperTypes()) {
            $one = "REGEXP(t.executors, '(^|\,)(" . implode("|", $position) . ")(\,|$)') = 1";
            $two = "t.issueId in (" . implode(",", $position) . ")";
            $three = "t.acceptId in (" . implode(",", $position) . ")";
            $where3 = $one . ' or ' . $two . ' or ' . $three;
            $where = "{$where} and ({$where3})";
        }

        //查询拼接
        if ($whereSelect) {
            $where = "({$where}) and {$whereSelect}";
        }

        $lists = $taskDM->name("t")->select("u,u2,t")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->leftJoin("Standard", "s", "t.standardId = s.id")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);

        if ($lists) {
            foreach ($lists as &$item) {
                if ($item["t_codeNo"] <= 0) continue;
                $subLists = $taskDM->name("t")
                    ->select("u,t")
                    ->leftJoin("User", "u", "u.id=t.issueId")
                    ->leftJoin("Standard", "s", "t.standardId = s.id")
                    ->where("t.codeNo='{$item["t_codeNo"]}' and t.sid={$item["t_sid"]}")->order("t.cycleUse")
                    ->getArray(true);
                if (count($subLists) > 1) {
                    end($subLists);
                    $item["mainItem"] = current($subLists);
                    $item["subLists"] = $subLists;
                }
            }
        }

        $this->assign("lists", $lists);

        $this->assign("searchForm", $searchForm);

        return $this->display("cycleLists");
    }

    public function add() {
        $types = Q()->get->get("types") ?: "reward";
        $this->assign("priorityDefault", 2);

        $this->assign("active", $types);
        if ($types == "temp") return $this->tempAdd();
        if ($types == "cycle") return $this->cycleAdd();

        $staffDM = StaffDModel::getInstance();
        $taskDM = TaskDModel::getInstance();
        $standardDM = StandardDModel::getInstance();

        if (Q()->isGet()) {
            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);
            //$taskSettingDM = TaskSettingDModel::getInstance();
            //$lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->getArray();
            //$lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);
            //$this->assign("lists1", $lists1);
            //$this->assign("lists2", $lists2);
            $accept = $staffDM->workerList($this->sid, "accept", array(), 1);
            $this->assign("accept", $accept);

            $this->assign("today", $taskDM->getToday());
            $this->assign("tomorrow", $taskDM->getTomorrow());
            $this->assign("week", $taskDM->getWeekTime());

            $taskGroupDM = TaskGroupDModel::getInstance();

            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));

            return $this->display();
        }
        $post = Q()->post->all();

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 1;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);
        //$post["executors"] = "";

        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $this->getUser("id");
        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;
        $task = $taskDM->newEntity();


        $taskDM->create($post, $task);

        unset($post["executors"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        $task->setAddTime(nowTime());
        $task->setCodeNo($taskDM->getCodeNo($this->sid));
        $taskDM->add($task)->flush();

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();
        $AllotDM->createAllots($post["executors"], $task);
        $dynamicDM = TaskDynamicDModel::getInstance();

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释

        $taskDM->sendAddMessage($task);

        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK_REWARD", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url("consoles_taskMe_myIssue"));
            return $this->success("发布成功", url("consoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success("发布成功");
    }

    public function cycleAdd() {
        $staffDM = StaffDModel::getInstance();
        $taskDM = TaskDModel::getInstance();
        $standardDM = StandardDModel::getInstance();

        if (Q()->isGet()) {
            $executors = $staffDM->workerList($this->sid, "executors");
            $accept = $staffDM->workerList($this->sid, "accept", array(), 1);
            $this->assign("executors", $executors);
            $this->assign("accept", $accept);

            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);

//            $taskSettingDM = TaskSettingDModel::getInstance();
//            $lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->getArray();
//            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);
            $this->assign("priorityMemo", $taskDM->priorityMemo);
//            $this->assign("lists1", $lists1);
//            $this->assign("lists2", $lists2);

            $this->assign("today", $taskDM->getToday());
            $this->assign("tomorrow", $taskDM->getTomorrow());
            $this->assign("week", $taskDM->getWeekTime());
            $taskGroupDM = TaskGroupDModel::getInstance();

            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));
            return $this->display("cycleAdd");
        }

        $post = Q()->post->all();

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 3;
        $post["sid"] = $this->sid;
        $post["executors"] = join(",", $post["executors"]);
        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["acorn"] = join("-", $post["acorn"]);

        $post["issueId"] = $this->getUser("id");
        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;

        $task = $taskDM->newEntity();

        $taskDM->create($post, $task);

        unset($post["nums"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        $startTime = $taskDM->getCycleTime($task, 0);
        $endTime = $taskDM->getCycleTime($task, 1);

        $task->setAddTime(\DateTime::createFromFormat("Y-m-d H:i:s", $startTime));
        $task->setDeadline(\DateTime::createFromFormat("Y-m-d H:i:s", $endTime));

        $task->setCodeNo($taskDM->getCodeNo($this->sid));


        $taskDM->setCycleNext($task);
        $task->setCycleUse(1);


        $taskDM->add($task)->flush();

        $taskCycleDM = TaskCycleDModel::getInstance();
        $taskCycle = $taskCycleDM->newEntity();
        $taskCycleDM->create($post, $taskCycle);

        $taskCycle->setCodeNo($task->getCodeNo());
        $taskCycle->setCycleNext($task->getCycleNext());
        $taskCycle->setFirstTime($task->getAddTime());
        $taskCycle->setLastTime($task->getAddTime());
        $taskCycleDM->add($taskCycle)->flush($taskCycle);

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();
        $AllotDM->createAllots($post["executors"], $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::createTaskTodo($task);

        $taskDM->sendAddMessage($task);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布周期任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url("consoles_taskMe_myIssue"));
            return $this->success("发布成功", url("consoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success("发布成功");

    }

    public function tempAdd() {
        $staffDM = StaffDModel::getInstance();
        $taskDM = TaskDModel::getInstance();
        $standardDM = StandardDModel::getInstance();

        if (Q()->isGet()) {
            $executors = $staffDM->workerList($this->sid, "executors");
            $accept = $staffDM->workerList($this->sid, "accept", array(), 1);
            $this->assign("executors", $executors);
            $this->assign("accept", $accept);
            $taskSettingDM = TaskSettingDModel::getInstance();

            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);

//            $lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->getArray();
//            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);
            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("priorityDefault", 2);
//            $this->assign("lists1", $lists1);
//            $this->assign("lists2", $lists2);

            $taskGroupDM = TaskGroupDModel::getInstance();

            $groups = $taskGroupDM->getList($this->sid);

            $this->assign("groups", $groups);
            $this->assign("today", $taskDM->getToday());
            $this->assign("tomorrow", $taskDM->getTomorrow());
            $this->assign("week", $taskDM->getWeekTime());
            $this->assign("acorns", $taskSettingDM->getAcorns($this->sid));

            $taskGroupDM = TaskGroupDModel::getInstance();

            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));
            return $this->display("tempAdd");
        }

        $post = Q()->post->all();

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 2;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);
        $post["executors"] = join(",", $post["executors"]);
        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $this->getUser("id");

        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;
        $task = $taskDM->newEntity();

        $taskDM->create($post, $task);
        unset($post["nums"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }
        $task->setAddTime(nowTime());
        $task->setCodeNo($taskDM->getCodeNo($this->sid));
        $taskDM->add($task)->flush();

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();

        $AllotDM->createAllots($post["executors"], $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::createTaskTodo($task);

        $taskDM->sendAddMessage($task); // 发送邮件和短信通知

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));

        //$shareid = 0;

        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url("consoles_taskMe_myIssue"));
            return $this->success("发布成功", url("consoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success("发布成功");

    }

    public function modify($id) {
//        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'Task');
//        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'TaskCBA');

        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) {
            return $this->error("获取任务信息失败");
        }

        if ($task->getTypes() == 2) return $this->tempModify($id);
        if ($task->getTypes() == 3) return $this->cycleModify($id);

        $taskDM = TaskDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $standardDM = StandardDModel::getInstance();

        if (Q()->isGet()) {
            $accept = $staffDM->workerList($this->sid, "accept", $task->getAcceptId(), 1);
            $executors = $staffDM->workerList($this->sid, "executors", $task->getExecutors(), 20, true);

            $this->assign("accept", $accept);
            $this->assign("executors", $executors);
            $taskSettingDM = TaskSettingDModel::getInstance();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $acorns = explode("-", $task->getAcorn());

            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);
            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("lists2", $lists2);
            $this->assign("acorns", $acorns);
            $this->assign("task", $task);


            $this->assign("issueUser", $taskDM->getIssueUser($task->getIssueId()));
            $this->assign("executorslist", $taskDM->executorsList($task->getExecutors(), $id, 0));

            $this->assign("actionTypes", Q()->get->get("a"));

            $standard = $standardDM->find($task->getStandardId() ?: 0);
            $this->assign("standard", $standard);
            $this->assign("aid", Q()->get->get("aid"));
            $this->assign("allotWorkloads", explode(",", $task->getWorkload()));
            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);

            $taskGroupDM = TaskGroupDModel::getInstance();
            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));

            $taskGroup = $taskGroupDM->find($task->getPid() ?: 0);
            $this->assign("taskGroupName", $taskGroup ? $taskGroup->getNames() : "设置项目");

            $this->assign("today", $taskDM->getToday());
            $this->assign("tomorrow", $taskDM->getTomorrow());
            $this->assign("week", $taskDM->getWeekTime());

            return $this->display();
        }

        $post = Q()->post->all();

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 1;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);
        //$post["executors"] = "";

        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $this->getUser("id");
        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;
        $task = $taskDM->newEntity();


        $taskDM->create($post, $task);

        unset($post["executors"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        $task->setAddTime(nowTime());
        $taskDM->save($task)->flush();

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();
        $AllotDM->createAllots($post["executors"], $task);


        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::updateTaskTodo($task);

        $taskDM->sendModifyMessage($task); // 发送邮件和短信通知

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]修改悬赏任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//
//
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url('consoles_task_details', 'id=' . $task->getId()));
            return $this->success("修改成功", url("consoles_index_sharePage", array("share" => $shareid, "modify" => 1)));
        }

        return $this->success("修改成功");

    }

    public function tempModify($id) {
        $taskDM = TaskDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $standardDM = StandardDModel::getInstance();

        $userId = $this->getUser("id");
        $this->assign("userId", $userId);

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (Q()->isGet()) {
            $accept = $staffDM->workerList($this->sid, "accept", $task->getAcceptId(), 1);
            $executors = $staffDM->workerList($this->sid, "executors", $task->getExecutors(), 20, true);

            $this->assign("accept", $accept);
            $this->assign("executors", $executors);
            $taskSettingDM = TaskSettingDModel::getInstance();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $acorns = explode("-", $task->getAcorn());

            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);
            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("lists2", $lists2);
            $this->assign("acorns", $acorns);
            $this->assign("task", $task);


            $this->assign("issueUser", $taskDM->getIssueUser($task->getIssueId()));
            $this->assign("executorslist", $taskDM->executorsList($task->getExecutors(), $id, 0));

            $this->assign("visibilityMemo", $taskDM->visibilityMemo);

            $this->assign("actionTypes", Q()->get->get("a"));

            $standard = $standardDM->find($task->getStandardId() ?: 0);
            $this->assign("standard", $standard);
            $this->assign("aid", Q()->get->get("aid"));
            $this->assign("allotWorkloads", explode(",", $task->getWorkload()));
            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);

            $taskGroupDM = TaskGroupDModel::getInstance();
            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));

            $taskGroup = $taskGroupDM->find($task->getPid() ?: 0);
            $this->assign("taskGroupName", $taskGroup ? $taskGroup->getNames() : "设置项目");

            $this->assign("today", $taskDM->getToday());
            $this->assign("tomorrow", $taskDM->getTomorrow());
            $this->assign("week", $taskDM->getWeekTime());

            return $this->display("tempModify");
        }

        $post = Q()->post->all();

        $thumbs = json_decode($task->getThumbs(), true) ?: array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 2;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);
        $post["executors"] = join(",", $post["executors"]);
        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $this->getUser("id");

        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;

        $taskDM->create($post, $task);
        unset($post["nums"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        $taskDM->save($task)->flush();

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();

        $AllotDM->createAllots($post["executors"], $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::updateTaskTodo($task);
        $taskDM->sendModifyMessage($task); // 发送邮件和短信通知

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]修改普通任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url('consoles_task_details', 'id=' . $task->getId()));
            return $this->success("修改成功", url("consoles_index_sharePage", array("share" => $shareid, "modify" => 1)));
        }

        return $this->success("修改成功");


    }

    public function cycleModify($id) {
        $staffDM = StaffDModel::getInstance();
        $taskDM = TaskDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $userId = $this->getUser("id");

        $this->assign("userId", $userId);

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (Q()->isGet()) {
            $executors = $staffDM->workerList($this->sid, "executors", $task->getExecutors(), 20, true);
            $executors1 = $staffDM->workerList($this->sid, "executors1", array(), 1);

            $accept = $staffDM->workerList($this->sid, "accept", $task->getAcceptId(), 1);
            $this->assign("accept", $accept);
            $this->assign("executors1", $executors1);
            $this->assign("executors", $executors);
            $taskSettingDM = TaskSettingDModel::getInstance();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->getArray();

            $acorns = explode("-", $task->getAcorn());

            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("lists2", $lists2);
            $this->assign("acorns", $acorns);
            $this->assign("task", $task);
            $this->assign("issueUser", $taskDM->getIssueUser($task->getIssueId()));
            $this->assign("executorslist", $taskDM->executorsList($task->getExecutors(), $id, Q()->get->get("aid")));
            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);


            $standard = $standardDM->find($task->getStandardId() ?: 0);
            $this->assign("standard", $standard);
            $this->assign("aid", Q()->get->get("aid"));
            $this->assign("allotWorkloads", explode(",", $task->getWorkload()));
            $standards = $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
            $this->assign("standards", $standards);

            $taskGroupDM = TaskGroupDModel::getInstance();

            $this->assign("groupHtml", $taskGroupDM->getGroupHtml($this->sid));

            $taskGroup = $taskGroupDM->find($task->getPid() ?: 0);

            $this->assign("taskGroupName", $taskGroup ? $taskGroup->getNames() : "设置项目");

            return $this->display("cycleModify");
        }


        $post = Q()->post->all();

        $thumbs = json_decode($task->getThumbs(), true) ?: array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $post["types"] = 3;
        $post["sid"] = $this->sid;
        $post["executors"] = join(",", $post["executors"]);
        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["acorn"] = join("-", $post["acorn"]);

        $post["acceptId"] = $post["accept"] ? current($post["accept"]) : 0;

        $taskDM->create($post, $task);

        unset($post["nums"]);
        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        $cTask = clone $task;

        $startTime = $taskDM->getCycleTime($cTask, 0);
        $endTime = $taskDM->getCycleTime($cTask, 1);


        $cTask->setAddTime(\DateTime::createFromFormat("Y-m-d H:i:s", $startTime));
        $cTask->setDeadline(\DateTime::createFromFormat("Y-m-d H:i:s", $endTime));

        $taskDM->setCycleNext($cTask);
        //$task->setCycleUse(1);
        $taskDM->save($task)->flush();

        $taskCycleDM = TaskCycleDModel::getInstance();
        $taskCycle = $taskCycleDM->getCycleInfo($task);

        $oldStatus = $taskCycle->getStatus();

        $taskCycleDM->create($post, $taskCycle);

        $taskCycle->setCodeNo($task->getCodeNo());
        $taskCycle->setStatus($oldStatus);
        $taskCycle->setCycleNext($cTask->getCycleNext());
        $taskCycleDM->save($taskCycle)->flush($taskCycle);
        $taskDM->name("t")->where("t.sid={$task->getSid()} and t.codeNo={$task->getCodeNo()}")
            ->update(array(
                "t.cycleTypes" => $taskCycle->getCycleTypes(),
                "t.cycleTimes" => $taskCycle->getCycleTimes(),
                "t.cycleStart" => sprintf("'%s'", $taskCycle->getCycleStart()),
                "t.cycleEnd" => sprintf("'%s'", $taskCycle->getCycleEnd()),
            ));
        /** @var Task $maxTask */
        $maxTask = $taskDM->findOneBy(array("sid" => $task->getSid(), "codeNo" => $task->getCodeNo()), array("cycleUse" => "desc"));

        $maxTask->setCycleNext($taskCycle->getCycleNext());
//        $maxTask->setAddTime($cTask->getAddTime());
//        $maxTask->setDeadline($cTask->getDeadline());

        $taskDM->save($maxTask)->flush($maxTask);

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->adds($task);

        $AllotDM = TaskAllotDModel::getInstance();
        $AllotDM->createAllots($post["executors"], $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::updateTaskTodo($task);
        $taskDM->sendModifyMessage($task); // 发送邮件和短信通知
        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]修改周期任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            $this->assign("showOnPopup", true);
            $this->assign("flushMainUrl", url('consoles_task_details', 'id=' . $task->getId()));
            return $this->success("修改成功", url("consoles_index_sharePage", array("share" => $shareid, "modify" => 1)));
        }

        return $this->success("修改成功");

    }

    public function dynamic_reply($id) {

        $types = Q()->get->get("types") ?: "reward";
        $dynamicDM = TaskDynamicDModel::getInstance();

        /** @var TaskDynamic $dynamic */

        $dynamic = $dynamicDM->find($id);

        if (!$dynamic) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }
        $post = Q()->post->all();
        if (trim($post["content"]) == "") return $this->ajaxReturn(array("status" => "n", "info" => "请输入评论/回复内容"));

        $rdynamic = $dynamicDM->newEntity();

        $rdynamic->setContent($post["content"]);
        $rdynamic->setTid($dynamic->getTid());
        $rdynamic->setUserId($this->getUser("id"));
        $rdynamic->setRuserId($dynamic->getUserId() == $this->getUser("id") ? 0 : $dynamic->getUserId());
        $rdynamic->setTypes(1);
        $rdynamic->setAddTime(nowTime());
        $dynamicDM->add($rdynamic)->flush();
        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "回复成功",
            "url" => url('consoles_mod', 'con=task&types=' . $types . '&id=' . $dynamic->getTid())
        ));

    }


    public function dynamic_comment($id) {
        $types = Q()->get->get("types") ?: "reward";
        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        /** @var TaskDynamic $dynamic */

        $dynamic = $dynamicDM->find($id);

        if (!$dynamic) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $post = Q()->post->all();
        if (trim($post["content"]) == "") return $this->ajaxReturn(array("status" => "n", "info" => "请输入评论/回复内容"));

        $comment = $commentDM->newEntity();

        $comment->setTid($dynamic->getTid());
        $comment->setAid($id);
        $comment->setUserId($this->getUser("id"));
        $comment->setReplyId($post["relId"]);
        $comment->setContent($post["content"]);
        $commentDM->add($comment)->flush();
        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "评论/回复成功",
            "url" => url('consoles_mod', 'con=task&types=' . $types . '&id=' . $dynamic->getTid())
        ));
    }

    public function dynamicAction($id) {
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $post = Q()->post->all();
        $userId = $this->getUser("id");

        $url = url("consoles_task_details", "id=$id");


        if ($post["types"] == "dynamic") {
            $executors = explode(",", $task->getExecutors());
            if (!in_array($userId, $executors) && $task->getIssueId() != $userId && $task->getAcceptId() != $userId) {
                return $this->ajaxReturn(array("status" => "n", "info" => "参与此项目的成员才能发布任务动态"));
            }

            $dynamicDM = TaskDynamicDModel::getInstance();
            $dynamic = $dynamicDM->newEntity();
            $dynamic->setContent($post["dynamicMemo"]);
            $dynamic->setTid($id);
            $dynamic->setUserId($userId);
            $dynamic->setRuserId(0);
            $dynamic->setTypes(1);
            $dynamic->setAddTime(nowTime());
            $dynamicDM->add($dynamic)->flush();

            $message = sprintf("#%d %s 任务动态提醒：%s说 \"%s\"", $task->getCodeNo(), $task->getNames(), $this->getUser("fullName"), $post["dynamicMemo"]);
            CompanyOpenapiDModel::sendMessage($task, $message, true);
            return $this->ajaxReturn(array(
                "status" => "y",
                "info" => "操作成功",
                "url" => $url
            ));
        }

        if ($task->getStatus() > 1) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行中等待的任务才能操作"));
        }


        if (in_array($post["types"], array("allot", "cancel"))) return $this->actionIssue($task, $post, $url);
        if (in_array($post["types"], array("done", "redeploy"))) return $this->actionTodo($task, $post, $url);
        if (in_array($post["types"], array("over"))) return $this->actionAccept($task, $post, $url);
        return $this->ajaxReturn(array("status" => "n", "info" => "非法操作"));
    }


    /**
     * 发布人的操作：指派他人，取消
     * @param Task $task
     * @param $post
     * @param $url
     * @return \phpex\Foundation\Response
     */

    public function actionIssue(Task $task, $post, $url) {
        if ($task->getIssueId() != $this->getUser("id") && $task->getAcceptId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的发布人或验收人"));
        }
        $types = $post["types"];

        $allotDM = TaskAllotDModel::getInstance();

        if ($types == "allot") {
            $userDM = UserDModel::getInstance();
            $users = array();

            if (!$post["executors1"]) {
                return $this->ajaxReturn(array("status" => "n", "info" => "请选择执行人员"));
            }
            $isAllot = $allotDM->isAllot($task, $post["executors1"], $this->getUser("id"));
            if ($isAllot) {
                return $this->ajaxReturn(array("status" => "n", "info" => $isAllot));
            }

            $userIds = array($this->getUser("id"), $task->getAcceptId());

            foreach ($post["executors1"] as $executor) {
                /** @var User $user */
                $user = $userDM->find($executor ?: 0);
                if (!$user) continue;

                $allot = $allotDM->createAllot($executor, $task);
                if ($allot) {
                    $users[] = $user->getFullName();
                    $userIds[] = $executor;
                }
            }
            //执行完成更新任务统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->updateAllot($task, $this->getUser('id'), $post["types"], $post["executors1"]);

            if ($users) {
                $dynamicDM = TaskDynamicDModel::getInstance();
                $dynamic = $dynamicDM->newEntity();
                $dynamic->setContent(sprintf("[系统消息]指派了新成员[%s]，附言：%s", join(",", $users), $post["executors1allotMemo"]));
                $dynamic->setTid($task->getId());
                $dynamic->setUserId($this->getUser("id"));
                $dynamic->setRuserId(0);
                $dynamic->setTypes(2);
                $dynamic->setAddTime(nowTime());
                $dynamicDM->add($dynamic)->flush();
            }
            $message = sprintf("#%d %s 任务动态提醒：%s指派了新成员[%s]，附言：%s",
                $task->getCodeNo(),
                $task->getNames(),
                $this->getUser("fullName"),
                join(",", $users),
                $post["executors1allotMemo"]
            );
            CompanyOpenapiDModel::sendMessage($task, $message, $userIds);


            $taskDM = TaskDModel::getInstance();
            $taskDM->updateTagsAndResolves($task);
            TodoDModel::createTaskTodo($task, true);

            $taskDM->sendAllotMessage($task, $post["executors1"]); // 发送邮件和短信通知

            return $this->ajaxReturn(array(
                "status" => "y",
                "info" => "操作成功",
                "url" => $url
            ));
        }

        if ($task->getIssueId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的发布人"));
        }

        $task->setStatus(2);
        $taskDM = TaskDModel::getInstance();
        $taskDM->save($task)->flush($task);

        $allotDM = TaskAllotDModel::getInstance();
        $allotDM->name("a")->where("a.tid={$task->getId()} and a.status=0")->update(array("a.status" => 3));

        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $this->getUser('id'), $post["types"]);

        $taskDM->updateTagsAndResolves($task);
        TodoDModel::createTaskDoneTodoAll($task);

        $taskDM->sendCancelMessage($task); // 发送邮件和短信通知

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]取消执行任务,取消原因：" . $post["cancelMemo"]);
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(4);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        $message = sprintf("#%d %s 任务动态提醒：%s取消执行任务，取消原因:%s", $task->getCodeNo(), $task->getNames(), $this->getUser("fullName"), $post["cancelMemo"]);
        CompanyOpenapiDModel::sendMessage($task, $message, true);
        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }


    /**
     * 执行人员：完成任务，转派他人
     * @param Task $task
     * @param $post
     * @param $url
     * @return \phpex\Foundation\Response
     */

    public function actionTodo(Task $task, $post, $url) {
        $executors = explode(",", $task->getExecutors());
        if (!in_array($this->getUser("id"), $executors)) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您未参与此任务"));
        }

        $allotDM = TaskAllotDModel::getInstance();

        if ($post["types"] == "redeploy") {
            $redeployNum = 0;

            if ($task->getStandardTypes() == 2) {
                if (!preg_match("/^(0|[1-9][0-9]*)$/", $post["redeployNum"])) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "完成数量不正确"));
                }
                $redeployNum = intval($post["redeployNum"]);
            }

            if ($post["redeployDay"] !== "0" && !preg_match("/^[1-9][0-9]*$/", $post["redeployDay"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时天数不正确"));
            }
            if (!preg_match("/^[0-7]$/", $post["redeployHour"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时小时不正确"));
            }
            if (!preg_match("/^(0|15|30|45)$/", $post["redeployMinute"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时分钟不正确"));
            }

            $acceptDay = join(",", array($post["redeployDay"], $post["redeployHour"], $post["redeployMinute"]));

            if ($redeployNum > 0 && $acceptDay == "0,0,0") {
                return $this->ajaxReturn(array("status" => "n", "info" => "完成数量大于0时，耗时不能为0"));
            }

            if ($task->getStandardTypes() == 2 && $redeployNum == 0 && $acceptDay != "0,0,0") {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时大于0时，请填写完成数量"));
            }

            if ($task->getStandardTypes() == 2) {
                $acceptDay .= "," . $redeployNum;
            }

            if (!$post["executors2"]) {
                return $this->ajaxReturn(array("status" => "n", "info" => "请选择执行人员"));
            }
            $isAllot = $allotDM->isAllot($task, $post["executors2"], $this->getUser("id"));
            if ($isAllot) {
                return $this->ajaxReturn(array("status" => "n", "info" => $isAllot));
            }
            //TodoDModel::createTaskRedeployTodo($task, $this->getUser("id"), $post["executors2"]);
        } else {
            if ($post["acceptDay"] !== "0" && !preg_match("/^[1-9][0-9]*$/", $post["acceptDay"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时天数不正确"));
            }
            if (!preg_match("/^[0-7]$/", $post["acceptHour"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时小时不正确"));
            }
            if (!preg_match("/^(0|15|30|45)$/", $post["acceptMinute"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时分钟不正确"));

            }

            $acceptNum = 0;

            if ($task->getStandardTypes() == 2) {
                if (!preg_match("/^(0|[1-9][0-9]*)$/", $post["acceptNum"])) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "完成数量不正确"));
                }
                $acceptNum = intval($post["acceptNum"]);
            }


            $acceptDay = join(",", array($post["acceptDay"], $post["acceptHour"], $post["acceptMinute"]));

            if ($acceptDay == "0,0,0") {
                if ($acceptNum > 0) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "完成数量大于0时，耗时不能为0"));
                }
                $execCount = $allotDM->name("a")->where("a.tid=:tid and a.userId<>:userId and a.status<2")->setParameter(array(
                    "tid" => $task->getId(),
                    "userId" => $this->getUser("id"),
                    //"addTime" => now(),
                ))->count();
                if ($execCount < 1)
                    return $this->ajaxReturn(array("status" => "n", "info" => "您是此任务的唯一执行人，耗时不能为0，如需要取消任务，请与发布人商量"));
            } elseif ($task->getStandardTypes() == 2 && $acceptNum == 0) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时大于0时，请填写完成数量"));
            }
            if ($task->getStandardTypes() == 2) {
                $acceptDay .= "," . $acceptNum;
            }

        }

        $params = array(
            "tid" => $task->getId(),
            "userId" => $this->getUser("id"),
            //"addTime" => now(),
        );

        $myAllotCount = $allotDM->name("a")->where("a.tid=:tid and a.userId=:userId")->setParameter($params)->count();
        if (!$myAllotCount) {
            $allotDM->createAllot($this->getUser("id"), $task);
        }


        $myExecAllotCount = $allotDM->name("a")->where("a.tid=:tid and a.userId=:userId and a.status=0")->setParameter($params)->count();
        if (!$myExecAllotCount) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您已完成任务或已经转派他人！"));
        }
        $params["addTime"] = now();


        $updateParams = array("a.status" => $post["types"] == "redeploy" ? 2 : 1, "a.doneTime" => "'{$params["addTime"]}'", "a.acceptDay" => "'{$acceptDay}'");
        if ($acceptDay == "0,0,0" || $acceptDay == "0,0,0,0") {
            $updateParams["a.accept"] = 1;
            $updateParams["a.workload"] = "'0,0,0,0'";
        }

        unset($params["addTime"]);

        $allotDM->name("a")->where("a.tid=:tid and a.userId=:userId and a.status=0")->setParameter($params)
            ->update($updateParams);


        if ($post["types"] == "redeploy") {
            $users = $allotDM->createAllots($post["executors2"], $task, $this->getUser("id"), 2);
            $users = join(",", $users);
            $content = "[系统消息]执行转派任务操作,将由[{$users}]执行，附言：" . $post["redeployMemo"];

            $message = sprintf("#%d %s 任务动态提醒：%s将任务转派给[%s]执行，附言：%s",
                $task->getCodeNo(),
                $task->getNames(),
                $this->getUser("fullName"),
                $users,
                $post["redeployMemo"]
            );
            $userIds = array($task->getIssueId(), $task->getAcceptId(), $this->getUser("id"));
            $executors2 = explode(",", $post["executors2"]);
            CompanyOpenapiDModel::sendMessage($task, $message, array_merge($userIds, $executors2));
            $taskDM = TaskDModel::getInstance();
            $taskDM->updateTagsAndResolves($task);
            TodoDModel::createTaskRedeployTodo($task, $this->getUser("id"), $post["executors2"]);

            $taskDM->sendRedeployMessage($this->getUser("id"), $post["executors2"], $task, $message); // 发送短信和邮件
        } else {
            $content = "[系统消息]执行完成任务操作，附言：" . $post["acceptMemo"];
            $message = sprintf("#%d %s 任务动态提醒：%s交付了任务，耗时%s%s",
                $task->getCodeNo(),
                $task->getNames(),
                $this->getUser("fullName"),
                TaskDModel::getInstance()->getWorkloadMemo($acceptDay),
                $post["acceptMemo"] ? "，附言：" . $post["acceptMemo"] : ""
            );
            CompanyOpenapiDModel::sendMessage($task, $message, array($task->getIssueId(), $task->getAcceptId(), $this->getUser("id")));

            $taskDM = TaskDModel::getInstance();
            $taskDM->sendSubmitMessage($this->getUser("id"), $acceptDay, $task, $message); // 发送短信和邮件
        }
        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $this->getUser('id'), $post["types"], $post["executors2"]);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent($content);
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes($post["types"] == "redeploy" ? 2 : 3);
        $dynamic->setAddTime(nowTime());

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $dynamic->setThumbs(json_encode($thumbs));
        $dynamicDM->add($dynamic)->flush();

        if ($post["types"] == "done") {
            if (isset($params["userId"])) unset($params["userId"]);
            $allotNoDone = $allotDM->name("a")->where("a.tid=:tid and (a.status=0 or a.status is null)")->setParameter($params)->count();
            if (!$allotNoDone) {
                $task->setStatus(1);
                $taskDM = TaskDModel::getInstance();
                $taskDM->save($task)->flush($task);
            }
            TodoDModel::createTaskDoneTodo($task, $this->getUser("id"));
        }

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }


    /**
     * 验收人操作结束周期的动作
     * @param Task $task
     * @param $post
     * @param $url
     * @return \phpex\Foundation\Response
     */

    public function actionAccept(Task $task, $post, $url) {
        if ($task->getAcceptId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的验收人"));
        }

        if ($task->getTypes() != 3) {
            return $this->ajaxReturn(array("status" => "n", "info" => "只能对周期任务进行操作"));
        }

        $allotDM = TaskAllotDModel::getInstance();

        $nowTime = now();

        //$allotDM->name("a")->where("a.tid={$task->getId()} and a.status=0")->update(array("a.status" => 1, "a.doneTime" => "'{$nowTime}'"));

        $taskDM = TaskDModel::getInstance();
        $task->setAstatus(2);
        $task->setEndTime(nowTime());
        $taskDM->save($task)->flush($task);

        $taskCycleDM = TaskCycleDModel::getInstance();

        /** @var TaskCycle $taskCycle */

        $taskCycle = $taskCycleDM->findOneBy(array("sid" => $task->getSid(), "codeNo" => $task->getCodeNo()));

        if ($taskCycle) {
            $taskCycle->setStatus(2);
            $taskCycleDM->save($taskCycle)->flush($taskCycle);
            $taskDM->name("t")
                ->where("t.sid={$task->getSid()} and t.codeNo={$task->getCodeNo()} and t.astatus<>2")
                ->update(array("t.astatus" => 2));
        }

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]结束任务，附言：" . $post["overMemo"]);
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(5);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();


        $message = sprintf("#%d %s 任务动态提醒：%s结束周期任务，附言：%s",
            $task->getCodeNo(),
            $task->getNames(),
            $this->getUser("fullName"),
            $post["overMemo"]
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);


        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }


    /**
     * 领取悬赏任务
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function receive($id) {
        $taskDM = TaskDModel::getInstance();


        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        if ($task->getTypes() != 1) {
            return $this->ajaxReturn(array("status" => "n", "info" => "只能领取悬赏的任务"));
        }

        if ($task->getNums() <= $task->getExecutor()) {
            return $this->ajaxReturn(array("status" => "n", "info" => "悬赏任务已被领取完"));
        }


        $userId = $this->getUser("id");

//        if ($userId == $task->getIssueId()) {
//            return $this->ajaxReturn(array("status" => "n", "info" => "发布人不能领取此任务$userId" . $task->getIssueId()));
//        }

        $executors = explode(",", $task->getExecutors());
        if (in_array($userId, $executors)) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您已领取此任务，不可重复领取"));
        }

        TaskAllotDModel::getInstance()->createAllot($userId, $task, 3);
        $task->setExecutor($task->getExecutor() + 1);
        $taskDM->save($task)->flush($task);
        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->addAllot($task, $userId);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("领取悬赏任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(2);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        $message = sprintf("#%d %s 任务动态提醒：%领取悬赏任务",
            $task->getCodeNo(),
            $task->getNames(),
            $this->getUser("fullName")
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        $taskDM->updateTagsAndResolves($task);

        TodoDModel::createTaskTodo($task);

        TodoDModel::updateTaskTodo($task);


        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "领取成功",
            "url" => url('consoles_lists', 'con=task&types=reward'),
            "detailUrl" => url('consoles_task_details', array('con' => 'task', 'id' => $id)),
        ));
    }


    /**
     * 任务管理-删除
     * @param $id
     */
    public function delete($id) {
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id ?: 0);

        if (!$this->isSuper()) {
            if (!$task || $task->getIssueId() != $this->getUser("id")) {
                return $this->ajaxReturn(array("status" => "n", "info" => "只有任务的发布人才能删除任务"));
            }
        }
        $taskDM->remove($task)->flush($task);

        $allotDM = TaskAllotDModel::getInstance();
        $allotDM->name("a")->where("a.tid=$id")->delete();
        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamicDM->name("d")->where("d.tid=$id")->delete();
        $commentDM = TaskCommentDModel::getInstance();
        $commentDM->name("c")->where("c.tid=$id")->delete();

        TodoDModel::removeTaskTodo($task);

        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功！"));
    }


    public function group($pid) {
        $userId = $this->getUser("id");
        $taskDM = TaskDModel::getInstance();
        //发布
        $where1 = "t.issueId = {$userId}";
        //验收
        $where2 = "t.acceptId = {$userId}";
        //执行
        $where3 = "a.userId = {$userId}";

        $where = "t.pid = {$pid} and t.sid = {$this->sid} and ({$where1} or {$where2} or {$where3})";

        $lists = $taskDM->name("t")->select("t,u,u2")
            ->leftJoin("TaskAllot", "a", "t.id=a.tid")
            ->leftJoin("User", "u", "u.id=t.acceptId")
            ->leftJoin("User", "u2", "u2.id=t.issueId")
            ->where($where)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);

        $this->assign("title", $taskDM->getGroupName($pid));
        $this->assign("lists", $lists);

        return $this->display("taskMe/groupLists");
    }


}
