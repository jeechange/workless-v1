<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/9/4
 * Time: 16:24
 */

namespace MobileConsoles\Controller;


use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\RedDotDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\StandardClassifyDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\StudyDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCommentDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TaskSettingDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\Staff;
use Admin\Entity\StaffStation;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\User;

class TaskMeController extends CommonController {
    private $menu = "task";

    public function _initialize() {
        parent::_initialize();
        $this->assign("isSuper", $this->isSuper());
        $this->assign("menu", $this->menu);
    }

    public function myTodo() {
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $userId = $this->getUser("id");

        $this->assign("tabs_sub", "myTodo");
        $allotDM = TaskAllotDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $taskDM = TaskDModel::getInstance();

        $params = array();
        $where = "t.sid = {$this->sid}";
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            $whereSelect = $taskDM->userSelect("primordial", $userId, $this->sid, $where, $params);
        }

        $where = "t.sid = {$this->sid} and a.user_id = {$userId} and a.status = 0";
        $where1 = "t.sid = {$this->sid} and t.status = 1 and t.accept_id = {$userId}";

        $where2_2 = "a.recheck_id = {$userId} and a.accept=2 and t.sid = {$this->sid}";

        $where2 = "({$where}) or ({$where1}) or ({$where2_2})";

        if ($whereSelect) {
            $where2 = "({$where2}) and {$whereSelect}";
            $this->assign("params", 1);
        }

        $fields = "";
        foreach ($metas = $taskDM->getMetas() as $meta) {
            $fields .= " t." . $meta["columnName"] . " as t_" . $meta["columnName"] . ",";
        }

        //优先级
        $this->assign("priorityMemo", $taskDM->priorityMemo);
        $this->assign("typesMemo", $taskDM->typesMemo);
        $this->assign("statusMemo", $taskDM->statusMemo);

        $taskAllotDM = TaskAllotDModel::getInstance();
        foreach ($taskAllotDM->getMetas() as $tav) {
            $fields .= " a." . $tav["columnName"] . " as a_" . $tav["columnName"] . ",";
        }

        $userDM = UserDModel::getInstance();
        foreach ($userDM->getMetas() as $uv) {
            $fields .= " u." . $uv["columnName"] . " as u_" . $uv["columnName"] . ",";
        }

        $fields = trim($fields, ",");

        $sql = "select " . $fields
            . " from __TASK__ t "
            . " left join __TASK_ALLOT__ a on t.id=a.tid "
            . " left join __USER__ u on u.id=t.issue_id "
            . " where {$where2} "
            . " ORDER BY FIELD(t.status,1,0,3,2)"
            . " limit {$offset},{$this->listsSize}";
        DM()->execute($sql, $params);
        $stat = DM()->getLastStatement();
        $lists = $stat->fetchAll();

        foreach ($lists as &$v) {
            $v["executorMemo"] = $taskDM->executorMemo($v["t_executors"]);
            $v["groupName"] = $taskDM->getGroupName($v["t_pid"]);
        }

