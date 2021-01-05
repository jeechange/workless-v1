<?php

namespace Admin\DModel;

use Admin\Entity\Settings;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\User;
use phpex\DModel\DModel;

class TaskDModel extends DModel {

    public $typesMemo = array(
        1 => "悬赏",
        2 => "普通",
        3 => "周期",
    );

    public $visibilityMemo = array(
        0 => "外部不可见",
        1 => "外部可见",
    );

    public $cycleTypesMemo = array(
        1 => "日",
        2 => "周",
        3 => "月",
    );

    public $priorityMemo = array(
        1 => "A",
        2 => "B",
        3 => "C",
        4 => "D",
    );

    public $cycleUseMemo = array(
        1 => "1小时",
        2 => "半天",
        3 => "1天",
        4 => "3天",
        5 => "1周",
    );

    public $statusMemo = array(
        0 => "执行中",
        1 => "等待验收",
        2 => "已取消",
        3 => "已完成",
    );

    public $astatusMemo = array(
        0 => "周期未终结",
        1 => "周期已终结",
        2 => "已生成新的周期",
        3 => "暂停生成周期",
    );

    private $meId = 0, $rewardId = 0;


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
        $this->addRule("names", self::RULE_REQUIRE, "任务名称不能为空", "", self::CHECK_NEED, self::TYPE_BOTH);
        $this->addRule("content", self::RULE_REQUIRE, "任务内容不能为空", "", self::CHECK_NEED, self::TYPE_BOTH);
        $this->addRule("executors", self::RULE_CALLBACK, "请选择执行人员", "checkExecutors", self::CHECK_EXIST, self::TYPE_BOTH);
        $this->addRule("acceptId", self::RULE_REQUIRE, "验收人不能为空", "", self::CHECK_NEED, self::TYPE_BOTH);
        $this->addRule("nums", self::RULE_CALLBACK, "请填写人数", "checkNum", self::CHECK_EXIST, self::TYPE_BOTH);
        $this->addRule("nums", self::RULE_CALLBACK, "请填写人数", "checkNum", self::CHECK_EXIST, self::TYPE_BOTH);
        $this->addRule("workload", self::RULE_CALLBACK, "任务量未设置或格式不正确", "checkWorkload", self::CHECK_NEED, self::TYPE_BOTH);
    }

    public function checkNum($val, Task $entity) {
        if ($entity->getTypes() != 1) return true;
        return preg_match("/^[1-9][0-9]*$/", $val);
    }

    public function checkExecutors($val, Task $entity) {
        if ($entity->getTypes() == 1) return true;
        return preg_match("/^([1-9][0-9]*\,?)+$/", $val);
    }

    public function checkWorkload($val, Task $entity) {
        if ($entity->getStandardTypes() == 2) {
            if (!$val || $val == "0") return false;
            return preg_match("/^[1-9][0-9]*$/", $val);
        }

        if (!$val || $val == "0,0,0") return false;
        $workload = explode(",", $val);
        if (count($workload) != 3) return false;
        if ($workload[0] !== "0" && !preg_match("/^[1-9][0-9]*$/", $workload[0])) {
            return false;
        }
        if (!preg_match("/^[0-7]$/", $workload[1])) {
            return false;
        }
        if (!preg_match("/^(0|15|30|45)$/", $workload[2])) {
            return false;
        }
        return true;
    }

    protected function resolveArray(&$result) {
        if ($this->scalar) {
            $result["priorityMemo"] = $this->priorityMemo[$result["t_priority"]];
            $result["typesMemo"] = $this->typesMemo[$result["t_types"]];
            $result["statusMemo"] = $this->statusMemo[$result["t_status"]];
            if ($result["t_status"] == 0) $result["astatusMemo"] = "--";
            else $result["astatusMemo"] = $this->astatusMemo[$result["t_astatus"]] ?: "--";
            //$result["acornMemo"] = $this->acornMemoDecode($result["t_acorn"], $result["t_sid"]);

            //$result["executorMemo"] = $this->executorMemo($result["t_executors"]);

            if ($result["t_types"] == 3) {
                $result["cycleMemo"] = $this->cycleMemo($result["t_cycleTypes"], $result["t_cycleStart"], $result["t_cycleEnd"]);
            }

            $result["groupName"] = $this->getGroupName($result["t_pid"]);

            if ($this->meId > 0) {
                $result["meStatusMemo"] = $this->meStatusMemo($result["t_id"], $result["t_executors"], $result["t_status"], $result["t_deadline"]);
            }

            if ($this->rewardId > 0 && $result["t_types"] == 1) {
                $result["rewardMemo"] = $this->rewardMemo($result["t_id"], $result["t_status"], $result["t_nums"], $result["t_executor"]);
            }

            $result["issueFullName"] = $this->resolvesField($result["t_issueId"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["executorsMemo"] = $this->resolvesField($result["t_executors"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["acceptMemo"] = $this->resolvesField($result["t_acceptId"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["groupName"] = $this->resolvesField($result["t_pid"], $result["t_resolves"], "groupName");
        }
    }

    public function rewardMemo($tid, $status, $nums, $executor, $showStatus = true) {

        $remain = $nums > $executor ? $nums - $executor : 0;

        if ($remain <= 0) {
            return array($remain, $status == 0 ? "已领完" : $this->statusMemo[$status], 0);
        }

        if ($this->rewardId <= 0 || $tid == 0) {
            if ($status == 0) return array($remain, "领取任务", 0);
            return array($remain, $this->statusMemo[$status], 0);
        }
        $allotDM = TaskAllotDModel::getInstance();

        $allot = $allotDM->findOneBy(array("tid" => $tid, "userId" => $this->rewardId), array("id" => "desc"));
        if (!$allot) return array($remain, "领取任务", 0);

        if ($showStatus) return array($remain, "已领取," . $this->statusMemo[$status], 1);

        return array($remain, "已领取", 1);

    }

    public function meStatusMemo($tid, $executors, $status, $deadline) {
        if ($this->meId <= 0 || $tid == 0) {
            if ($status > 0) return array($status, $this->statusMemo[$status]);
            return array("totime", totime($deadline));
        }
        if (!is_array($executors)) $executors = explode(",", $executors);

        if (!in_array($this->meId, $executors)) return $this->meStatusMemo(0, $executors, $status, $deadline);

        $allotDM = TaskAllotDModel::getInstance();

        $allot = $allotDM->findOneBy(array("tid" => $tid, "userId" => $this->meId), array("id" => "desc"));
        if (!$allot) return $this->meStatusMemo(0, $executors, $status, $deadline);

        if ($allot->getStatus() == 0) return $this->meStatusMemo(0, $executors, 0, $deadline);

        $statusMemo = $allotDM->statusMemo[$allot->getStatus()];

        if ($allot->getAccept()) return array(3, $statusMemo . ",已验收");
        return array("1", $statusMemo . ",未验收");
    }

    public function resolvesField($val, $resolves, $type, $taskId = 0) {
        if (!$val) return "";
        $jsons = json_decode($resolves, true);

        $resolvesUpdate = false;
        if (!$jsons || !isset($jsons[$type])) {
            $jsons = array($type => array());
            $resolvesUpdate = true;
        }
        $vals = explode(",", $val);
        $results = array();
        foreach ($vals as $val) {
            if (isset($jsons[$type][$val])) {
                $results[] = $jsons[$type][$val];
            } elseif ($type == "fullName" && $val) {
                $user = UserDModel::getInstance()->find($val);
                if ($user) {
                    $jsons["fullName"][$val] = $user->getFullName();
                    $results[] = $jsons[$type][$val];
                    $resolvesUpdate = true;
                }
            }
        }
        if ($resolvesUpdate)
            $this->name("t")->where("t.id=$taskId")->update(array('t.resolves' => sprintf("'%s'", json_encode($jsons))));
        return join(",", $results);
    }

    public function executorMemo($executors) {
        if (!$executors) return "";

        $executorIds = explode(",", $executors);
        $staffDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $staffDM->find($id);
            if ($user) $users[] = $user->getFullName();
        }
        return join(",", $users);
    }

    public function cycleMemo($cycleTypes, $cycleStart, $cycleEnd) {
        switch ($cycleTypes) {
            case 1:
                return sprintf("每天%s至%s", $cycleStart, $cycleEnd);
            case 2:
                $weekMemo = array(1 => "一", 2 => "二", "3" => "三", "4" => "四", "5" => "五", "6" => "六", 7 => "日");
                return sprintf("每周星期%s至星期%s", $weekMemo[$cycleStart], $weekMemo[$cycleEnd]);
            case 3:
                return sprintf("每月%d号至%d号", $cycleStart, $cycleEnd);

        }
    }

    public function cycleMemoModify($cycleTypes, $cycleStart, $cycleEnd) {
        switch ($cycleTypes) {
            case 1:
                return sprintf("%s 至 %s", $cycleStart, $cycleEnd);
            case 2:
                $weekMemo = array(1 => "一", 2 => "二", "3" => "三", "4" => "四", "5" => "五", "6" => "六", 7 => "日");
                return sprintf("星期%s 至 星期%s", $weekMemo[$cycleStart], $weekMemo[$cycleEnd]);
            case 3:
                return sprintf("%d号 至 %d号", $cycleStart, $cycleEnd);

        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Task();
    }

    public function getIssueUser($issueId) {
        if (!$issueId) return "[未知]";

        $user = UserDModel::getInstance()->find($issueId);
        return $user ? $user->getFullName() : "[未知]";
    }

    public function executorsList($selectedIds, $taskId, $taskTypes) {
        if (is_string($selectedIds)) $selectedIds = explode(",", $selectedIds);


        $users = array();

        /** @var Task $task */

        $task = $this->find($taskId);
        if (!$task) return $users;

        foreach ($selectedIds as $id) {
            $user = UserDModel::getInstance()->find($id);
            if ($user) {
                $users[] = array("fullName" =>mb_substr($user->getFullName(),0,4,"utf8"), "status" => $this->getAllots($task, $id, $taskTypes));
            }

        }
        return $users;
    }


    public function getAllots(Task $task, $userId, $taskTypes) {
        $allotDM = TaskAllotDModel::getInstance();
        /** @var TaskAllot $allot */
        $allot = $allotDM->findOneBy(array("tid" => $task->getId(), "userId" => $userId), array("endTime" => "desc"));
        if (!$allot) {
            $allotDM->createAllot($userId, $task);
            return array("memo" => "执行中", "class" => "0");
        }
        if ($task->getTypes() != 3) {
            return array("memo" => $allotDM->statusMemo[$allot->getStatus()], "class" => $allot->getStatus());
        }

        if (!$taskTypes) {
            $nowTime = date("Y-m-d H:i:s");
            $allot1 = $allotDM->name("a")
                ->where("a.tid={$task->getId()} and a.userId=$userId  and a.addTime<= '{$nowTime}' and a.endTime>='{$nowTime}'")
                ->setMax(1)->getOneObject();
            if ($allot1) {
                return array("memo" => $allotDM->statusMemo[$allot->getStatus()], "class" => $allot->getStatus());
            }
            return array("memo" => $allotDM->statusMemo[$allot->getStatus()], "class" => $allot->getStatus());
        }
        /** @var TaskAllot $reference */
        $reference = $allotDM->find($taskTypes);
        if (!$reference) {
            return $this->getAllots($task, $userId, 0);
        }

        $startTime = totime($reference->getAddTime());
        $endTime = totime($reference->getAddTime());

        $allot1 = $allotDM->name("a")
            ->where("a.tid={$task->getId()} and a.userId=$userId and a.addTime<= '{$startTime}' and a.endTime>='{$endTime}'")
            ->setMax(1)->getOneObject();
        if ($allot1) return array("memo" => $allotDM->statusMemo[$allot1->getStatus()], "class" => $allot1->getStatus());

        $nowTime = date("Y-m-d H:i:s");
        $allot1 = $allotDM->name("a")
            ->where("a.tid={$task->getId()} and a.userId=$userId and a.addTime<= '{$nowTime}' and a.endTime>='{$nowTime}'")
            ->setMax(1)->getOneObject();
        if ($allot1) {
            return array("memo" => $allotDM->statusMemo[$allot->getStatus()], "class" => $allot->getStatus());
        }
        return array("memo" => $allotDM->statusMemo[$allot->getStatus()], "class" => $allot->getStatus());
    }


    public function canDispose($userId, Task $task, $types) {
        if ($task->getIssueId() == $userId) {
            return true;
        }

        $executors = explode(",", $task->getExecutors());

        if (!in_array($userId, $executors)) {
            $this->error = "您未参与此任务，不能进行操作";
            return false;
        }

        if (in_array($types, array(0, 1))) {
            return true;
        }

        $allotDM = TaskAllotDModel::getInstance();

        $allot = $allotDM->findOneBy(array("userId" => $userId, "tid" => $task->getId()), array("id" => "DESC"));
        if (!$allot) {
            $allot = $allotDM->createAllot($userId, $task);
        }
        if ($types == 2) {
            if ($allot->getStatus() != 0) {
                $this->error = "你执行的任务才能指派给他人";
                return false;
            }
            return true;
        }
        if ($allot->getStatus() == 2 || $allot->getStatus() == 1) {
            $this->error = "此任务您已经指派他人或者完成任务";
            return false;
        }
        return true;
    }


    public function setDoneStatus(Task $task) {
        if ($task->getCycleTypes() != 3) {
            $task->setStatus(1);
            $this->save($task)->flush($task);
            return true;
        }

        $nextEnd = null;

        switch ($task->getCycleTypes()) {
            case 1:
                $nextEnd = \DateTime::createFromFormat('H:i:s', $task->getCycleEnd());
                break;
            case 2:
                $week = array(1 => "Mon", 2 => "Tue", 3 => "Wed", 4 => "Thu", 5 => "Fri", 6 => "Sat", 7 => "Sun");
                $nextEnd = \DateTime::createFromFormat("D H:i:s", $week[$task->getCycleEnd()] . " 23:59:59");
                break;
            case 3:
                $n = date("n");
                $j = date("j");
                $y = date("Y");
                if ($j > $task->getCycleEnd()) $n++;
                if ($n > 12) {
                    $n = 1;
                    $y++;
                }
                $nextEnd = \DateTime::createFromFormat('Y n j H:i:s', $y . " " . $n . " " . intval($task->getCycleEnd()) . " 23:59:59");

        }
        if (!$nextEnd) return false;

        if ($nextEnd->getTimestamp() >= $task->getDeadline()->getTimestamp()) {
            $task->setStatus(1);
            $this->save($task)->flush($task);
            return true;
        }
        return true;
    }

    public function getCycleTime(Task $task, $types = 0, $reference = "") {

        if ($task->getTypes() != 3) return $types == 0 ? totime($task->getAddTime()) : totime($task->getDeadline());
        $cycleTime = null;
        $start = (int)str_replace(":", "", $task->getCycleStart());
        $end = (int)str_replace(":", "", $task->getCycleEnd());

        $endNext = $start > $end;
        $referenceTime = $reference ? strtotime($reference) : time();

        $nowTime = time();
        $endTimes = "";
        switch ($task->getCycleTypes()) {
            case 1:
                $endTime = strtotime($task->getCycleEnd() . ":00", $referenceTime);
                if ($endNext) $endTime += 86400;
                while ($endTime <= $nowTime) {
                    $endTime += 86400;
                }
                $endTimes = date('Y-m-d H:i:s', $endTime);
                break;
            case 2:
                $w = date("w", $referenceTime);
                if ($endNext) $w += 7;
                $endTime = $referenceTime + (($task->getCycleEnd() - $w) * 86400);
                while ($endTime <= $nowTime) {
                    $endTime += 86400 * 7;
                }
                $endTimes = date('Y-m-d 23:59:59', $endTime);
                break;
            case 3:
                $endTime = $endNext ? strtotime("+1 month", $referenceTime) : $referenceTime;
                $time = strtotime(date("Y-m-{$task->getCycleEnd()}  23:59:59", $endTime));
                while ($time <= $nowTime) {
                    $endTime = strtotime("+1 month", $endTime);
                    $time = strtotime(date("Y-m-{$task->getCycleEnd()}  23:59:59", $endTime));
                }
                $endTimes = date("Y-m-{$task->getCycleEnd()} 23:59:59", $endTime);
                break;
        }
        if ($types == 1) return $endTimes;

        switch ($task->getCycleTypes()) {
            case 1:
                $startTime = strtotime($endTimes);
                if ($endNext) $startTime -= 86400;
                return date('Y-m-d H:i:00', strtotime($task->getCycleStart() . ":00", $startTime));
            case 2:
                $startTime = strtotime($endTimes);
                if ($endNext) $startTime -= 86400 * 7;
                $w = date("w", $startTime);
                $time = $startTime + (($task->getCycleStart() - $w) * 86400);
                return date('Y-m-d 00:00:00', $time);
            case 3:
                $startTime = $endNext ? strtotime("-1 month", strtotime($endTimes)) : strtotime($endTimes);
                return date("Y-m-{$task->getCycleStart()}  00:00:00", $startTime);
        }
        return now($referenceTime);
    }

    public function getCodeNo($sid) {
        if (!$sid) return 0;

        $codeNo = $this->name("t")->select('max(t.codeNo) as mCodeNo')->where("t.sid=$sid")->setMax(1)->getOneArray();

        return $codeNo ? $codeNo["mCodeNo"] + 1 : 1;
    }

    public function setCycleNext(Task $task) {
        if (!$task->getCycleTimes()) $task->setCycleTimes(1);
        $cycleTypes = array(
            1 => "day",
            2 => "week",
            3 => "month"
        );
        $exp = sprintf("+%d %s", $task->getCycleTimes(), $cycleTypes[$task->getCycleTypes()]);

        $task->setCycleNext(date("Ymd", strtotime($exp, $task->getAddTime()->getTimestamp())));
    }


    public function getDefaultDealine($time) {
        $times = strtotime($time);
        return array(
            date("Y", $times),
            date("m", $times),
            date("d", $times),
            date("H", $times),
            date("i", $times),
            date("s", $times),
        );
    }

    public function getGroupName($groupId) {
        if (!$groupId) return "";
        $group = TaskGroupDModel::getInstance()->find($groupId);

        return $group ? $group->getNames() : "";
    }

    public function getToday($showSecond = true) {
        return $showSecond ? date("Y-m-d 23:59:59") : date("Y-m-d 23:59");
    }

    public function getTomorrow($showSecond = true) {
        return $showSecond ? date("Y-m-d 23:59:59", time() + 86400) : date("Y-m-d 23:59", time() + 86400);
    }

    public function getWeekTime($showSecond = true) {

        $w = date("w");
        return $showSecond ? date("Y-m-d 23:59:59", time() + 86400 * (7 - $w)) : date("Y-m-d 23:59", time() + 86400 * (7 - $w));
    }


    public function getWorkloadMemo($workload, $standardTypes = 1) {
        if ($standardTypes != 2) {
            if (!$workload) return "[未设置]";
            if ($workload == "0,0,0") return "0分钟";

            $workloads = explode(",", $workload);
            $return = "";
            if ($workloads[0]) $return .= $workloads[0] . "天";
            if ($workloads[1]) $return .= $workloads[1] . "小时";
            if ($workloads[2]) $return .= $workloads[2] . "分";
            if (!$return) return "[未设置]";
            return $return;
        }
        return sprintf("%d 个单位", $workload);
    }

    /**
     * $from 原生的代码用：primordial 非原生的用：vendor
     * $comeFrom 1：PC 2：H5
     */
//    public $statusMemoss = array(
//        0 => "执行中",
//        1 => "等待验收",
//        2 => "已取消",
//        3 => "已完成",
//    );//任务表的状态
//    public $statusMemo123 = array(
//        0 => "执行中",
//        1 => "已完成",
//        2 => "已转派",
//        3 => "已取消",
//    );//执行人表的状态
    public function userSelect($from = 'vendor', $userId, $sid, $where, $params, $comeFrom = 1) {
        $allotDM = TaskAllotDModel::getInstance();

        $userWhere = "u.fullName like '{$params['search']}' and t.sid in ({$sid})";
        if ($params['t_status'] == 0) {
            $userWhere .= " and a.status = 0";
        } elseif ($params['t_status'] == 1) {
            $userWhere .= " and a.status in (1,2) and a.accept != 1";
        } elseif ($params['t_status'] == 3) {
            $userWhere .= " and a.status in (1,2) and a.accept = 1";
        } else {
            $userWhere .= " and a.status != 0";
        }

        $allotLists = $allotDM->name("a")->select("u,a")
            ->leftJoin("Task", "t", "t.id = a.tid")
            ->leftJoin("User", "u", "u.id = a.userId")
            ->where($userWhere)
            ->order("a.id", "DESC")
            ->getArray(true);

        $executors = $tid = array();
        foreach ($allotLists as $vv) {
            $executors[] = $vv['u_id'];
            $tid[] = $vv['a_tid'];
        }
        $tid = array_unique($tid);
        $tid = implode(",", $tid);
        $executors = array_unique($executors);
        $executors = implode("|", $executors);

        if (!$executors) {
            if (isset($params['search']) or isset($params['keywords'])) {
                $executors = 0;
            } else {
                $executors = $userId ?: 0;
            }
        }

        $tsql = $NEWwhere = '';

        if ($tid and isset($params['t_status'])) {
            $tsql = "and t.id in ({$tid})";
        }

        if (isset($params['search']) or isset($params['keywords'])) {
            if ($from == 'primordial') {
                $findOne = "(t.executors REGEXP '(^|\,)(" . $executors . ")(\,|$)' " . $tsql . " )";
                $findTwo = "(t.names like :search)";
                $findThree = "(t.code_no like :search)";
                $findFour = "(u.full_name like :search)";
            } else {
                $findOne = "(REGEXP(t.executors,'(^|\,)(" . $executors . ")(\,|$)')=1)";
                $findTwo = "(t.names like :search)";
                $findThree = "(t.codeNo like :search)";
                $findFour = "(u.fullName like :search)";
            }

            if (isset($params['t_status'])) {
                $status = "t.status = :t_status";
                $NEWwhere .= $status . " and ($findOne " . " or " . " $findThree)";
                $where = str_replace("t.status = :t_status", $NEWwhere, $where);
            } else {
                $NEWwhere .= $findOne . " or " . $findTwo . " or " . $findThree . " or " . $findFour;
            }
        }

        if ($comeFrom == 1) {
            $where = str_replace("search LIKE :search", $NEWwhere, $where);
            $where = str_replace("search like :search", $NEWwhere, $where);
        } else {
            $where = str_replace(":search", ":keywords", $where);
            $where = str_replace(":search", ":keywords", $where);

            $where = str_replace("t.names LIKE :keywords", $NEWwhere, $where);
            $where = str_replace("t.names like :keywords", $NEWwhere, $where);
        }

        return $where;
    }

    public function setMeId($userId) {
        $this->meId = $userId;
    }

    public function setRewardId($userId) {
        $this->rewardId = $userId;
    }

    public function updateRechecksById($id, $flush = true) {
        /** @var Task $task */
        $task = $this->find($id);
        if ($task) {
            $task = $this->updateRechecks($task, $flush);
        }
        return $task;
    }

    public function updateRechecks(Task $task, $flush = true) {

        $allots = TaskAllotDModel::getInstance()->name("a")->select("a.recheckId,a.id")->where("a.tid = {$task->getId()} and a.recheckId>0")->getArray();

        if (!$allots) {
            $task->setRechecks("");
            if ($flush) $this->save($task)->flush($task);
            return $task;
        }
        $ids = array();
        foreach ($allots as $allot) {
            $ids[] = $allot["recheckId"];
        }
        $task->setRechecks($ids ? join(",", $ids) : "");
        if ($flush) $this->save($task)->flush($task);
        return $task;

    }


    public function updateTagsAndResolvesById($id, $flush = true) {
        /** @var Task $task */
        $task = $this->find($id);
        if ($task) {
            $task = $this->updateTagsAndResolves($task, $flush);
        }
        return $task;
    }


    public function updateTagsAndResolves(Task $task, $flush = true) {
        $ids = array($task->getIssueId(), $task->getAcceptId());
        $ids = array_merge($ids, explode(",", $task->getExecutors()));

        $allots = TaskAllotDModel::getInstance()->name("a")->select("a.recheckId,a.id")->where("a.tid = {$task->getId()} and a.recheckId>0")->getArray();


        $recheckIds = array();
        foreach ($allots as $allot) {
            $recheckIds[] = $allot["recheckId"];
            $ids[] = $allot["recheckId"];
        }

        $ids = array_unique($ids);
        $ids = array_filter($ids);
        $idsStr = join(",", $ids);

        $users = UserDModel::getInstance()->name("u")->select("u.id,u.fullName")->where("u.id in ({$idsStr})")->getArray();

        $fullNames = array();

        $resolves = array();

        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $tags = $task->getCodeNo() . "," . join(",", $fullNames);

        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }

        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $task->setTags($tags);
        $task->setResolves($resolvesStr);
        $task->setRechecks(join(",", $recheckIds));

        if ($flush) $this->save($task)->flush($task);

        return $task;
    }

    public function updateAcceptTime(Task $task) {
        $taskAllotDM = TaskAllotDModel::getInstance();

        /** @var TaskAllot $allot */

        $allot = $taskAllotDM->findOneBy(array("accept" => 1, "tid" => $task->getId()), array("acceptTime" => "DESC"));
        if ($allot) {
            if ($allot->getAcceptTime()) {
                $acceptTime = clone $allot->getAcceptTime();
            } elseif ($allot->getDoneTime()) {
                $acceptTime = clone $allot->getDoneTime();
            } elseif ($allot->getEndTime()) {
                $acceptTime = clone $allot->getEndTime();
            } else {
                $acceptTime = nowTime();
            }
        } else {
            $acceptTime = nowTime();
        }
        $task->setAcceptTime($acceptTime);
        $this->save($task)->flush($task);
    }


    /**
     * 添加任务时发送SMS和EMAIL消息
     * @param Task $task
     * @return bool
     */
    public function sendAddMessage(Task $task) {

        if (!$task) return true;

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $smsSettings */
        $smsSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "sms"));
        /** @var Settings $emailSettings */
        $emailSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "email"));

        if (!$smsSettings && !$emailSettings) return true;

        $userDM = UserDModel::getInstance();

        if ($smsSettings && $smsSettings->getStatus() == 1) {
            $smsDM = SmsDModel::getInstance();

            $smsDM->setting($smsSettings);

            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            if ($issue && $issue->getPhone()) {
                $smsDM->send("TASK_ADD_SUCCESS", $issue->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }

            $executes = explode(",", $task->getExecutors());

            foreach ($executes as $executeId) {
                /** @var User $execute */
                $execute = $userDM->find($executeId ?: 0);

                if ($execute && $execute->getPhone()) {
                    $smsDM->send("TASK_EXECUTORS", $execute->getPhone(), array("CODE_NO" => $task->getCodeNo()));
                }
            }
        }

        if ($emailSettings && $emailSettings->getStatus() == 1) {

            $emailDM = EmailDModel::getInstance();

            $emailDM->setting($emailSettings);


            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            $content = sprintf("任务名称：[#%d]%s<br>任务优先级:<b>%s</b><br>执行人员：%s<br>验收人%s<br>任务内容：<br>%s",
                $task->getCodeNo(),
                $task->getNames(),
                $this->priorityMemo[$task->getPriority()],
                $this->resolvesField($task->getExecutors(), $task->getResolves(), "fullName", $task->getId()),
                $this->resolvesField($task->getAcceptId(), $task->getResolves(), "fullName", $task->getId()),
                $task->getContent()
            );


            if ($issue && $issue->getEmail()) {
                $title = sprintf("你发布了一个任务，[#%d]%s", $task->getCodeNo(), $task->getNames());
                $emailDM->send("CUSTOM1", $issue->getEmail(), $title, $content);
            }

            $executes = explode(",", $task->getExecutors());

            $eTitle = sprintf("您有一个待执行任务，[#%d]%s", $task->getCodeNo(), $task->getNames());
            foreach ($executes as $executeId) {
                /** @var User $execute */
                $execute = $userDM->find($executeId ?: 0);

                if ($execute && $execute->getEmail()) {
                    $emailDM->send("CUSTOM1", $execute->getEmail(), $eTitle, $content);
                }
            }

        }
        return true;
    }

    /**
     * 修改任务时发送SMS和EMAIL消息
     * @param Task $task
     * @return bool
     */

    public function sendModifyMessage(Task $task) {
        if (!$task) return true;

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $smsSettings */
        $smsSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "sms"));
        /** @var Settings $emailSettings */
        $emailSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "email"));

        if (!$smsSettings && !$emailSettings) return true;

        $userDM = UserDModel::getInstance();

        if ($smsSettings && $smsSettings->getStatus() == 1) {
            $smsDM = SmsDModel::getInstance();

            $smsDM->setting($smsSettings);

            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            if ($issue && $issue->getPhone()) {
                $smsDM->send("TASK_ADD_SUCCESS", $issue->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }

            $executes = explode(",", $task->getExecutors());

            foreach ($executes as $executeId) {
                /** @var User $execute */
                $execute = $userDM->find($executeId ?: 0);

                if ($execute && $execute->getPhone()) {
                    $smsDM->send("TASK_EXECUTORS", $execute->getPhone(), array("CODE_NO" => $task->getCodeNo()));
                }
            }
        }

        if ($emailSettings && $emailSettings->getStatus() == 1) {

            $emailDM = EmailDModel::getInstance();

            $emailDM->setting($emailSettings);


            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            $content = sprintf("任务名称：[#%d]%s<br>任务优先级:<b>%s</b><br>执行人员：%s<br>验收人%s<br>任务内容：<br>%s",
                $task->getCodeNo(),
                $task->getNames(),
                $this->priorityMemo[$task->getPriority()],
                $this->resolvesField($task->getExecutors(), $task->getResolves(), "fullName", $task->getId()),
                $this->resolvesField($task->getAcceptId(), $task->getResolves(), "fullName", $task->getId()),
                $task->getContent()
            );


            if ($issue && $issue->getEmail()) {
                $title = sprintf("你修改了一个任务，[#%d]%s", $task->getCodeNo(), $task->getNames());
                $emailDM->send("CUSTOM1", $issue->getEmail(), $title, $content);
            }

            $executes = explode(",", $task->getExecutors());

            $eTitle = sprintf("您有一个待执行任务信息发生了变化，[#%d]%s", $task->getCodeNo(), $task->getNames());
            foreach ($executes as $executeId) {
                /** @var User $execute */
                $execute = $userDM->find($executeId ?: 0);

                if ($execute && $execute->getEmail()) {
                    $emailDM->send("CUSTOM1", $execute->getEmail(), $eTitle, $content);
                }
            }

        }
        return true;
    }


    /**
     * 指派新成员时发送SMS和EMAIL消息
     * @param Task $task
     * @param $executors
     * @return bool
     */

    public function sendAllotMessage(Task $task, $executors) {
        if (!$task) return true;
        if (!is_array($executors)) $executors = explode(",", $executors);

        $executors = array_unique($executors);
        $executors = array_filter($executors);
        if (!$executors) return true;

    }


    /**
     * 取消任务时发送SMS和EMAIL消息
     * @param Task $task
     * @return bool
     */

    public function sendCancelMessage(Task $task) {
        if (!$task) return true;

    }

    /**
     *
     * 交付任务时发送SMS和EMAIL消息
     * @param Integer $userId
     * @param String $acceptDay
     * @param Task $task
     * @param String $post_content
     * @return bool
     */

    public function sendSubmitMessage($userId, $acceptDay, Task $task, $post_content) {

        if (!$userId || !$task) return true;


        $executes = explode(",", $task->getExecutors());

        if (!in_array($userId, $executes)) return true;

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $smsSettings */
        $smsSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "sms"));
        /** @var Settings $emailSettings */
        $emailSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "email"));

