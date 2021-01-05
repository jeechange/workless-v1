<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/7/23
 * Time: 15:00
 */

namespace Consoles\Controller;


use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\StandardClassifyDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\StudyResDModel;
use Admin\DModel\StudySettingDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCommentDModel;
use Admin\DModel\TaskCycleDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\TaskSettingDModel;
use Admin\DModel\StudyDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\Staff;
use Admin\Entity\StaffStation;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\TaskCycle;

class TaskMeController extends CommonController {

    public function lists() {
        $userId = $this->getUser("id");

        $this->assign("active", "myTodo");
        $taskDM = TaskDModel::getInstance();
        $allotDM = TaskAllotDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $search = $this->search();

        $search->labelType("placeholder");
//        $search->addChoiceKeyword(array("t.names" => "任务名称", "t.code_no" => "任务编号", "u.full_name" => "发布人", "t.executors" => "执行人"));
        $search->addKeyword("search", "任务名/任务编号/姓名");
        $search->bindData(Q()->get->all());
        $where = "t.sid = {$this->sid}";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        if ($params) {
            $whereSelect = $taskDM->userSelect("primordial", $userId, $this->sid, $where, $params);
            $this->assign("params", 1);
        }

        $where0 = "t.sid = {$this->sid} and a.user_id = {$userId} and a.status = 0";
        $where1 = "t.status = 1 and t.accept_id = {$userId} and (a.accept!=2 or a.accept is null) and t.sid = {$this->sid}";
        $where2 = "a.recheck_id = {$userId} and a.accept=2 and t.sid = {$this->sid}";

        $where = "({$where0}) or ($where1) or ($where2)";
        //查询拼接
        if ($whereSelect) {
            $where = "({$where}) and {$whereSelect}";
        }

        $this->assign("priorityMemo", $taskDM->priorityMemo);
        $this->assign("typesMemo", $taskDM->typesMemo);
        $this->assign("statusMemo", $taskDM->statusMemo);

        $fields = "";
        foreach ($metas = $taskDM->getMetas() as $meta) {
            $fields .= " t." . $meta["columnName"] . " as t_" . $meta["columnName"] . ",";
        }
        $taskAllotDM = TaskAllotDModel::getInstance()->data_sort();
        foreach ($taskAllotDM->getMetas() as $tav) {
            $fields .= " a." . $tav["columnName"] . " as a_" . $tav["columnName"] . ",";
        }

        foreach ($userDM->getMetas() as $uv) {
            $fields .= " u." . $uv["columnName"] . " as u_" . $uv["columnName"] . ",";
        }

        foreach ($userDM->getMetas() as $uv) {
            $fields .= " u2." . $uv["columnName"] . " as u2_" . $uv["columnName"] . ",";
        }

        $fields = trim($fields, ",");

        $page = $this->page();

        $countsql = "select count(*) as count_tmp"
            . " from __TASK__ t "
            . " left join __TASK_ALLOT__ a on t.id=a.tid "
            . " left join __USER__ u on u.id=t.issue_id "
            . " left join __USER__ u2 on u2.id=t.accept_id "
            . " where {$where} ";

        DM()->execute($countsql, $params);
        $stat1 = DM()->getLastStatement();
        $count = $stat1->fetch();
        $page->setTotal($count['count_tmp']);
        $page->showEvent();

        $value = Q()->get->get("data_sort");

        if ($value) {
            $sorts = explode("|", $value);
            $data_sort = sprintf("%s %s,", $sorts[0], isset($sorts[1]) && $sorts[1] != 2 ? "ASC" : "DESC");
        } else {
            $data_sort = "";
        }


        $sql = "select " . $fields
            . " from __TASK__ t "
            . " left join __TASK_ALLOT__ a on t.id=a.tid "
            . " left join __USER__ u on u.id=t.issue_id "
            . " left join __USER__ u2 on u2.id=t.accept_id "
            . " where {$where} "
            . " ORDER BY {$data_sort} FIELD(t.status,1,0,3,2)"
            . " limit {$page->firstRow},{$page->listRows}";

        DM()->execute($sql, $params);
        $stat = DM()->getLastStatement();
        $lists = $stat->fetchAll();

        foreach ($lists as &$v) {
            $v["executorMemo"] = $taskDM->executorMemo($v["t_executors"]);
            $v["groupName"] = $taskDM->getGroupName($v["t_pid"]);
        }

        //评价查询
        $taskSettingDM = TaskSettingDModel::getInstance();
        $TSlists = $taskSettingDM->getIdLists($this->sid, 1);
        $this->assign("TSlists", $TSlists);

        $this->assign("userId", $userId);

        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        $this->assign("typesNames", array(1 => "reward", 2 => "temp", 3 => "cycle"));
        return $this->display();
    }

