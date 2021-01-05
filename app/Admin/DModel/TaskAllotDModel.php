<?php

namespace Admin\DModel;

use Admin\Entity\CompanyMember;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\User;
use phpex\DModel\DModel;

class TaskAllotDModel extends DModel {

    public $typesMemo = array(
        "1" => "任务指派",
        "2" => "指派转移",
        "3" => "领取任务",
        "4" => "周期生成",
    );

    public $statusMemo = array(
        0 => "执行中",
        1 => "已完成",
        2 => "已转派",
        3 => "已取消",
    );

    public $acceptMemo = array(
        0 => "未验收",
        1 => "已验收",
        2 => "验收审核中",
        3 => "审核结果确认中",
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
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    public function setMeId($userId) {
        $this->meId = $userId;
    }

    protected function resolveArray(&$result) {
        $taskDM = TaskDModel::getInstance();
        if ($this->scalar) {
            $result["priorityMemo"] = $taskDM->priorityMemo[$result["t_priority"]];
            // $result["executorMemo"] = $taskDM->executorMemo($result["t_executors"]);
            $result["cycleMemo"] = $taskDM->cycleMemo($result["t_cycleTypes"], $result["t_cycleStart"], $result["t_cycleEnd"]);
            $result["taskTypesMemo"] = $taskDM->typesMemo[$result["t_types"]];
            $result["remainTime"] = $this->getRemainTime($result["a_endTime"]);
            $result["statusMemo"] = $this->statusMemo[$result["a_status"]];
            // $result["groupName"] = $this->getGroupName($result["t_pid"]);

            if ($this->meId > 0) {
                $result["meStatusMemo"] = $this->meStatusMemo($result["t_id"], $result["t_executors"], $result["t_status"], $result["t_deadline"]);
            }

            if ($this->rewardId > 0 && $result["t_types"] == 1) {
                $result["rewardMemo"] = $this->rewardMemo($result["t_id"], $result["t_status"], $result["t_nums"], $result["t_executor"]);
            }

            $result["issueFullName"] = $taskDM->resolvesField($result["t_issueId"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["executorsMemo"] = $taskDM->resolvesField($result["t_executors"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["acceptMemo"] = $taskDM->resolvesField($result["t_acceptId"], $result["t_resolves"], "fullName", $result["t_id"]);
            $result["groupName"] = $taskDM->resolvesField($result["t_pid"], $result["t_resolves"], "groupName");


        } else {
            $userDM = UserDModel::getInstance();
            $user = $userDM->find($result["userId"] ?: 0);
            $fuser = $userDM->find($result["fromId"] ?: 0);
            $result["userName"] = $user ? $user->getFullName() : "[未知]";
            $result["fromUserName"] = $fuser ? $fuser->getFullName() : "[系统]";
            $result["typesMemo"] = $this->typesMemo[$result['types']];
            $result["statusMemo"] = $this->statusMemo[$result['status']];
            $result["groupName"] = $this->getGroupName($result["t_pid"]);
        }
    }

    protected function getRemainTime(\DateTime $endTime) {
        if (!$endTime) return "无限制";
        $startTime = nowTime();
        $diff = $endTime->diff($startTime);
        if ($diff->invert == 0) {
            return "已超时";
        }
        $return = "";
        if ($diff->days)
            $return = $diff->days . "天";
        if ($diff->h) {
            $return .= $diff->h . "小时";
        }
        if ($diff->i && !$diff->days) {
            $return .= $diff->i . "分";
        }
        if ($diff->s && !$diff->days && !$diff->h) {
            $return .= $diff->s . "秒";
        }
        return $return;
    }

    protected function resolveObject($result = null) {


    }

    public function newEntity() {
        return new \Admin\Entity\TaskAllot();
    }

    public function createAllot($userId, Task $task, $types = 1, $fromId = 0) {


        $executors = explode(",", $task->getExecutors());
        if (!in_array($userId, $executors)) {
            $executors[] = $userId;
            $executors = array_unique($executors);
            $task->setExecutors(join(",", $executors));
            TaskDModel::getInstance()->save($task)->flush($task);
        }

        $allot = $this->newEntity();

        $allot->setTid($task->getId());
        $allot->setUserId($userId);
        $allot->setTypes($types);
        $allot->setFromId($fromId);

        $allot->setStatus(0);
        if ($task->getTypes() != 3) {
            $allot->setAddTime(clone $task->getAddTime());
            $allot->setEndTime(clone $task->getDeadline());
        } else {
            $taskDM = TaskDModel::getInstance();
            $allot->setAddTime(\DateTime::createFromFormat('Y-m-d H:i:s', $taskDM->getCycleTime($task, 1)));
            $allot->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $taskDM->getCycleTime($task, 2)));
        }

        $old = $this->name("a")->where("a.tid=:tid and a.userId=:userId and a.addTime=:addTime and a.endTime=:endTime and a.status<>2")
            ->setParameter(array("tid" => $task->getId(), "userId" => $userId, "addTime" => totime($allot->getAddTime()), "endTime" => totime($allot->getEndTime())))
            ->setMax(1)
            ->getArray(false, false);
        if ($old) return false;

        $this->add($allot)->flush($allot);

        return $allot;
    }

    public function getAllot($userId, Task $task, $startTime, $endTime, $fromId = 0, $types = 1) {
        $executors = explode(",", $task->getExecutors());
        if (!in_array($userId, $executors)) {
            $executors[] = $userId;
            $executors = array_unique($executors);
            $task->setExecutors(join(",", $executors));
            TaskDModel::getInstance()->save($task)->flush($task);
        }

        $params = array(
            "tid" => $task->getId(),
            "userId" => $userId,
        );
        /** @var TaskAllot $allot */
        $allot = $this->name("a")->where("a.tid= :tid and a.userId =:userId")
            ->setParameter($params)
            ->setMax(1)->getOneObject();

        if ($allot) {
            $addTime1 = totime($allot->getAddTime());
            $endTime1 = totime($allot->getEndTime());

            if ($startTime != $addTime1 || $endTime1 != $endTime) {
                $allot->setAddTime(\DateTime::createFromFormat("Y-m-d H:i:s", $startTime));
                $allot->setEndTime(\DateTime::createFromFormat("Y-m-d H:i:s", $endTime));
                $this->save($allot)->flush($allot);
            }
            return $allot;
        }

        $allot = $this->newEntity();
        $allot->setTid($task->getId());
        $allot->setUserId($userId);
        $allot->setTypes($types);
        $allot->setFromId($fromId);
        $allot->setAddTime(\DateTime::createFromFormat("Y-m-d H:i:s", $startTime));
        $allot->setEndTime(\DateTime::createFromFormat("Y-m-d H:i:s", $endTime));
        $allot->setStatus(0);

        $this->add($allot)->flush($allot);

        return $allot;

    }

    public function createAllots($executors, Task $task, $fromId = 0, $types = 1) {
        $users = array();
        if (!$executors) return $users;
        if (!is_array($executors)) $executors = explode(",", $executors);

        $taskDM = TaskDModel::getInstance();

        $startTime = $task->getTypes() < 3 ? totime($task->getAddTime()) : $taskDM->getCycleTime($task, 0);
        $endTime = $task->getTypes() < 3 ? totime($task->getDeadline()) : $taskDM->getCycleTime($task, 1);

        foreach ($executors as $userId) {
            /** @var User $user */
            $user = UserDModel::getInstance()->find($userId);
            if ($user) {
                $this->getAllot($userId, $task, $startTime, $endTime, $fromId, $types);
                $users[] = $user->getFullName();
            }
        }
        return $users;
    }

    public function getGroupName($groupId) {
        if (!$groupId) return "";
        $group = TaskGroupDModel::getInstance()->find($groupId);

        return $group ? $group->getNames() : "";
    }

    public function isAllot(Task $task, $allots, $userId) {
        if ($allots === "0" || !$allots) return false;

        if (!is_array($allots)) $allots = explode(",", $allots);

        $executors = explode(",", $task->getExecutors());

        foreach ($allots as $allotId) {
            if ($allotId == $userId) {
                return "安排人员不能包含您自己！";
            }
            if (!in_array($allotId, $executors)) continue;
            /** @var TaskAllot $allot */
            $allot = $this->findOneBy(array("tid" => $task->getId(), "userId" => $allotId), array("id" => "desc"));
            if (!$allot || $allot->getStatus() < 2) {
                /** @var User $user */
                $user = UserDModel::getInstance()->find($allotId);
                return $user ? "[" . $user->getFullName() . "]已经在执行任务" : "执行人员获取异常";
            }
        }
        return false;
    }

    public function isWaitingRecheckForMe($meId, $tid) {
        $allot = $this->findOneBy(array("recheckId" => $meId, "tid" => $tid, "accept" => "2"));
        return $allot ? true : false;
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

    public function ifTimeoutDeduct($sid, $auditor, TaskAllot $allot, $codeNo) {

        if ($allot->getAccept() != 1) return false;

        $endTime = $allot->getEndTime()->getTimestamp();
        $doneTime = $allot->getDoneTime() ? $allot->getDoneTime()->getTimestamp() : nowTime()->getTimestamp();

        if ($endTime >= $doneTime) return false;

        $taskSettingDM = TaskSettingDModel::getInstance();

        $lists7 = $taskSettingDM->getLists($sid, 7);

        $standardDM = StandardDModel::getInstance();

        $standardClassifyDM = StandardClassifyDModel::getInstance();
        $classify = $standardClassifyDM->findOneBy(array("namesEn" => "Task"));

        $standard = $standardDM->getStandard("任务超时", $sid, $classify->getId());

        $acornDM = AcornDModel::getInstance();

        $day = ceil(($doneTime - $endTime) / 86400);

        $acorn = $lists7[0]["names"] * $day;

        if (!$acorn) return false;
        $acornDM->addAcorn($sid, $allot->getUserId(), $auditor, $auditor, $classify->getId(), $standard->getId(), 0 - $acorn, sprintf("任务[%s]超时", $codeNo), $codeNo);

        if ($lists7[1]["names"] <= 0) return true;


        /** @var User $user */

        $user = UserDModel::getInstance()->find($allot->getUserId() ?: 0);
        $fullName = $user ? $user->getFullName() : "";

        $companyMemberDM = CompanyMemberDModel::getInstance();
        /** @var CompanyMember $member */

        $member = $companyMemberDM->findOneBy(array("userId" => $allot->getUserId(), "sid" => $sid));


        if (!$member || !$member->getLeader()) return true;

        for ($i = 0; $i < $lists7[1]["names"]; $i++) {
            if (!$member || !$member->getLeader()) break;
            $j = $i + 2;
            $leader = $companyMemberDM->findOneBy(array("userId" => $member->getLeader(), "sid" => $sid));
            $leaderId = $member->getLeader();
            $member = $leader;

            if (!isset($lists7[$j])) continue;
            $scale = $lists7[$j]["names"];
            $relatedAcorn = ceil($acorn * $scale / 100);
            if (!$relatedAcorn) continue;
            $acornDM->addAcorn($sid, $leaderId, $auditor, $auditor, $classify->getId(), $standard->getId(), 0 - $relatedAcorn, sprintf("[%]执行的任务[%s]超时", $fullName, $codeNo), $codeNo);
        }
        return true;

    }


    public static function stopAllot($userId, $sid) {
        if (!$userId || !$sid) return false;

        $sql = "UPDATE jee_task_allot a LEFT JOIN jee_task t on a.tid = t.id set a.`status`= 3, a.accept =1 where t.sid={$sid} and a.user_id={$userId} and a.`status`=0";
        DM()->execute($sql);

        return true;
    }


}