//        if (!$smsSettings && !$emailSettings) return true;
//        if ($smsSettings->getStatus() != 1 && $emailSettings->getStatus() != 1) return true;

        $userDM = UserDModel::getInstance();

        if ($smsSettings && $smsSettings->getStatus() == 1) {
            $smsDM = SmsDModel::getInstance();

            $smsDM->setting($smsSettings);

            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            if ($issue && $issue->getPhone()) {
                $smsDM->send("TASK_SUBMIT_TO_ISSUE", $issue->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }

            /** @var User $allotUser */
            $allotUser = $userDM->find($userId ?: 0);
            if ($allotUser && $allotUser->getPhone()) {
                $smsDM->send("TASK_SUBMIT", $allotUser->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }
            /** @var User $acceptUser */
            $acceptUser = $userDM->find($task->getAcceptId() ?: 0);
            if ($acceptUser && $acceptUser->getPhone()) {
                $smsDM->send("TASK_SUBMIT_TO_ACCEPT", $acceptUser->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }
        }

        if ($emailSettings && $emailSettings->getStatus() == 1) {
            $emailDM = EmailDModel::getInstance();

            $emailDM->setting($emailSettings);


            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            $content = sprintf("任务名称：[#%d]%s<br>任务优先级:<b>%s</b><br>执行人员：%s<br>验收人%s<br>任务内容：<br>%s<br>交付人：%s<br>耗时：%s,<br>附言：",
                $task->getCodeNo(),
                $task->getNames(),
                $this->priorityMemo[$task->getPriority()],
                $this->resolvesField($task->getExecutors(), $task->getResolves(), "fullName", $task->getId()),
                $this->resolvesField($task->getAcceptId(), $task->getResolves(), "fullName", $task->getId()),
                $task->getContent(),
                $this->resolvesField($userId, $task->getResolves(), "fullName", $task->getId()),
                $this->getWorkloadMemo($acceptDay),
                $post_content
            );
            $title = sprintf("任务交付通知，[#%d]%s", $task->getCodeNo(), $task->getNames());

            if ($issue && $issue->getEmail()) {
                $emailDM->send("CUSTOM1", $issue->getEmail(), $title, $content);
            }

            /** @var User $allotUser */
            $allotUser = $userDM->find($userId ?: 0);
            if ($allotUser && $allotUser->getEmail()) {
                $emailDM->send("CUSTOM1", $allotUser->getEmail(), $title, $content);
            }
            /** @var User $acceptUser */
            $acceptUser = $userDM->find($task->getAcceptId() ?: 0);
            if ($acceptUser && $acceptUser->getEmail()) {
                $emailDM->send("CUSTOM1", $acceptUser->getEmail(), $title, $content);
            }

        }
        return true;
    }

    /**
     * 转派任务时发送SMS和EMAIL消息
     * @param Integer $userId
     * @param String $executors
     * @param Task $task
     * @param String $post_content
     * @return bool
     */
    public function sendRedeployMessage($userId, $executors, Task $task, $post_content) {

        if (!$userId || !$task) return true;

        if (!is_array($executors)) $executors = explode(",", $executors);

        $executors = array_unique($executors);
        $executors = array_filter($executors);
        if (!$executors) return true;

    }

    /**
     * 验收任务时发送SMS和EMAIL消息
     * @param TaskAllot $allot
     * @param Task $task
     * @param Integer $acceptId
     * @param String $post_content
     * @return bool
     */

    public function sendAcceptMessage(TaskAllot $allot, Task $task, $acceptId, $post_content) {

        if (!$allot || !$task) return true;

        if ($allot->getTid() != $task->getId()) return true;

        $executes = explode(",", $task->getExecutors());

        if (!in_array($allot->getUserId(), $executes)) return true;

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $smsSettings */
        $smsSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "sms"));
        /** @var Settings $emailSettings */
        $emailSettings = $settingsDM->findOneBy(array("sid" => $task->getSid(), "names" => "email"));

        if (!$smsSettings && !$emailSettings) return true;
        if ($smsSettings->getStatus() != 1 && $emailSettings->getStatus() != 1) return true;

        $userDM = UserDModel::getInstance();

        if ($smsSettings && $smsSettings->getStatus() == 1) {
            $smsDM = SmsDModel::getInstance();

            $smsDM->setting($smsSettings);

            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            if ($issue && $issue->getPhone()) {
                $smsDM->send("TASK_SUBMIT_TO_ISSUE", $issue->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }

            /** @var User $allotUser */
            $allotUser = $userDM->find($allot->getUserId() ?: 0);
            if ($allotUser && $allotUser->getPhone()) {
                $smsDM->send("TASK_SUBMIT", $allotUser->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }
            /** @var User $acceptUser */
            $acceptUser = $userDM->find($acceptId ?: 0);
            if ($acceptUser && $acceptUser->getPhone()) {
                $smsDM->send("TASK_SUBMIT_TO_ACCEPT", $acceptUser->getPhone(), array("CODE_NO" => $task->getCodeNo()));
            }
        }

        if ($emailSettings && $emailSettings->getStatus() == 1) {
            $emailDM = EmailDModel::getInstance();

            $emailDM->setting($emailSettings);


            /** @var User $issue */

            $issue = $userDM->find($task->getIssueId() ?: 0);

            $content = sprintf("任务名称：[#%d]%s<br>任务优先级:<b>%s</b><br>执行人员：%s<br>验收人%s<br>任务内容：<br>%s<br>交付人：%s<br>耗时：%s,<br>附言：",
                $task->getCodeNo(),
                $task->getNames(),
                $this->priorityMemo[$task->getPriority()],
                $this->resolvesField($task->getExecutors(), $task->getResolves(), "fullName", $task->getId()),
                $this->resolvesField($task->getAcceptId(), $task->getResolves(), "fullName", $task->getId()),
                $task->getContent(),
                $this->resolvesField($allot->getUserId(), $task->getResolves(), "fullName", $task->getId()),
                $this->getWorkloadMemo($allot->getAcceptDay()),
                $post_content
            );
            $title = sprintf("任务交付通知，[#%d]%s", $task->getCodeNo(), $task->getNames());

            if ($issue && $issue->getEmail()) {
                $emailDM->send("CUSTOM1", $issue->getEmail(), $title, $content);
            }

            /** @var User $allotUser */
            $allotUser = $userDM->find($allot->getUserId() ?: 0);
            if ($allotUser && $allotUser->getEmail()) {
                $emailDM->send("CUSTOM1", $allotUser->getEmail(), $title, $content);
            }
            /** @var User $acceptUser */
            $acceptUser = $userDM->find($acceptId ?: 0);
            if ($acceptUser && $acceptUser->getEmail()) {
                $emailDM->send("CUSTOM1", $acceptUser->getEmail(), $title, $content);
            }

        }
        return true;
    }


}