        $this->assign("userId", $userId);

        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));

        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }

        return $this->success(array(
            "html" => $this->fetch("myTodoItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }

    public function myAccept() {
        $userId = $this->getUser('id') ?: 0;

        $this->assign("tabs_sub", "myAccept");
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";

        $taskDM = TaskDModel::getInstance();
        $where = "(t.acceptId = $userId or REGEXP(t.rechecks,'(^|\,)(" . $userId . ")(\,|$)')=1 ) and t.sid = {$this->sid}";

        $params = array();

        if ($keywords) {
            $where .= " and (t.names  LIKE :search or t.tags LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
        }

        $lists = $taskDM->name("t")->select("t")
            ->where($where)
            ->setParameter($params)
            ->order("t.status", "ASC")
            ->order("t.acceptTime", "DESC")
            ->order("t.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);

//        $lists = $taskDM->name("t")->select("t,a,u,u2")
//            ->leftJoin("TaskAllot", 'a', "t.id=a.tid")
//            ->leftJoin("User", "u", "u.id=t.issueId")
//            ->leftJoin("User", "u2", "u2.id=t.acceptId")
//            ->where($where)->setParameter($params)
//            ->order("t.acceptTime", "DESC")
//            ->order("t.id", "DESC")
//            ->limit($offset, $this->listsSize)
//            ->getArray(true);
        $this->assign("userId", $userId);
        $this->assign("lists", $lists);
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));
        if (!Q()->headers->has("onrefreshorinfinite")) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }
        return $this->success(array(
            "html" => $this->fetch("myAcceptItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));

    }

    public function acceptDetail($id) {
        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'TaskCBA');

        $this->assign("tabs_sub", "myAccept");
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) return $this->display("Task/detailError");

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

    public function acceptAction($id) {
        $this->assign("tabs_sub", "myAccept");
        $taskDM = TaskDModel::getInstance();

        $allotDM = TaskAllotDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);
        $userId = $this->getUser("id");
        if (!$task) return $this->display("Task/detailError");

        if ($this->getUser("id") != $task->getAcceptId()) {
            $allots = $allotDM->name("a")->where("a.tid=" . $task->getId() . " and a.recheckId=$userId")->getArray();
            if (!$allots) return $this->display("Task/detailError");
            $this->assign("recheckAllots", $allots);
        } else {
            $allots = $allotDM->name("a")->where("a.tid=$id")->order("a.addTime", "desc")->getArray();
            $this->assign("allots", $allots);
        }

        $this->assign("task", $task);
        $taskSettingDM = TaskSettingDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);
        $this->assign("standard", $standard);

        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $lists2 = $taskSettingDM->getLists($this->sid, 2);
        $lists3 = $taskSettingDM->getLists($this->sid, 3);
        $lists4 = $taskSettingDM->getLists($this->sid, 4);
        $lists5 = $taskSettingDM->getLists($this->sid, 5);


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

        $this->assign("workloadMemo", $taskDM->getWorkloadMemo($task->getWorkload(), $task->getStandardTypes()));


        $sid = $task->getSid();

        $staffDM = StaffDModel::getInstance();
        $myMaxAcorn = $staffDM->getMaxAcorn($userId, $sid);
        $this->assign("myMaxAcorn", $myMaxAcorn);
        return $this->display();

    }


    /**
     * 验收人执行：验收任务通过
     * @return \phpex\Foundation\Response
     */
    public function acceptAllot() {
        $post = Q()->post->all();
        $post["eachAcorn"] = $post["hard"];
        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */

        $allot = $allotDM->find($post["aid"] ?: 0);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }
        if ($allot->getAccept() != 0) {
            return $this->ajaxReturn(array("status" => "n", "info" => "不能重复验收"));
        }
        if ($allot->getStatus() == 0) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务未完成，不能验收"));
        }

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($allot->getTid());

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));
        }

        $userId = $this->getUser("id");
        if ($task->getAcceptId() != $userId) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是验收人，不能进行此操作"));
        }

        $taskSettingDM = TaskSettingDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);

        if ($task->getStandardTypes() != 2) {

            if ($post["day"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["day"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务量天数不正确"));
            }
            if ($post["aday"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["aday"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时天数不正确"));
            }

            $wDay = $post["day"] + ($post["hour"] / 8) + ($post["minute"] / 480);
            $aDay = $post["aday"] + ($post["ahour"] / 8) + ($post["aminute"] / 480);
            if ($wDay > 0 && $aDay <= 0) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务量大于0时必须填写耗时"));
            }
        }

        if (!$standard || $standard->getAcorn() == 0) {
            if ($allot->getStatus() == 1 || $wDay > 0) {

                $lists3 = $taskSettingDM->getLists($this->sid, 3);
                $lists4 = $taskSettingDM->getLists($this->sid, 4);
                $lists5 = $taskSettingDM->getLists($this->sid, 5);

                if (!isset($post["quality"]) || $post["quality"] < $lists5[0]["names"] || $post["quality"] > $lists5[1]["names"]) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "完成质量参数不正确"));
                }
                if ($task->getStandardTypes() != 2) {
                    if (!isset($post["hard"]) || $post["hard"] < $lists4[0]["names"] || $post["hard"] > $lists4[1]["names"]) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "难度系数参数不正确"));
                    }

                    $base = $lists3[0]["names"];
                    $post["acorn"] = round($base * $wDay * $post["hard"] * $post["quality"] / 100);
                } else {
                    if (!isset($post["eachAcorn"]) || !preg_match("#^(0|[1-9][0-9]*)(\.[0-9]*)?$#", $post["eachAcorn"]) || $post["eachAcorn"] <= 0) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "每件积分不正确"));
                    }
                    if (!isset($post["workloadNum"]) || !preg_match("#^[1-9][0-9]*$#", $post["workloadNum"])) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "核准数量不正确"));
                    }
                    $post["acorn"] = round($post["eachAcorn"] * $post["workloadNum"] * $post["quality"] / 100);
                }
            } else {
                $post["acorn"] = 0;
            }
        } elseif ($allot->getStatus() == 1) {
            if ($post["acorn"] <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写实得分数"));
            if ($wDay <= 0 && !$post["workloadNum"]) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
        }

        $standardClassifyDM = StandardClassifyDModel::getInstance();

        $classify = $standardClassifyDM->findOneBy(array("namesEn" => "Task"));

        if (!$classify) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
        }
        if (!$standard) {
            $finds = array("names" => "任务执行", "sid" => $this->sid, "classify" => $classify->getId());
            $standard = $standardDM->findOneBy($finds);
            if (!$standard) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
            }
        }


        if (!isset($post["rating"])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请对完成情况进行评价"));
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($allot->getUserId() ?: 0);

        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行人获取信息异常，请刷新页面重试"));
        }

        if ($task->getStandardTypes() != 2) {

            $allot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"])));
            $allot->setWorkload(join(",", array($post["day"], $post["hour"], $post["minute"])));
            $allot->setAcceptHard($post["hard"]);
        } else {
            $allot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["allotNum"])));
            $allot->setWorkload(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["workloadNum"])));
            $allot->setAcceptHard($post["eachAcorn"]);
        }
        $allot->setAcceptQuality($post["quality"]);
        $allot->setAcorn($post["acorn"]);
        $allot->setRating($post["rating"]);
        $allot->setMedal($post["medal"]);
        $allot->setLearns(json_encode($post["learns"]));

        $allot->setAccept(1);
        $allot->setAcceptTime(nowTime());
        $allotDM->save($allot)->flush($allot);

        $taskDM->updateAcceptTime($task);
        $acornDM = AcornDModel::getInstance();


        if ($post["acorn"] > 0) {

            $acornDM->addAcorn($this->sid, $allot->getUserId(), $userId, $userId, $classify->getId(), $standard->getId(), $post["acorn"], "完成" . $task->getNames() . "任务", $task->getCodeNo());
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $allot->getUserId(), $this->sid);

            if ($wDay > 0 && $task->getStandardTypes() != 2) {
                $lists3 = $taskSettingDM->getLists($this->sid, 3);
                $base = $lists3[0]["names"];
                $baseScale = $lists3[1]["names"] / 100;
                $acceptAcorn = round($wDay * $base * $baseScale) ?: 1;
                $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                $acornDM->addAcorn($this->sid, $userId, $userId, $userId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());

                //更新验收统计记录
                $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
            } elseif ($post["workloadNum"] > 0 && $task->getStandardTypes() == 2) {

                $lists6 = $taskSettingDM->getLists($this->sid, 6);
                $baseScale = $lists6[0]["names"] / 100;
                $acceptAcorn = round($post["acorn"] * $baseScale) ?: 1;
                $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                $acornDM->addAcorn($this->sid, $task->getAcceptId(), $userId, $userId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());
                //更新验收统计记录
                $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
            }
        } else {
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
        }

        $acorn = $post["acorn"] ?: 0;

        $memo = $post["memo"] ? "，验收说明:" . $post["memo"] : "";
        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息][{$user->getFullName()}]执行的任务通过验收，获取{$acorn}积分" . $memo);
        $dynamic->setTid($allot->getTid());
        $dynamic->setUserId($userId);
        $dynamic->setRuserId(0);
        $dynamic->setTypes(1);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();


        $taskSetting = $taskSettingDM->find($post["rating"] ?: 0);


        $message = sprintf("#%d %s 任务动态提醒：已完成验收，完成质量：%s，执行人[%s]获得%d分%s%s",
            $task->getCodeNo(),
            $task->getNames(),
            $post["quality"] . "%",
            $user->getFullName(),
            $acorn,
            $taskSetting ? sprintf("【%s】", $taskSetting->getNames()) : "",
            $post["memo"] ? ",附言：" . $post["memo"] : ""
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);


        $url = url("consoles_task_details", "id=" . $allot->getTid());

        $params = array(
            "tid" => $task->getId(),
        );

        if ($task->getStatus() != 2) {
            $allotNoAccept = $allotDM->name("a")->where("a.tid=:tid and (a.accept=0 or a.accept is null or a.accept=2)")->setParameter($params)->count();
            if (!$allotNoAccept) {
                $task->setStatus(3);
                // $taskDM = TaskDModel::getInstance();
                $taskDM->save($task)->flush($task);
                TodoDModel::doneAcceptTask($userId, $task);
            }
        }

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));

    }


    public function myIssue() {
        $userId = $this->getUser('id') ?: 0;
        $this->assign("tabs_sub", "myIssue");
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";
        $taskDM = TaskDModel::getInstance();
        $params = array();
        $where = "t.issueId=$userId and t.sid =" . $this->sid;
        if ($keywords) {
            $where .= " and (t.names LIKE :keywords or t.tags LIKE :keywords)";
            $params["keywords"] = "%" . $keywords . "%";
        }

        $lists = $taskDM->name("t")
            ->select("t")
            ->where($where)
            ->setParameter($params)
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
            "html" => $this->fetch("myIssueItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }


    public function issueDetail($id) {

        $this->assign("tabs_sub", "myIssue");
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task || $task->getIssueId() != $this->getUser("id")) return $this->display("Task/detailError");

        $typesTabs = array(
            1 => "reward",
            2 => "temp",
            3 => "cycle",
        );
        $this->assign("lobby_tabs", $typesTabs[$task->getTypes()]);
        $this->assign("task", $task);
        $this->assign("titles", $taskDM->typesMemo[$task->getTypes()]);


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
        $accept = $staffDM->workers($this->sid, $task->getAcceptId(), 1);
        $executors = $staffDM->workers($this->sid, $task->getExecutors(), 20);

        $this->assign("executors", $executors);
        $this->assign("accept", $accept);
        $this->assign("acorns", explode("-", $task->getAcorn()));

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();


        $dynamics = $dynamicDM->name("d")->where("d.tid=$id")->order("d.id")->getArray();

        if ($dynamics) {
            foreach ($dynamics as &$dynamic) {
                $dynamic["thumbs"] = explode(",", $dynamic["thumbs"]);
            }
        }


        $comments = $commentDM->getComments($id);

        $studyDM = StudyDModel::getInstance();

        $studies = $studyDM->getStudies($task->getLearns());

        $this->assign("studies", $studies);

        $this->assign("dynamics", $dynamics);
        $this->assign("comments", $comments);

        $this->assign("userId", $this->getUser("id"));

        return $this->display();

    }

    public function todoDetail($id) {
        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'Task');

        $this->assign("tabs_sub", "myTodo");
        $taskDM = TaskDModel::getInstance();
        $task = $taskDM->find($id);

        $executors = explode(",", $task->getExecutors());
        if (!$task || !in_array($this->getUser("id"), $executors)) return $this->display("Task/detailError");

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
        $accept = $staffDM->workers($this->sid, $task->getAcceptId(), 1);
        $executors = $staffDM->workers($this->sid, $task->getExecutors(), 20);

        $this->assign("executors", $executors);
        $this->assign("accept", $accept);
        $this->assign("acorns", explode("-", $task->getAcorn()));

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        $dynamics = $dynamicDM->name("d")->where("d.tid=$id")->order("d.id")->getArray();

        if ($dynamics) {
            foreach ($dynamics as &$dynamic) {
                $dynamic["thumbs"] = $dynamic["thumbs"] ? explode(",", $dynamic["thumbs"]) : array();
            }
        }

        $comments = $commentDM->getComments($id);

        $studyDM = StudyDModel::getInstance();

        $studies = $studyDM->getStudies($task->getLearns());

        $this->assign("studies", $studies);

        $this->assign("dynamics", $dynamics);
        $this->assign("comments", $comments);

        $this->assign("userId", $this->getUser("id"));

        return $this->display();

    }

    public function taskCancel($id) {
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task || $task->getIssueId() != $this->getUser("id")) {
            return $this->error("只有任务的发布人才能取消任务");
        }
        if ($task->getStatus() != 0) {
            return $this->error("执行中的任务才能取消");
        }
        $task->setStatus(2);

        $taskDM->save($task)->flush($task);

        $allotDM = TaskAllotDModel::getInstance();


        $allotDM->name("a")->where("a.tid=$id and a.status=0")->update(array("a.status" => 3));

        $post = Q()->post->all();

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

        return $this->success("取消成功");

    }

    public function taskRemove() {

        $id = Q()->post->get("taskId");

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id ?: 0);

        if (!$this->isSuper()) {
            if (!$task || $task->getIssueId() != $this->getUser("id")) {
                return $this->error("只有任务的发布人才能删除任务");
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

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "删除成功",
            "url" => url("mobileConsoles_taskme_myissue")
        ));
    }


    public function taskDone($id) {


        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task) {
            return $this->error("任务信息获取失败");
        }
        if ($task->getStatus() != 0) {
            return $this->error("执行中的任务才能操作");
        }

        $executors = explode(",", $task->getExecutors());
        $userId = $this->getUser("id");

        if (!in_array($userId, $executors)) {
            return $this->error("您未参与此任务，不能进行此操作");
        }
        $startTime = $task->getTypes() < 3 ? totime($task->getAddTime()) : $taskDM->getCycleTime($task, 0);
        $endTime = $task->getTypes() < 3 ? totime($task->getDeadline()) : $taskDM->getCycleTime($task, 1);


        $allotDM = TaskAllotDModel::getInstance();

        $allot = $allotDM->getAllot($userId, $task, $startTime, $endTime);
        if ($allot->getStatus() != 0) {
            return $this->error("执行中的任务才能操作");
        }

        $allot->setStatus(1);

        $allotDM->save($allot)->flush($allot);

        return $this->success("操作成功");
    }

    public function acceptDone($id) {

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task) {
            return $this->error("任务信息获取失败");
        }
        if ($task->getAcceptId() != $this->getUser("id")) {
            return $this->error("只有任务的验收人才能操作");
        }

        $task->setEndTime(nowTime());
        $task->setStatus(1);

        $allotDM = TaskAllotDModel::getInstance();

        $allotDM->name("a")->where("a.tid=$id and a.status=0")->update(array("a.status" => 1));

        $taskDM->save($task)->flush($task);
    }

    public function redeploy($id) {
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task) {
            return $this->error("任务信息获取失败");
        }
        if ($task->getStatus() != 0) {
            return $this->error("执行中的任务才能操作");
        }

        $executors = explode(",", $task->getExecutors());
        $userId = $this->getUser("id");

        if (!in_array($userId, $executors)) {
            return $this->error("您未参与此任务，不能进行此操作");
        }


        $startTime = $task->getTypes() < 3 ? totime($task->getAddTime()) : $taskDM->getCycleTime($task, 0);
        $endTime = $task->getTypes() < 3 ? totime($task->getDeadline()) : $taskDM->getCycleTime($task, 1);


        $allotDM = TaskAllotDModel::getInstance();

        $allot = $allotDM->getAllot($userId, $task, $startTime, $endTime);

        if ($allot->getStatus() != 0) {
            return $this->error("执行中的任务才能操作");
        }


        $allot->setStatus(2);

        $allotDM->save($allot)->flush($allot);

        return $this->success("操作成功");
    }

    public function addDynamic($id) {

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);

        $userId = $this->getUser("id");

        $executors = explode(",", $task->getExecutors());

        if (!$task || ($task->getIssueId() != $userId && $task->getAcceptId() != $userId && !in_array($userId, $executors))) {
            return $this->error("你不是参与人员，不能发表任务动态");
        }

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

        $message = sprintf("#%d %s 任务动态提醒：%s说 \"%s\"", $task->getCodeNo(), $task->getNames(), $this->getUser("fullName"), $post["content"]);
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        return $this->success("发布成功");
    }

    public function taskAllot($id) {

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task || $task->getIssueId() != $this->getUser("id")) {
            return $this->error("任务发布人才能指派人员");
        }
        if ($task->getStatus() != 0) {
            return $this->error("任务已经取消，不需要指派人员");
        }

        $post = Q()->post->all();

        $executors = explode(",", $post["executors"]);


        $startTime = $task->getTypes() < 3 ? totime($task->getAddTime()) : $taskDM->getCycleTime($task, 0);
        $endTime = $task->getTypes() < 3 ? totime($task->getDeadline()) : $taskDM->getCycleTime($task, 1);
        $allotDM = TaskAllotDModel::getInstance();
        $isAllot = $allotDM->isAllot($task, $executors, 0);
        if ($isAllot) {
            return $this->ajaxReturn(array("status" => "n", "info" => $isAllot));
        }
        $userDM = UserDModel::getInstance();

        $users = array();
        $userIds = array($this->getUser("id"), $task->getAcceptId());
        foreach ($executors as $executor) {

            /** @var User $user */
            $user = $userDM->find($executor ?: 0);
            if (!$user) continue;

            $allot = $allotDM->getAllot($executor, $task, $startTime, $endTime);
            if ($allot) {
                $users[] = $user->getFullName();
                $userIds[] = $executor;
            }
        }
        if ($users) {
            $dynamicDM = TaskDynamicDModel::getInstance();
            $dynamic = $dynamicDM->newEntity();
            $dynamic->setContent(sprintf("[系统消息]指派了新成员【%s】", join(",", $users)));
            $dynamic->setTid($task->getId());
            $dynamic->setUserId($this->getUser("id"));
            $dynamic->setRuserId(0);
            $dynamic->setTypes(2);
            $dynamic->setAddTime(nowTime());
            $dynamicDM->add($dynamic)->flush();
        }

        $message = sprintf("#%d %s 任务动态提醒：%s指派了新成员[%s]", $task->getCodeNo(), $task->getNames(), $this->getUser("fullName"), join(",", $users));
        CompanyOpenapiDModel::sendMessage($task, $message, $userIds);

        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功"));
    }


    public function commentDynamic($id) {

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($id);
        if (!$task) {
            return $this->error("记录获取失败");
        }
        $post = Q()->post->all();
        if (trim($post["content"]) == "") return $this->ajaxReturn(array("status" => "n", "info" => "请输入评论/回复内容"));

        $commentDM = TaskCommentDModel::getInstance();


        $comment = $commentDM->newEntity();

        $comment->setTid($id);
        $comment->setAid($post["dynamicId"]);
        $comment->setUserId($this->getUser("id"));
        $comment->setReplyId($post["userId"]);
        $comment->setContent($post["content"]);
        $comment->setAddTime(nowTime());
        $commentDM->add($comment)->flush();
        return $this->success("回复成功");

    }

    public function dynamicAction($id) {
        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */

        $task = $taskDM->find($id);

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $post = Q()->post->all();

        $url = url("mobileConsoles_task_details", "id=$id");

        if ($task->getStatus() != 0) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行中的任务才能操作"));
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
        if ($task->getIssueId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的发布人"));
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
            if ($users) {
                $dynamicDM = TaskDynamicDModel::getInstance();
                $dynamic = $dynamicDM->newEntity();
                $dynamic->setContent(sprintf("[系统消息]指派了新成员[%s]", join(",", $users)));
                $dynamic->setTid($task->getId());
                $dynamic->setUserId($this->getUser("id"));
                $dynamic->setRuserId(0);
                $dynamic->setTypes(2);
                $dynamic->setAddTime(nowTime());
                $dynamicDM->add($dynamic)->flush();
            }

            $message = sprintf("#%d %s 任务动态提醒：%s指派了新成员[%s]", $task->getCodeNo(), $task->getNames(), $this->getUser("fullName"), join(",", $users));
            CompanyOpenapiDModel::sendMessage($task, $message, $userIds);
            $taskDM = TaskDModel::getInstance();
            $taskDM->updateTagsAndResolves($task);
            TodoDModel::createTaskTodo($task);
            return $this->ajaxReturn(array(
                "status" => "y",
                "info" => "操作成功",
                "url" => $url
            ));
        }

        $task->setStatus(2);
        $taskDM = TaskDModel::getInstance();
        $taskDM->save($task)->flush($task);

        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $this->getUser('id'), $post["types"], $post["executors1"]);

        $allotDM = TaskAllotDModel::getInstance();
        $allotDM->name("a")->where("a.tid={$task->getId()} and a.status=0")->update(array("a.status" => 3));


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

            if ($redeployNum > 0 && $acceptDay = "0,0,0") {
                return $this->ajaxReturn(array("status" => "n", "info" => "完成数量大于0时，耗时不能为0"));
            }

            if ($task->getStandardTypes() == 2 && $redeployNum == 0 && $acceptDay != "0,0,0") {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时大于0时，请填写完成数量"));
            }

            if ($task->getStandardTypes() == 2) {
                $acceptDay .= "," . $redeployNum;
            }

            if (!$post["redeployMember"]) {
                return $this->ajaxReturn(array("status" => "n", "info" => "请选择执行人员"));
            }
            $isAllot = $allotDM->isAllot($task, $post["redeployMember"], $this->getUser("id"));
            if ($isAllot) {
                return $this->ajaxReturn(array("status" => "n", "info" => $isAllot));
            }

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

        $params["addTime"] = now();


        $myExecAllotCount = $allotDM->name("a")->where("a.tid=:tid and a.userId=:userId and a.status=0 and a.addTime<=:addTime")->setParameter($params)->count();
        if (!$myExecAllotCount) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您已完成任务或已经转派他人！"));
        }


        $updateParams = array("a.status" => $post["types"] == "redeploy" ? 2 : 1, "a.doneTime" => "'{$params["addTime"]}'", "a.acceptDay" => "'{$acceptDay}'");

        if ($acceptDay == "0,0,0" || $acceptDay == "0,0,0,0") {
            $updateParams["a.accept"] = 1;
            $updateParams["a.workload"] = "'0,0,0,0'";
        }


        $allotDM->name("a")->where("a.tid=:tid and a.userId=:userId and a.status=0 and a.addTime<=:addTime")->setParameter($params)
            ->update($updateParams);
        if ($post["types"] == "redeploy") {
            $users = $allotDM->createAllots($post["redeployMember"], $task, $this->getUser("id"), 2);
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
            $executors2 = explode(",", $post["redeployMember"]);
            CompanyOpenapiDModel::sendMessage($task, $message, array_merge($userIds, $executors2));
            $taskDM = TaskDModel::getInstance();
            $taskDM->updateTagsAndResolves($task);
            TodoDModel::createTaskRedeployTodo($task, $this->getUser("id"), $post["redeployMember"]);

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

            TodoDModel::createTaskDoneTodo($task, $this->getUser("id"));
        }

        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $this->getUser('id'), $post["types"], $post["redeployMember"]);

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent($content);
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes($post["types"] == "redeploy" ? 2 : 3);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();


        if ($post["types"] == "done") {
            unset($params["userId"]);
            $allotNoDone = $allotDM->name("a")->where("a.tid=:tid and a.status=0 and a.addTime<=:addTime")->setParameter($params)->count();
            if (!$allotNoDone) {
                $task->setStatus(1);
                $taskDM = TaskDModel::getInstance();
                $taskDM->save($task)->flush($task);
            }
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
        $allotDM->name("a")->where("a.tid={$task->getId()} and a.status=0")->update(array("a.status" => 1, "a.doneTime" => "'{$nowTime}'"));
        $taskDM = TaskDModel::getInstance();

        $task->setStatus(3);
        $task->setEndTime(nowTime());
        $taskDM->save($task)->flush($task);

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


    public function myExecute() {
        $userId = $this->getUser('id') ?: 0;
        $this->assign("tabs_sub", "myExecute");
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";


        $where = "t.sid =" . $this->sid . " and a.userId =$userId";

        $params = array();

        if ($keywords) {
            $where .= " and (t.names LIKE :keywords or t.tags LIKE :keywords)";
            $params["keywords"] = "%" . $keywords . "%";
        }


        $allotDM = TaskAllotDModel::getInstance();
        $allotDM->setMeId($this->getUser("id"));

        $lists = $allotDM->name("a")->select("a,t")
            ->leftJoin("Task", "t", "t.id=a.tid")
            ->where($where)
            ->setParameter($params)
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
        $myStatusMemo = function ($item) {
            return $item["statusMemo"];
        };
        $this->assign("myStatusMemo", $myStatusMemo);

        return $this->success(array(
            "html" => $this->fetch("myExecuteItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));
    }


    public function executeDetail($id) {
        $this->assign("tabs_sub", "myExecute");
        $taskDM = TaskDModel::getInstance();
        $task = $taskDM->find($id);

        $executors = explode(",", $task->getExecutors());
        if (!$task || !in_array($this->getUser("id"), $executors)) return $this->display("Task/detailError");

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
        $accept = $staffDM->workers($this->sid, $task->getAcceptId(), 1);
        $executors = $staffDM->workers($this->sid, $task->getExecutors(), 20);

        $this->assign("executors", $executors);
        $this->assign("accept", $accept);
        $this->assign("acorns", explode("-", $task->getAcorn()));

        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();

        $dynamics = $dynamicDM->name("d")->where("d.tid=$id")->order("d.id")->getArray();

        if ($dynamics) {
            foreach ($dynamics as &$dynamic) {
                $dynamic["thumbs"] = $dynamic["thumbs"] ? explode(",", $dynamic["thumbs"]) : array();
            }
        }

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
     * 验收人操作，审核不通过
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function acceptAllotNoPass() {
        $id = Q()->post->get("dataId");

        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */
        $allot = $allotDM->find($id ?: 0);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        if ($allot->getAccept() != 0) {
            return $this->ajaxReturn(array("status" => "n", "info" => "不能重复验收"));
        }
        if ($allot->getStatus() != 1) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务未完成，不能验收"));
        }

        $taskDM = TaskDModel::getInstance();

        /** @var \Admin\Entity\Task $task */

        $task = $taskDM->find($allot->getTid());

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));
        }

        $userId = $this->getUser("id");
        if ($task->getAcceptId() != $userId) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是验收人，不能进行此操作"));
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($allot->getUserId() ?: 0);

        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行人获取信息异常，请刷新页面重试"));
        }

        $post = Q()->post->all();
        if (!$post["memo"]) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写不通过的验收说明"));
        }


        $memo = "，验收说明:" . $post["memo"];


        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息][{$user->getFullName()}]执行的任务未通过验收，请重新执行，确认完成任务后再次交付" . $memo);
        $dynamic->setTid($allot->getTid());
        $dynamic->setUserId($userId);
        $dynamic->setRuserId(0);
        $dynamic->setTypes(1);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        $allot->setStatus(0);
        $allot->setDoneTime(null);
        $allotDM->save($allot)->flush();

        $task->setStatus(0);
        // $taskDM = TaskDModel::getInstance();
        $taskDM->save($task)->flush($task);

        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $allot->getUserId(), "noPass");

        $message = sprintf("#%d %s 任务动态提醒：任务未通过验收，执行人[%s]请重新执行，附言：%s",
            $task->getCodeNo(),
            $task->getNames(),
            $user->getFullName(),
            $post["memo"]
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        TodoDModel::NoPassTask($userId, $allot->getUserId(), $task);

        $url = url("consoles_task_details", "id=" . $allot->getTid());

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }

    public function acceptAllotUp($id) {
        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($id);

        $userId = $this->getUser("id");


        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));
        }
        if ($task->getAcceptId() != $userId) {

            $allotDM = TaskAllotDModel::getInstance();
            /** @var TaskAllot $allot */
            $allot = $allotDM->find(Q()->post->get("dataId") ?: 0);
            if (!$allot || $allot->getTid() != $id || $allot->getRecheckId() != $userId) {
                return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的验收人"));
            }
        }
        $acorn = Q()->post->get("acorn");
        $staffStationDM = StaffStationDModel::getInstance();

        $lists = $staffStationDM->name("ss")->select("s,ss,d")
            ->innerJoin("Staff", "s", "s.station=ss.id")
            ->leftJoin("Department", "d", "d.id=ss.department")
            ->where("ss.sid={$this->sid} and (ss.riseAcorn>={$acorn} or ss.limitAcorn=0)")
            ->getarray(true);

        $sid = $task->getSid();
        $staffDM = StaffDModel::getInstance();
        $myMaxAcorn = $staffDM->getMaxAcorn($userId, $sid);

        $this->assign("myMaxAcorn", $myMaxAcorn);

        $this->assign("posts", Q()->post->all());
        $this->assign("lists", $lists);
        return $this->ajaxReturn(array("status" => "y", "sid" => $this->sid, "html" => $this->fetch()));
    }

    public function acceptAllotUpSubmit() {

        $post = Q()->post->all();

        $dataId = $post["aid"];

        $taskAllotDM = TaskAllotDModel::getInstance();
        /** @var TaskAllot $taskAllot */
        $taskAllot = $taskAllotDM->find($dataId ?: 0);
        if (!$taskAllot || $taskAllot->getAccept() != 0 || !in_array($taskAllot->getStatus(), array(1, 2))) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务执行信息获取失败"));
        }

        $taskDM = TaskDModel::getInstance();
        /** @var Task $task */
        $task = $taskDM->find($taskAllot->getTid() ?: 0);

        $userId = $this->getUser("id");

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务执行信息获取失败"));
        }
        if ($task->getAcceptId() != $userId) {
            if ($taskAllot->getRecheckId() != $userId) {
                return $this->ajaxReturn(array("status" => "n", "info" => "您不是任务的验收人或审核人"));
            }
        }

        $staffDM = StaffDModel::getInstance();
        /** @var Staff $staff */

        $staff = $staffDM->findOneBy(array("sid" => $this->sid, "id" => $post["id"]));

        if (!$staff || $staff->getStatus() == 3) {
            return $this->ajaxReturn(array("status" => "n", "info" => "审核人不存在或者已经离职"));
        }

        $staffStationDM = StaffStationDModel::getInstance();

        /** @var StaffStation $station */

        $station = $staffStationDM->find($staff->getStation() ?: 0);

        if (!$station || $station->getSid() != $this->sid || $station->getStatus() != 1) {
            return $this->ajaxReturn(array("status" => "n", "info" => "审核人职位异常"));
        }

        if ($station->getLimitAcorn() && $post["acorn"] > $station->getRiseAcorn()) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您选择的审核人不满足审核条件"));
        }
        if ($post["day"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["day"])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务量天数不正确"));
        }

        $wDay = $post["day"] + ($post["hour"] / 8) + ($post["minute"] / 480);

        if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));

        $taskSettingDM = TaskSettingDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);

        if (!$standard || $standard->getAcorn() == 0) {
            $lists3 = $taskSettingDM->getLists($this->sid, 3);
            $lists4 = $taskSettingDM->getLists($this->sid, 4);
            $lists5 = $taskSettingDM->getLists($this->sid, 5);

            if (!isset($post["hard"]) || $post["hard"] < $lists4[0]["names"] || $post["hard"] > $lists4[1]["names"]) {
                return $this->ajaxReturn(array("status" => "n", "info" => "难度系数参数不正确"));
            }
            if (!isset($post["quality"]) || $post["quality"] < $lists5[0]["names"] || $post["quality"] > $lists5[1]["names"]) {
                return $this->ajaxReturn(array("status" => "n", "info" => "完成质量参数不正确"));
            }
            $base = $lists3[0]["names"];
            $post["acorn"] = round($base * $wDay * $post["hard"] * $post["quality"] / 100);
        }

        if (!isset($post["rating"])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请对完成情况进行评价"));
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($taskAllot->getUserId() ?: 0);

        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行人获取信息异常，请刷新页面重试"));
        }

        $taskAllot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"])));
        $taskAllot->setWorkload(join(",", array($post["day"], $post["hour"], $post["minute"])));
        $taskAllot->setAcceptHard($post["hard"]);
        $taskAllot->setAcceptQuality($post["quality"]);
        $taskAllot->setAcorn($post["acorn"]);
        $taskAllot->setRating($post["rating"]);
        $taskAllot->setMedal($post["medal"]);
        $taskAllot->setLearns(json_encode($post["learns"]));

        $taskAllot->setAccept(2);
        $taskAllot->setRecheckId($staff->getUserId());
        $taskAllotDM->save($taskAllot)->flush($taskAllot);

        TodoDModel::submitRecheckTask($staff->getUserId(), $task);
        $taskDM->updateTagsAndResolves($task);

        $message = sprintf("#%d %s 任务动态提醒：验收人[%s]已将任务提交给[%s]验收审核，附言：%s",
            $task->getCodeNo(),
            $task->getNames(),
            $this->getUser("fullName"),
            $staff->getFullName(),
            $post["memo"]
        );
        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息]" . $message);
        $dynamic->setTid($taskAllot->getTid());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(1);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        CompanyOpenapiDModel::sendMessage($task, $message, array($user->getId(), $this->getUser("id"), $staff->getUserId()));

        // $url = url("consoles_task_details", "id=" . $taskAllot->getTid());

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            // "url" => $url
        ));
    }

    public function acceptAllotRecheck() {
        $allotDM = TaskAllotDModel::getInstance();

        $post = Q()->post->all();


        $id = $post["aid"];
        /** @var TaskAllot $allot */

        $allot = $allotDM->find($id);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        if ($allot->getAccept() != 2) {
            return $this->ajaxReturn(array("status" => "n", "info" => "不能重复验收"));
        }

        if ($allot->getStatus() == 0) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务未完成，不能验收"));
        }

        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        if ($allot->getRecheckId() !== $userId) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是审核人，不能进行此操作"));
        }


        $taskDM = TaskDModel::getInstance();

        /** @var \Admin\Entity\Task $task */

        $task = $taskDM->find($allot->getTid());

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));
        }


        $taskSettingDM = TaskSettingDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);

        if ($task->getStandardTypes() != 2) {

            if ($post["day"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["day"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务量天数不正确"));
            }
            if ($post["aday"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["aday"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "耗时天数不正确"));
            }

            $wDay = $post["day"] + ($post["hour"] / 8) + ($post["minute"] / 480);
            $aDay = $post["aday"] + ($post["ahour"] / 8) + ($post["aminute"] / 480);
            if ($wDay > 0 && $aDay <= 0) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务量大于0时必须填写耗时"));
            }
        }

        if (!$standard || $standard->getAcorn() == 0) {
            if ($allot->getStatus() == 1 || $wDay > 0) {
                if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
                $lists3 = $taskSettingDM->getLists($this->sid, 3);
                $lists4 = $taskSettingDM->getLists($this->sid, 4);
                $lists5 = $taskSettingDM->getLists($this->sid, 5);
                if ($task->getStandardTypes() != 2) {
                    if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));

                    if (!isset($post["quality"]) || $post["quality"] < $lists5[0]["names"] || $post["quality"] > $lists5[1]["names"]) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "完成质量参数不正确"));
                    }

                    if (!isset($post["hard"]) || $post["hard"] < $lists4[0]["names"] || $post["hard"] > $lists4[1]["names"]) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "难度系数参数不正确"));
                    }

                    $base = $lists3[0]["names"];
                    $post["acorn"] = round($base * $wDay * $post["hard"] * $post["quality"] / 100);
                } else {
                    $lists6 = $taskSettingDM->getLists($this->sid, 6);
                    if (!isset($post["quality"]) || $post["quality"] < $lists6[1]["names"] || $post["quality"] > $lists6[2]["names"]) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "完成质量参数不正确"));
                    }

                    if (!isset($post["eachAcorn"]) || !preg_match("#^(0|[1-9][0-9]*)(\.[0-9]*)?$#", $post["eachAcorn"]) || $post["eachAcorn"] <= 0) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "每件积分不正确"));
                    }
                    if (!isset($post["workloadNum"]) || !preg_match("#^[1-9][0-9]*$#", $post["workloadNum"])) {
                        return $this->ajaxReturn(array("status" => "n", "info" => "核准数量不正确"));
                    }
                    $post["acorn"] = round($post["eachAcorn"] * $post["workloadNum"] * $post["quality"] / 100);

                }
            } else {
                $post["acorn"] = 0;
            }
        } elseif ($allot->getStatus() == 1) {
            if ($post["acorn"] <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写实得分数"));
            if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
        }

        $staffDM = StaffDModel::getInstance();
        $myMaxAcorn = $staffDM->getMaxAcorn($userId, $sid);
        if ($myMaxAcorn != 0 && $post["acorn"] > $myMaxAcorn) {
            return $this->ajaxReturn(array("status" => "n", "info" => "验收失败(本次任务已经超出您的最高审核分：{$myMaxAcorn})"));
        }

        $standardClassifyDM = StandardClassifyDModel::getInstance();
        $classify = $standardClassifyDM->findOneBy(array("namesEn" => "Task"));

        if (!$classify) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
        }

        if (!$standard) {
            $finds = array("names" => "任务执行", "sid" => $this->sid, "classify" => $classify->getId());
            $standard = $standardDM->findOneBy($finds);
            if (!$standard) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
            }
        }

        if (!isset($post["rating"])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请对完成情况进行评价"));
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($allot->getUserId() ?: 0);

        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行人获取信息异常，请刷新页面重试"));
        }

        if ($task->getStandardTypes() != 2) {

            $allot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"])));
            $allot->setWorkload(join(",", array($post["day"], $post["hour"], $post["minute"])));
            $allot->setAcceptHard($post["hard"]);
        } else {
            $allot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["allotNum"])));
            $allot->setWorkload(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["workloadNum"])));
            $allot->setAcceptHard($post["eachAcorn"]);
        }
        $allot->setAcceptQuality($post["quality"]);
        $allot->setAcorn($post["acorn"]);
        $allot->setRating($post["rating"]);
        $allot->setMedal($post["medal"]);
        $allot->setLearns(json_encode($post["learns"]));

        $allot->setAccept(1);
        $allot->setAcceptTime(nowTime());
        $allotDM->save($allot)->flush($allot);

        $taskDM->updateAcceptTime($task);
        $acornDM = AcornDModel::getInstance();


        if ($post["acorn"] > 0) {
            $acornDM->addAcorn($this->sid, $allot->getUserId(), $userId, $userId, $classify->getId(), $standard->getId(), $post["acorn"], "完成" . $task->getNames() . "任务", $task->getCodeNo());
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $allot->getUserId(), $this->sid);

            if ($wDay > 0 && $task->getStandardTypes() != 2) {
                $lists3 = $taskSettingDM->getLists($this->sid, 3);
                $base = $lists3[0]["names"];
                $baseScale = $lists3[1]["names"] / 100;
                $acceptAcorn = round($wDay * $base * $baseScale) ?: 1;
                $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                $acornDM->addAcorn($this->sid, $task->getAcceptId(), $userId, $userId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());
                //更新验收统计记录
                $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
            } elseif ($post["workloadNum"] > 0 && $task->getStandardTypes() == 2) {

                $lists6 = $taskSettingDM->getLists($this->sid, 6);
                $baseScale = $lists6[0]["names"] / 100;
                $acceptAcorn = round($post["acorn"] * $baseScale) ?: 1;
                $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                $acornDM->addAcorn($this->sid, $task->getAcceptId(), $userId, $userId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());
                //更新验收统计记录
                $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
            }
        } else {
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
        }

        $acorn = $post["acorn"] ?: 0;
        $memo = $post["memo"] ? "，验收说明:" . $post["memo"] : "";
        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent("[系统消息][{$user->getFullName()}]执行的任务通过验收，获取{$acorn}积分" . $memo);
        $dynamic->setTid($allot->getTid());
        $dynamic->setUserId($userId);
        $dynamic->setRuserId(0);
        $dynamic->setTypes(1);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        $taskSetting = $taskSettingDM->find($post["rating"] ?: 0);

        $message = sprintf("#%d %s 任务动态提醒：已完成验收并通过审核，完成质量：%s，执行人[%s]获得%d分%s%s",
            $task->getCodeNo(),
            $task->getNames(),
            $post["quality"] . "%",
            $user->getFullName(),
            $acorn,
            $taskSetting ? sprintf("【%s】", $taskSetting->getNames()) : "",
            $post["memo"] ? ",附言：" . $post["memo"] : ""
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        $url = url("consoles_task_details", "id=" . $allot->getTid());

        $params = array(
            "tid" => $task->getId(),
        );
        if ($task->getStatus() != 2) {
            $allotNoAccept = $allotDM->name("a")->where("a.tid=:tid and (a.accept=0 or a.accept is null or a.accept=2)")->setParameter($params)->count();
            if (!$allotNoAccept) {
                $task->setStatus(3);
                // $taskDM = TaskDModel::getInstance();
                $taskDM->save($task)->flush($task);
                TodoDModel::doneRecheckTask($userId, $task);
            } else {
                $allotNoAccept = $allotDM->name("a")
                    ->where("a.tid=:tid and (a.accept=0 or a.accept is null or a.accept=2) and a.recheckId={$userId}")
                    ->setParameter($params)->count();
                if (!$allotNoAccept) {
                    TodoDModel::doneRecheckTask($userId, $task);
                }
            }
        }

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));

    }

}
