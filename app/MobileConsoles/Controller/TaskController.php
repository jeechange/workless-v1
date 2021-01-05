<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/8/20
 * Time: 15:56
 */

namespace MobileConsoles\Controller;


use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StandardClassifyDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\StudyDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCommentDModel;
use Admin\DModel\TaskCycleDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TaskSettingDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\Task;
use Admin\Entity\TaskSetting;


class TaskController extends CommonController {

    private $menu = "task";

    public function _initialize() {
        parent::_initialize();
        $this->assign("cdnThumbBase", $this->cdnThumbBase);
        $this->assign("isSuper", $this->isSuper());
        $this->assign("menu", $this->menu);
    }

    public function lists() {
        $taskDM = TaskDModel::getInstance();

        $this->assign("tabs_sub", "lobby");
        $this->assign("curTabs", "manageTemp");

        $userId = $this->getUser("id") ?: 0;
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $where = "t.types=2 and t.sid =" . $this->sid;
        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            unset($params['keywords']);
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params, 1);
        }

        if ($whereSelect) {
            $where2 = "t.types=2 and t.sid =" . $this->sid;
            $where = "({$where2}) and {$whereSelect}";
            $this->assign("params", 1);
        }

        $lists = $taskDM->name("t")->select("t,u,u2")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->where($where)->setParameter($params)
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display("listsTemp");
        }

        return $this->success(array(
            "html" => $this->fetch("listsTempItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }

    public function manageReward() {
        $this->assign("tabs_sub", "lobby");
        $this->assign("lobby_tabs", "cycle");
        $this->assign("curTabs", "manageReward");
        $userId = $this->getUser("id") ?: 0;
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $taskDM = TaskDModel::getInstance();
        $where = "t.types=1 and t.sid =" . $this->sid;
        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            unset($params['keywords']);
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params, 1);
        }

        if ($whereSelect) {
            $where2 = "t.types=1 and t.sid =" . $this->sid;
            $where = "({$where2}) and {$whereSelect}";
            $this->assign("params", 1);
        }

        $lists = $taskDM->name("t")->select("t,u")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->where($where)->setParameter($params)
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }

        return $this->success(array(
            "html" => $this->fetch("listsCycleItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }


    public function listsReward() {
        $userId = $this->getUser('id') ?: 0;

        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $this->assign("tabs_sub", "reward");
        $taskDM = TaskDModel::getInstance();
        $where = "t.types=1 and t.sid =" . $this->sid;

        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            unset($params['keywords']);
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params, 1);
        }

        //查询拼接
        if ($whereSelect) {
            $where = "t.types=1 and t.sid =" . $this->sid;
            $where = "({$where}) and {$whereSelect}";
        }

        $lists = $taskDM->name("t")->select("t,(t.nums - t.executor) as tremain")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->where($where)->setParameter($params)
            ->order("tremain", "DESC")
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }

        return $this->success(array(
            "html" => $this->fetch("listsRewardItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }

    public function listsCycle() {
        $userId = $this->getUser('id') ?: 0;
        $this->assign("tabs_sub", "lobby");
        $this->assign("lobby_tabs", "cycle");
        $this->assign("curTabs", "manageCycle");

        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $taskDM = TaskDModel::getInstance();
        $where = "t.types=3 and t.sid =" . $this->sid;

        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            unset($params['keywords']);
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params, 1);
        }

        //查询拼接
        if ($whereSelect) {
            $where = "t.types=3 and t.sid =" . $this->sid;
            $where = "({$where}) and {$whereSelect}";
        }

        $lists = $taskDM->name("t")->select("t,u")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->where($where)->setParameter($params)
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }

        return $this->success(array(
            "html" => $this->fetch("listsCycleItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }

    public function details($id) {
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);
        $this->assign("taskId", $task->getId());

        if (!$task) return $this->display("detailError");
        $this->assign("task", $task);

        $userDM = UserDModel::getInstance();
        $IssueName = $userDM->getUserName($task->getIssueId());
        $acceptName = $userDM->getUserName($task->getAcceptId());
        $this->assign("executorslist", $taskDM->executorsList($task->getExecutors(), $id, "0"));
        $this->assign("IssueName", $IssueName);
        $this->assign("acceptName", $acceptName);

        $this->assign("statusMemo", $taskDM->statusMemo);
        $this->assign("typesTitle", $taskDM->typesMemo[$task->getTypes()]);
        $this->assign("taskTypesMemo", $taskDM->typesMemo[$task->getTypes()]);


        $this->assign("priorityMemo", $taskDM->priorityMemo);
        $taskSettingDM = TaskSettingDModel::getInstance();
        $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();

        $this->assign("lists2", $lists2);
        $this->assign("thumbs", explode(",", $task->getThumbs()));
        $this->assign("cdnThumbBase", $this->cdnThumbBase);

        $taskGroupDM = TaskGroupDModel::getInstance();
        $group = null;
        if ($task->getPid()) {
            $group = $taskGroupDM->find($task->getPid());
        }
        $this->assign("groupName", $group ? $group->getNames() : "");
        $this->assign("groupId", $group ? $task->getPid() : 0);
        $staffDM = StaffDModel::getInstance();
        $accept = $staffDM->workers($this->sid, "", 1);
        $executors = $staffDM->workers($this->sid, "", 20);

        $this->assign("executors", $executors);
        $this->assign("accept", $accept);
        $this->assign("acorns", explode("-", $task->getAcorn()));

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        $dynamics = $dynamicDM->name("d")->where("d.tid={$id}")->order("d.id")->getArray();

        $comments = $commentDM->getComments($id);

        $studyDM = StudyDModel::getInstance();

        $studies = $studyDM->getStudies($task->getLearns());

        $this->assign("studies", $studies);
        $this->assign("dynamics", $dynamics);
        $this->assign("comments", $comments);
        $userId = $this->getUser("id");
        $this->assign("userId", $userId);

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);
        $this->assign("standard", $standard);


        $this->assign("taskStatusMemo", $taskDM->statusMemo[$task->getStatus()]);

        if ($task->getTypes() == 3) {
            $taskCycleDM = TaskCycleDModel::getInstance();
            $cycleInfo = $taskCycleDM->getCycleInfo($task);
            $this->assign("taskCycleMemo", $taskDM->cycleMemo($cycleInfo->getCycleTypes(), $cycleInfo->getCycleStart(), $cycleInfo->getCycleEnd()));

            $codeNo = $task->getCodeNo();
            $lists = $taskDM->name("t")
                ->where("t.codeNo={$codeNo} and t.sid={$task->getSid()}")
                ->order("t.cycleUse")
                ->getArray(true);
            $this->assign("lists", $lists);
        }

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

        if (($task->getIssueId() == $userId || $task->getAcceptId() == $userId) && $task->getStatus() > 0) {
            $allotDM = TaskAllotDModel::getInstance();
            $doneAllots = $allotDM->name("a")->where("a.tid={$id} and a.userId<>{$userId} and a.status>0")->getArray();
            $this->assign("doneAllots", $doneAllots);
        }
        if ($userId != $task->getAcceptId()) {
            $allotDM = TaskAllotDModel::getInstance();
            $recheckCount = $allotDM->name("a")->where("a.tid=" . $task->getId() . " and a.recheckId=$userId")->count();
            $this->assign("recheckCount", $recheckCount);
        }

        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $this->assign("lists1", $lists1);

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
        $this->assign("workloadMemo", $taskDM->getWorkloadMemo($task->getWorkload(), $task->getStandardTypes()));
        if (Q()->get->get("different") == "yes") {
            return $this->display("ExternalRelations:detail");
        }
        return $this->display();
    }

    private function getThumbType($name) {
        $ext = strtolower(substr(strrchr($name, "."), 1));

        if (in_array($ext, array("png", "jpg", "gif", "jpeg", "bmp"))) return "img";

        return "file";
    }

    public function detail($id) {
        $this->assign("tabs_sub", "lobby");
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) return $this->display("detailError");

        $typesTabs = array(
            1 => "reward",
            2 => "temp",
            3 => "cycle",
        );

        $this->assign("lobby_tabs", $typesTabs[$task->getTypes()]);

        $this->assign("task", $task);

        $this->assign("priorityMemo", $taskDM->priorityMemo);
        $taskSettingDM = TaskSettingDModel::getInstance();
        $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();

        $this->assign("lists2", $lists2);
        $this->assign("thumbs", explode(",", $task->getThumbs()));
        $this->assign("cdnThumbBase", $this->cdnThumbBase);

        $taskGroupDM = TaskGroupDModel::getInstance();
        $group = null;
        if ($task->getPid()) {
            $group = $taskGroupDM->find($task->getPid());
        }
        $this->assign("groupName", $group ? $group->getNames() : "");
        $staffDM = StaffDModel::getInstance();
        $accept = $staffDM->workers($this->sid, $task->getAcceptId(), 1, true);
        $executors = $staffDM->workers($this->sid, $task->getExecutors(), 20, true);

        $this->assign("executors", $executors);
        $this->assign("accept", $accept);
        $this->assign("acorns", explode("-", $task->getAcorn()));

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        $dynamics = $dynamicDM->name("d")->where("d.tid=$id")->order("d.id")->getArray();

        $comments = $commentDM->getComments($id);

        $studyDM = StudyDModel::getInstance();

        $studies = $studyDM->getStudies($task->getLearns());

        $this->assign("studies", $studies);
        $this->assign("dynamics", $dynamics);
        $this->assign("comments", $comments);

        $this->assign("userId", $this->getUser("id"));

        return $this->display();
    }

    /**
     * 发布悬赏
     * @return \phpex\Foundation\Response
     */
    public function addReward() {

        $staffDM = StaffDModel::getInstance();

        $taskDM = TaskDModel::getInstance();

        $this->assign("tabs_sub", "lobby");
        $this->assign("lobby_tabs", "reward");

        if (Q()->isGet()) {
            $taskSettingDM = TaskSettingDModel::getInstance();

            $lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();

            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);
            $this->assign("lists1", $lists1);
            $this->assign("lists2", $lists2);

            $accept = $staffDM->workers($this->sid, array(), 1);
            $this->assign("accept", $accept);

            $taskGroupDM = TaskGroupDModel::getInstance();
            $studyDM = StudyDModel::getInstance();
            $groups = $taskGroupDM->getList($this->sid);
            $learns = $studyDM->getList($this->sid);
            $this->assign("groups", $groups);
            $this->assign("learns", $learns);


            $today = $taskDM->getToday(false);

            $this->assign("defaultDeadline", $taskDM->getDefaultDealine($today));

            $this->assign("taskStandards", $this->taskStandardLists());
            $this->assign("today", $taskDM->getToday(false));
            $this->assign("tomorrow", $taskDM->getTomorrow(false));
            $this->assign("week", $taskDM->getWeekTime(false));
            $this->assign("priorityDefault", 4);

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


        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $this->getUser("id");
        $post["acceptId"] = $post["accept"];
        $task = $taskDM->newEntity();

        $taskDM->create($post, $task);

        if (!$taskDM->check($post, $task)) {
            return $this->error($taskDM->getError());
        }

        if (!$post["nums"]) {
            return $this->error("执行人数为必填项");
        }

        $task->setAddTime(nowTime());

        $task->setCodeNo($taskDM->getCodeNo($this->sid));
        $taskDM->add($task)->flush();

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        //TodoDModel::createTaskTodo($task);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

//        $standardDM = StandardDModel::getInstance();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK_REWARD", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->success(url("mobileConsoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success(url("mobileConsoles_taskme_myissue"));

    }

    /**
     * 添加普通
     * @return \phpex\Foundation\Response
     */
    public function addTemp() {
        $staffDM = StaffDModel::getInstance();

        $taskDM = TaskDModel::getInstance();

        if (Q()->isGet()) {
            $taskSettingDM = TaskSettingDModel::getInstance();
            $lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();

            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);
            $this->assign("lists1", $lists1);
            $this->assign("lists2", $lists2);
            $accept = $staffDM->workers($this->sid, array(), 1);
            $executors = $staffDM->workers($this->sid, array(), 20);
            $this->assign("accept", $accept);
            $this->assign("executors", $executors);


            $taskGroupDM = TaskGroupDModel::getInstance();
            $studyDM = StudyDModel::getInstance();
            $groups = $taskGroupDM->getList($this->sid);
            $learns = $studyDM->getList($this->sid);
            $this->assign("groups", $groups);
            $this->assign("learns", $learns);

            $today = $taskDM->getToday(false);

            $this->assign("defaultDeadline", $taskDM->getDefaultDealine($today));
            $this->assign("priorityDefault", 2);
            $this->assign("taskStandards", $this->taskStandardLists());
            $this->assign("today", $taskDM->getToday(false));
            $this->assign("tomorrow", $taskDM->getTomorrow(false));
            $this->assign("week", $taskDM->getWeekTime(false));

            return $this->display();
        }
        $post = Q()->post->all();


        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);

        $executors = explode(",", $post["executors"]);

        $userId = $this->getUser("id");

//        if (in_array($post["accept"], $executors)) {
//            return $this->error("执行人不能包含验收人");
//        }

        $post["types"] = 2;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);

        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $userId;
        $post["acceptId"] = $post["accept"];
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

        $allotDM = TaskAllotDModel::getInstance();

        $allotDM->createAllots($executors, $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::createTaskTodo($task);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($userId);
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

//        $standardDM = StandardDModel::getInstance();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->success(url("mobileConsoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success(url("mobileConsoles_taskme_myissue"));
    }

    /**
     * 添加周期
     * @return \phpex\Foundation\Response
     */
    public function addCycle() {
        $staffDM = StaffDModel::getInstance();

        $taskDM = TaskDModel::getInstance();

        $this->assign("tabs_sub", "lobby");
        $this->assign("lobby_tabs", "cycle");

        if (Q()->isGet()) {
            $taskSettingDM = TaskSettingDModel::getInstance();
            $lists1 = $taskSettingDM->name("t")->where("t.types=1 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();
            $lists2 = $taskSettingDM->name("t")->where("t.types=2 and t.sid=" . $this->sid)->order("t.sort")->setMax(4)->getArray();

            $this->assign("priorityMemo", $taskDM->priorityMemo);
            $this->assign("lists1", $lists1);
            $this->assign("lists2", $lists2);

            $this->assign("cycleTypes", $taskDM->cycleTypesMemo);
            $this->assign("visibilityMemo", $taskDM->visibilityMemo);

            $accept = $staffDM->workers($this->sid, array(), 1);
            $executors = $staffDM->workers($this->sid, array(), 20);
            $this->assign("accept", $accept);
            $this->assign("executors", $executors);

            $taskGroupDM = TaskGroupDModel::getInstance();
            $studyDM = StudyDModel::getInstance();
            $groups = $taskGroupDM->getList($this->sid);
            $learns = $studyDM->getList($this->sid);
            $this->assign("groups", $groups);
            $this->assign("learns", $learns);

            $this->assign("defaultDeadline", $taskDM->getDefaultDealine("+1 month"));

            $this->assign("taskStandards", $this->taskStandardLists());
            $this->assign("priorityDefault", 3);
            return $this->display();
        }
        $post = Q()->post->all();

        $thumbs = array();

        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }

        $post["thumbs"] = json_encode($thumbs);


        $executors = explode(",", $post["executors"]);

        $userId = $this->getUser("id");

//        if (in_array($post["accept"], $executors)) {
//            return $this->error("执行人不能包含验收人");
//        }


        $post["types"] = 3;
        $post["sid"] = $this->sid;
        $post["acorn"] = join("-", $post["acorn"]);

        $post["deadline"] = $post["deadline"] ? new \DateTime($post["deadline"]) : null;
        $post["issueId"] = $userId;
        $post["acceptId"] = $post["accept"];

        if (!$post["cycleTypes"]) {
            return $this->error("请设置周期");
        }

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

        $allotDM = TaskAllotDModel::getInstance();

        $allotDM->createAllots($executors, $task);

        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::createTaskTodo($task);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]发布任务");
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(0);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

//        $standardDM = StandardDModel::getInstance();
//        $addHot = $standardDM->addHot($post['standardId'], $this->sid);
//        if (!$addHot) {
//            return $this->error($standardDM->getError());
//        }
        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("RELEASE_TASK", $taskDM, $task->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->success(url("mobileConsoles_index_sharePage", array("share" => $shareid)));
        }

        return $this->success(url("mobileConsoles_taskme_myissue"));
    }

    /**
     * 领取任务
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function taskReceive($id) {

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

        if ($userId == $task->getIssueId()) {
            return $this->ajaxReturn(array("status" => "n", "info" => "发布人不能领取此任务"));
        }

        $executors = explode(",", $task->getExecutors());
        if (in_array($userId, $executors)) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您已领取此任务，不可重复领取"));
        }

        TaskAllotDModel::getInstance()->createAllot($userId, $task, 3);
        $task->setExecutor($task->getExecutor() + 1);
        $taskDM->save($task)->flush($task);


        $taskDM->updateTagsAndResolves($task); // 更新搜索关键及ID注释
        TodoDModel::createTaskTodo($task);
        TodoDModel::updateTaskTodo($task);


        $message = sprintf("#%d %s 任务动态提醒：%领取悬赏任务",
            $task->getCodeNo(),
            $task->getNames(),
            $this->getUser("fullName")
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        //添加任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->addAllot($task, $userId);
        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "领取成功",
            "url" => url('mobileConsoles_taskreward')
        ));
    }

    public function settings() {

        $this->assign("tabs_sub", "lobby");
        $this->assign("curTabs", "settings");

        $taskSettingDM = TaskSettingDModel::getInstance();
        if (!Q()->isPost()) {
            $lists1 = $taskSettingDM->getLists($this->sid, 1);
            $lists2 = $taskSettingDM->getLists($this->sid, 2);
            $this->assign("cdnThumbBase", $this->cdnThumbBase);
            $this->assign("lists1", $lists1);
            $this->assign("lists2", $lists2);
            $this->assign("typesCount", $taskSettingDM->typesCount);
            return $this->display();
        }

        $post = Q()->post->all();

        $settings1 = $post["settings1"];
        $settings2 = $post["settings2"];
        $icons = $post["images"];
        $lists1 = $taskSettingDM->getLists($this->sid, 1, "object");
        foreach ($lists1 as $item) {
            $item->setNames($settings1[$item->getSort()]);
            $taskSettingDM->save($item)->flush($item);
        }

        $lists2 = $taskSettingDM->getLists($this->sid, 2, "object");

        foreach ($lists2 as $item) {
            $item->setNames($settings2[$item->getSort()]);
            $item->setIcon($icons[$item->getSort()]);
            $taskSettingDM->save($item)->flush($item);
        }
        return $this->success("保存成功");
    }

    public function taskStandardLists() {
        $standardDM = StandardDModel::getInstance();

        return $standardDM->getAddedLists($this->sid, array("Task", "Content", "TrainAndLearn"));
    }

    /**
     * 项目的添加
     * @return \phpex\Foundation\Response
     */
    public function addtaskGroup() {
        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post["names"]) return $this->ajaxReturn(array("status" => "n", "info" => "项目名称必须填写"));
        if (!$post["addTime"]) $post["addTime"] = date("Y-m-d");
        if (!$this->sid) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请先加入一个企业，再进行任务发布等相关操作"));
        }

        $groupDM = TaskGroupDModel::getInstance();
        $group = $groupDM->newEntity();
        $has = $groupDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($has) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务项目名称必须唯一"));
        }
        $group->setNames($post["names"]);
        $group->setAddTime(\DateTime::createFromFormat("Y-m-d", $post["addTime"]));
        $group->setSid($this->sid);
        $group->setMemo($post["memo"]);
        $group->setStatus(1);
        $groupDM->add($group)->flush();

        return $this->ajaxReturn(array("status" => "y", "info" => "新建成功", "url" => url("mobileConsoles_task_taskGroupLists")));
    }

    /**
     * 修改项目
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function groupModify($id) {
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $group = $groupDM->findOneBy(array("sid" => $this->sid, "id" => $id));
        if (!$group) {
            return $this->ajaxReturn(array("status" => "n", "info" => "项目信息获取失败"));
        }
        if (Q()->isGet()) {
            $this->assign("lists", $groupDM->toArray($group));
            return $this->display();
        }
        $post = Q()->post->all();
        if ($post["names"] == $group->getNames()) {
            return $this->ajaxReturn(array("status" => "n", "info" => "项目名称未发生改成"));
        }

        $group->setNames($post["names"]);
        $group->setMemo($post["memo"]);
        $groupDM->save($group)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功", "url" => url("mobileConsoles_task_taskGroupLists")));
    }

    /**
     * 删除项目
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function groupDelete($id) {
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $group = $groupDM->findOneBy(array("sid" => $this->sid, "id" => $id));
        if (!$group) {
            return $this->ajaxReturn(array("status" => "n", "info" => "项目信息获取失败"));
        }
        $groupDM->remove($group)->flush($group);
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_task_taskGroupLists")));
    }

    /**
     * 项目列表
     * @return \phpex\Foundation\Response
     */
    public function taskGroupLists() {
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $search = $this->search();
        $search->addKeyword("g.names", "项目名称");
        $search->build($where, $searchForm, $parameters);

        $lists = $groupDM->name("g")
            ->select("g")
            ->where("g.sid=" . $this->sid . "AND g.status=1")
            ->order("g.id", "DESC")
            ->getArray();
        $this->assign("lists", $lists);
        $this->assign("active", "taskGroup");
        return $this->display();
    }


}