    public function allotMe() {
        $userId = $this->getUser("id");

        $this->assign("active", "allotMe");
        $allotDM = TaskAllotDModel::getInstance();

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("t.status", "状态", array(0 => "执行中", 1 => "等待验收", 2 => "已取消", 3 => "已完成"), "=全部=");
        $search->addKeyword("t.tags,t.names", "任务名/任务编号/姓名");

        $search->bindData(Q()->get->all());
        $where = "t.sid =" . $this->sid . " and a.userId =$userId";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        $lists = $allotDM->name("a")->select("a,t")
            ->leftJoin("Task", "t", "t.id=a.tid")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);

        //评价查询
        $taskSettingDM = TaskSettingDModel::getInstance();
        $TSlists = $taskSettingDM->getIdLists($this->sid, 1);
        $this->assign("TSlists", $TSlists);

        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        $this->assign("typesNames", array(1 => "reward", 2 => "temp", 3 => "cycle"));
        return $this->display();
    }

    public function myIssue() {
        $this->assign("active", "myIssue");
        $userId = $this->getUser("id");
        $taskDM = TaskDModel::getInstance();

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("t.status", "状态", array(0 => "执行中", 1 => "等待验收", 2 => "已取消", 3 => "已完成"), "=全部=");
        $search->addKeyword("t.names,t.tags", "任务名/任务编号/姓名");

        $search->bindData(Q()->get->all());
        $where = "t.issueId=$userId and t.sid =" . $this->sid;
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm


        //评价查询
        $taskSettingDM = TaskSettingDModel::getInstance();
        $TSlists = $taskSettingDM->getIdLists($this->sid, 1);
        $this->assign("TSlists", $TSlists);

        //任务查询
        $lists = $taskDM->name("t")->select("t")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.id", "DESC")
            ->getArray(true);

        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        $this->assign("typesNames", array(1 => "reward", 2 => "temp", 3 => "cycle"));

        return $this->display();
    }

    public function myAccept() {
        $this->assign("active", "myAccept");
        $userId = $this->getUser("id");
        $taskDM = TaskDModel::getInstance();

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("t.status", "状态", array(0 => "执行中", 1 => "等待验收", 2 => "已取消", 3 => "已完成"), "=全部=");
        $search->addKeyword("t.names,t.tags", "任务名/任务编号/姓名");
        $search->bindData(Q()->get->all());

        $where = "(t.acceptId = $userId or REGEXP(t.rechecks,'(^|\,)(" . $userId . ")(\,|$)')=1 ) and t.sid = {$this->sid}";

        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        //评价查询
        $taskSettingDM = TaskSettingDModel::getInstance();
        $TSlists = $taskSettingDM->getIdLists($this->sid, 1);
        $this->assign("TSlists", $TSlists);

        $lists = $taskDM->name("t")->select("t")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("t.status", "ASC")
            ->order("t.acceptTime", "DESC")
            ->order("t.id", "DESC")
            ->getArray(true);
        $this->assign("userId", $userId);

        $this->assign("allotDM", TaskAllotDModel::getInstance());
        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        $this->assign("typesNames", array(1 => "reward", 2 => "temp", 3 => "cycle"));

        return $this->display();
    }

    public function accept($id) {

        $taskDM = TaskDModel::getInstance();

        /** @var \Admin\Entity\Task $task */

        $task = $taskDM->find($id);

        if (!$task || $task->getSid() != $this->sid) {
            return $this->error("记录信息获取失败");
        }

        $allotDM = TaskAllotDModel::getInstance();

        $allots = $allotDM->name("a")->where("a.tid=$id")->getArray();


        $taskSettingDM = TaskSettingDModel::getInstance();
        $studySettingDM = StudySettingDModel::getInstance();
        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $lists2 = $taskSettingDM->getLists($this->sid, 2);
        $lists3 = $taskSettingDM->getLists($this->sid, 3);
        $lists4 = $taskSettingDM->getLists($this->sid, 4);
        $lists5 = $taskSettingDM->getLists($this->sid, 5);
        //$lists5 = $studySettingDM->name("s")->where("s.types=1 and s.sid=" . $this->sid)->order("s.sort")->getArray();
        $this->assign("lists", $allots);

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);


        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("lists3", $lists3);
        $this->assign("lists4", $lists4);
        $this->assign("lists5", $lists5);
        $this->assign("standard", $standard);

        $acornrange = explode("-", $task->getAcorn());


        $this->assign("acornrange", $acornrange);

        $learns = explode(",", $task->getLearns());

        $learnsInfo = array();

        $studyDM = StudyDModel::getInstance();

        foreach ($learns as $learn) {
            $study = $studyDM->find($learn);
            if ($study) {
                $learnsInfo[$learn] = array(
                    "id" => $learn,
                    "names" => $study->getNames(),
                    "icon" => $study->getIcon(),
                );
            }
        }

        $this->assign("learnsInfo", $learnsInfo);
        $this->assign("cdnThumb", $this->cdnThumbBase);


        return $this->display();


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
            ->getArray(true);

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

        $dataId = $post["dataId"];

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

        if ($task->getStandardTypes() != 2) {

            if ($post["day"] != 0 && !preg_match("/^([0-9]|[1-9][0-9]*)$/", $post["day"])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务量天数不正确"));
            }

            $wDay = $post["day"] + ($post["hour"] / 8) + ($post["minute"] / 480);

            if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
        }

        $taskSettingDM = TaskSettingDModel::getInstance();

        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);

        if (!$standard || $standard->getAcorn() == 0) {
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

        }
        if (!isset($post["rating"])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请对完成情况进行评价"));
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($taskAllot->getUserId() ?: 0);

        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "执行人获取信息异常，请刷新页面重试"));
        }

        if ($task->getStandardTypes() != 2) {

            $taskAllot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"])));
            $taskAllot->setWorkload(join(",", array($post["day"], $post["hour"], $post["minute"])));
            $taskAllot->setAcceptHard($post["hard"]);
        } else {
            $taskAllot->setAcceptDay(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["allotNum"])));
            $taskAllot->setWorkload(join(",", array($post["aday"], $post["ahour"], $post["aminute"], $post["workloadNum"])));
            $taskAllot->setAcceptHard($post["eachAcorn"]);
        }
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

        $url = url("consoles_task_details", "id=" . $taskAllot->getTid());

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }

    //复核人验收任务通过
    public function acceptAllotRecheck($id) {
        $allotDM = TaskAllotDModel::getInstance();

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

        $post = Q()->post->all();

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
            if ($allot->getStatus() == 1 || $wDay > 0 || $post["workloadNum"]) {
                $lists3 = $taskSettingDM->getLists($this->sid, 3);
                $lists4 = $taskSettingDM->getLists($this->sid, 4);
                $lists5 = $taskSettingDM->getLists($this->sid, 5);
                if (!isset($post["quality"]) || $post["quality"] < $lists5[0]["names"] || $post["quality"] > $lists5[1]["names"]) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "完成质量参数不正确"));
                }
                if ($task->getStandardTypes() != 2) {
                    if ($wDay <= 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
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

        $taskDM->sendAcceptMessage($allot, $task, $userId, $post["memo"]);
//        TodoDModel::taskAcceptFeedback($task, $allot->getUserId());

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));

    }

    // 验收人执行：验收任务通过
    public function acceptAllot($id) {
        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */

        $allot = $allotDM->find($id);
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

        /** @var \Admin\Entity\Task $task */

        $task = $taskDM->find($allot->getTid());

        if (!$task) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));
        }

        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;
        if ($task->getAcceptId() != $userId) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是验收人，不能进行此操作"));
        }
        $post = Q()->post->all();

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
                    if ($wDay < 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
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
            if ($post["acorn"] < 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填写实得分数"));
            if ($wDay < 0 && !$post["workloadNum"]) return $this->ajaxReturn(array("status" => "n", "info" => "请填写任务量"));
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

        $allot->setAccept(1);// 验收完成
//        $allot->setAccept(3);// 验收确认中
        $allot->setAcceptTime(nowTime());
        $allotDM->save($allot)->flush($allot);


        $taskDM->updateAcceptTime($task);

        $acornDM = AcornDModel::getInstance();

          //所得积分---验收完成
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
        $taskDM->sendAcceptMessage($allot, $task, $userId, $post["memo"]);

//        TodoDModel::taskAcceptFeedback($task, $allot->getUserId());

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));

    }

    //验收人、复核人操作，审核不通过
    public function acceptAllotNoPass($id) {
        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */
        $allot = $allotDM->find($id);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        if ($allot->getAccept() != 0 && $allot->getAccept() != 2) {
            return $this->ajaxReturn(array("status" => "n", "info" => "不能重复验收"));
        }

        $userId = $this->getUser("id");

        if ($allot->getAccept() == 2 && $allot->getRecheckId() != $userId) {
            return $this->ajaxReturn(array("status" => "n", "info" => "您不是验收审核人，不能进行此操作"));
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
        if ($allot->getAccept() != 2 && $task->getAcceptId() != $userId) {
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
        $allot->setAccept(0);
        $allot->setRecheckId(0);
        $allot->setDoneTime(null);
        $allotDM->save($allot)->flush();

        $task->setStatus(0);
        // $taskDM = TaskDModel::getInstance();
        $taskDM->save($task)->flush($task);
        //执行完成更新任务统计记录
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $taskStatisticsDM->updateAllot($task, $allot->getUserId(), "noPass");

        $url = url("consoles_task_details", "id=" . $allot->getTid());

        $message = sprintf("#%d %s 任务动态提醒：任务未通过验收，执行人[%s]请重新执行，附言：%s",
            $task->getCodeNo(),
            $task->getNames(),
            $user->getFullName(),
            $post["memo"]
        );
        CompanyOpenapiDModel::sendMessage($task, $message, true);

        TodoDModel::NoPassTask($userId, $allot->getUserId(), $task);

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "操作成功",
            "url" => $url
        ));
    }

    public function cycles() {

        $userId = $this->getUser("id");

        $this->assign("active", "cycles");

        $search = $this->search();

        $search->labelType("placeholder");

        $search->addKeyword("t.names,t.tags", "任务名/任务编号");
        $search->bindData(Q()->get->all());


        $where = "t.sid = {$this->sid} and (t.issueId=$userId or REGEXP(t.executors,'(^|\,)$userId(\,|$)')=1) and t.types=3";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        $taskDM = TaskDModel::getInstance();
        $lists = $taskDM->name("t")
            ->select("t")
            ->where($where)->setParameter($params)
            ->groupBy("t.codeNo")
            ->data_sort()
            ->setPage()
            ->order("t.id", "desc")->getArray(true, false);
        $taskCycleDM = TaskCycleDModel::getInstance();
        if ($lists) {
            foreach ($lists as &$item) {
                if ($item["t_codeNo"] <= 0) continue;
                $subLists = $taskDM->name("t")
                    ->select("t")
                    ->where("t.codeNo='{$item["t_codeNo"]}' and t.sid={$item["t_sid"]}")->order("t.cycleUse", "desc")
                    ->getArray(true);
                reset($subLists);
                $item["cycleInfo"] = $taskCycleDM->getCycleInfoForArr($item);
                $item["mainItem"] = current($subLists);
                $item["subLists"] = $subLists;
            }
        }

        $this->assign("lists", $lists);
        return $this->display();
    }


    public function cycleStatus($id, $status) {

        if (!in_array($status, array(1, 2, 3))) return $this->ajaxReturn(array("status" => "n", "info" => "状态异常"));

        $taskCycleDM = TaskCycleDModel::getInstance();

        /** @var TaskCycle $cycle */
        $cycle = $taskCycleDM->find($id);

        if (!$cycle || $cycle->getSid() != $this->sid) return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));

        $codeNo = $cycle->getCodeNo();

        $taskDM = TaskDModel::getInstance();

        /** @var  Task $task */
        $task = $taskDM->findOneBy(array("sid" => $this->sid, "codeNo" => $codeNo), array("cycleUse" => "desc"));

        if (!$task || $task->getIssueId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "只有任务的发布人才能操作"));
        }
        $cycle->setStatus($status);

        $taskCycleDM->save($cycle)->flush($cycle);
        $taskStatus = array(1 => 0, 2 => 1, 3 => 3,);
        $taskDM->name("t")->where("t.sid={$this->sid} and t.codeNo={$codeNo}")->update(array("t.astatus" => $taskStatus[$status]));

        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功"));
    }

    public function cycleDelete($id) {
        $taskCycleDM = TaskCycleDModel::getInstance();

        /** @var TaskCycle $cycle */
        $cycle = $taskCycleDM->find($id);

        if (!$cycle || $cycle->getSid() != $this->sid) return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败"));

        $codeNo = $cycle->getCodeNo();

        $taskDM = TaskDModel::getInstance();

        /** @var  Task $task */
        $task = $taskDM->findOneBy(array("sid" => $this->sid, "codeNo" => $codeNo), array("cycleUse" => "desc"));

        if (!$task || $task->getIssueId() != $this->getUser("id")) {
            return $this->ajaxReturn(array("status" => "n", "info" => "只有任务的发布人才能操作"));
        }

        $tasks = $taskDM->findBy(array("sid" => $this->sid, "codeNo" => $codeNo));

        $commentDM = TaskCommentDModel::getInstance();
        $dynamicDM = TaskDynamicDModel::getInstance();
        $allotDM = TaskAllotDModel::getInstance();
        $todoDM = TodoDModel::getInstance();
        foreach ($tasks as $task) {
            $allotDM->name("a")->where("a.tid={$task->getId()}")->delete();
            $dynamicDM->name("d")->where("d.tid={$task->getId()}")->delete();
            $commentDM->name("c")->where("c.tid={$task->getId()}")->delete();
            $todoDM->name("t")->where("t.relateId={$task->getId()} and t.types<4")->delete();
            $taskDM->remove($task)->flush($task);
        }

        $taskCycleDM->remove($cycle)->flush($cycle);
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功！"));

    }

    //同意验收结果
    public function confirmAcorn() {

        $id = Q()->post->get("id") ?: 0;

        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */

        $allot = $allotDM->find($id);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $userId = $this->getUser("id");


        if ($allot->getUserId() != $userId) return $this->ajaxReturn(array("status" => "n", "info" => "您无权操作"));
        if ($allot->getAccept() != 3) return $this->ajaxReturn(array("status" => "n", "info" => "您已确认！"));


        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($allot->getTid() ?: 0);

        if (!$task) return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败！"));


        $acornDM = AcornDModel::getInstance();


        $standardClassifyDM = StandardClassifyDModel::getInstance();
        $classify = $standardClassifyDM->findOneBy(array("namesEn" => "Task"));

        if (!$classify) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
        }
        $standardDM = StandardDModel::getInstance();

        $standard = $standardDM->find($task->getStandardId() ?: 0);

        if (!$standard) {
            $finds = array("names" => "任务执行", "sid" => $this->sid, "classify" => $classify->getId());
            $standard = $standardDM->findOneBy($finds);
            if (!$standard) {
                return $this->ajaxReturn(array("status" => "n", "info" => "任务标准获取异常，请联系管理员设置标准再验收"));
            }
        }
        $acceptId = $allot->getRecheckId() ?: $task->getAcceptId();
        if ($allot->getAcorn() > 0) {

            $acornDM->addAcorn(
                $task->getSid(),
                $allot->getUserId(),
                $acceptId, $acceptId,
                $classify->getId(), $standard->getId(),
                $allot->getAcorn(),
                "完成" . $task->getNames() . "任务",
                $task->getCodeNo()
            );
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $allot->getUserId(), $this->sid);

            $taskSettingDM = TaskSettingDModel::getInstance();

            if ($task->getStandardTypes() != 2) {

                $wDays = explode(",", $allot->getWorkload());

                $wDay = $wDays[0] + ($wDays[1] / 8) + ($wDays[2] / 480);
                if ($wDay) {
                    $lists3 = $taskSettingDM->getLists($this->sid, 3);
                    $base = $lists3[0]["names"];
                    $baseScale = $lists3[1]["names"] / 100;
                    $acceptAcorn = round($wDay * $base * $baseScale) ?: 1;
                    $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                    $acornDM->addAcorn($this->sid, $task->getAcceptId(), $userId, $userId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());
                    //更新验收统计记录
                    $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                    $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
                }


            } elseif ($task->getStandardTypes() == 2) {
                $lists6 = $taskSettingDM->getLists($task->getSid(), 6);
                $baseScale = $lists6[0]["names"] / 100;
                $acceptAcorn = round($allot->getAcorn() * $baseScale) ?: 1;
                $acceptStandard = $standardDM->getStandard("验收任务", $this->sid, $classify->getId());
                $acornDM->addAcorn($task->getSid(), $task->getAcceptId(), $acceptId, $acceptId, $classify->getId(), $acceptStandard->getId(), $acceptAcorn, "完成" . $task->getNames() . "任务验收", $task->getCodeNo());
                //更新验收统计记录
                $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
                $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
            }
        } else {
            //更新验收统计记录
            $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
            $taskStatisticsDM->checkAccept($allot, $userId, $this->sid);
        }

        $allot->setAccept(1);
        $allotDM->save($allot)->flush($allot);
        if ($task->getPriority() == 1) {
            $allotDM->ifTimeoutDeduct($task->getSid(), $acceptId, $allot, $task->getCodeNo());
        }
        TodoDModel::getInstance()->name("t")
            ->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types=7 and t.userId={$allot->getUserId()}")
            ->delete();

        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功", "url" => $url = url("consoles_task_details", "id=" . $task->getId())));

    }

    public function rejectAcorn() {
        $id = Q()->post->get("id") ?: 0;
        $content = Q()->post->get("content") ?: "";


        if (!trim($content)) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请输入申诉原因"));
        }

        $allotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */

        $allot = $allotDM->find($id);
        if (!$allot) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $userId = $this->getUser("id");


        if ($allot->getUserId() != $userId) return $this->ajaxReturn(array("status" => "n", "info" => "您无权操作"));
        if ($allot->getAccept() != 3) return $this->ajaxReturn(array("status" => "n", "info" => "您已确认！"));

        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($allot->getTid() ?: 0);

        if (!$task) return $this->ajaxReturn(array("status" => "n", "info" => "任务信息获取失败！"));


        $allot->setAccept(0);
        $params = array(
            "tid" => $task->getId(),
            // "userId" => $this->getUser("id"),
            //"addTime" => now(),
        );

        $allotNoDone = $allotDM->name("a")->where("a.tid=:tid and (a.status=0 or a.status is null)")->setParameter($params)->count();
        if (!$allotNoDone) {
            $task->setStatus(1);
            $taskDM = TaskDModel::getInstance();
            $taskDM->save($task)->flush($task);
        }

        $content1 = "[系统消息]任务验收结果申诉，附言：" . $content;

        $dynamicDM = TaskDynamicDModel::getInstance();
        $dynamic = $dynamicDM->newEntity();
        $dynamic->setContent($content1);
        $dynamic->setTid($task->getId());
        $dynamic->setUserId($this->getUser("id"));
        $dynamic->setRuserId(0);
        $dynamic->setTypes(1);
        $dynamic->setAddTime(nowTime());
        $dynamicDM->add($dynamic)->flush();

        $message = sprintf("#%d %s 任务动态提醒：%s对任务验收结果进行了申诉，附言：%s",
            $task->getCodeNo(),
            $task->getNames(),
            $this->getUser("fullName"),
            $content
        );
        CompanyOpenapiDModel::sendMessage($task, $message, array($task->getIssueId(), $task->getAcceptId(), $this->getUser("id")));

        TodoDModel::createTaskDoneTodo($task, $this->getUser("id"));

        TodoDModel::getInstance()->name("t")
            ->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types=7 and t.userId={$allot->getUserId()}")
            ->delete();

        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功", "url" => $url = url("consoles_task_details", "id=" . $task->getId())));

    }


}
