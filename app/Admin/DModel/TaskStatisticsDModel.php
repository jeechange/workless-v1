<?php

namespace Admin\DModel;

use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use phpex\DModel\DModel;

class TaskStatisticsDModel extends DModel {
    public $acceptMemo = array(
        "1" => "是执行人",
        "2" => "不是执行人"
    );
    public $typesMemo = array(
        "0" => "取消任务",
        "1" => "完成任务",
    );

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

    protected function resolveArray(&$result) {

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskStatistics();
    }

    public function week($addTime = null) {
        return $addTime ? date("W", strtotime(totime($addTime))) : date("W");
    }

    public function year($addTime = null) {
        return $addTime ? date("Y", strtotime(totime($addTime))) : date("Y");
    }

    public function month($addTime = null) {
        return $addTime ? date("m", strtotime(totime($addTime))) : date("m");
    }

    /**
     * 添加任务统计记录
     * @param Task $taks
     * @param int $types
     */
    public function adds(Task $taks, $types = 1) {
        $date = date("Ymd");
        $workload = explode(",", $taks->getWorkload());
        $workload = ($workload[0] * 24) + $workload[1] + ($workload[2] / 60);

        $ssueLists = $this->newEntity();
        $ssueLists->setUserId($taks->getIssueId());
        $ssueLists->setSid($taks->getSid());
        $ssueLists->setIssueCount(1);//发布数
        $ssueLists->setRealWl(0);//真实总数任务量
        $ssueLists->setTotalWl(0);//预估总数任务量
        $ssueLists->setExecute(0);//正在执行
        $ssueLists->setAcceptCount(0);//审核数目
        $ssueLists->setAllotCount(0);//执行数目
        $ssueLists->setQuality(0);//质量
        $ssueLists->setCoefficient(0);//系数

        if (in_array($taks->getIssueId(), array($taks->getAcceptId()))) {
            $ssueLists->setAccept(1);
        } else {
            $this->addAccept($taks, $date);//验收人
        }
        if ($taks->getExecutors()) {
            if (in_array($taks->getIssueId(), explode(",", $taks->getExecutors()))) {
                $ssueLists->setExecute(1);
                $ssueLists->setTotalWl($workload);
            }
        }

        $ids = explode(",", $taks->getExecutors());
        foreach ($ids as $id) {
            if (!in_array($taks->getIssueId(), array($id))) {
                if ($id) {
                    $this->addAllot($taks, $id);
                }
            }
        }

        $ssueLists->setWeek($this->week());
        $ssueLists->setMonth($this->month());
        $ssueLists->setYear($this->year());
        $ssueLists->setTaskId($taks->getId());
        $ssueLists->setTaskId($taks->getId());
        $ssueLists->setDay($date);
        $ssueLists->setAddTime(nowTime());
        $ssueLists->setTypes($types);
        $ssueLists->setAcceptDay(0);
        $this->save($ssueLists)->flush();
    }

    /**
     * 添加审核人统计记录
     * @param Task $taks
     * @param $date
     */
    public function addAccept(Task $taks, $date) {
        $acceptLists = $this->newEntity();
        $acceptLists->setUserId($taks->getAcceptId());
        $acceptLists->setSid($taks->getSid());
        $acceptLists->setAcceptCount(0);
        $acceptLists->setExecute(0);
        $acceptLists->setAllotCount(0);
        $acceptLists->setIssueCount(0);
        $acceptLists->setAccept(1);
        $acceptLists->setQuality(0);//质量
        $acceptLists->setCoefficient(0);//系数
        $acceptLists->setDay($date);
        $acceptLists->setAddTime(nowTime());
        $acceptLists->setWeek($this->week());
        $acceptLists->setMonth($this->month());
        $acceptLists->setYear($this->year());
        $acceptLists->setRealWl(0);
        $acceptLists->setTotalWl(0);
        $acceptLists->setAcceptDay(0);
        $acceptLists->setTypes(1);
        $acceptLists->setTaskId($taks->getId());
        $this->save($acceptLists)->flush();
    }

    /**
     * 添加执行人统计记录
     * @param Task $taks
     * @param $date
     */
    public function addAllot(Task $taks, $id) {
        $date = date("Ymd");
        $workload = explode(",", $taks->getWorkload());
        $workload = ($workload[0] * 24) + $workload[1] + ($workload[2] / 60);
        $allotLists = $this->newEntity();
        $allotLists->setUserId($id);
        $allotLists->setSid($taks->getSid());
        $allotLists->setExecute(1);
        $allotLists->setAllotCount(0);
        $allotLists->setIssueCount(0);
        $allotLists->setAcceptCount(0);
        $allotLists->setAccept(0);
        $allotLists->setQuality(0);//质量
        $allotLists->setCoefficient(0);//系数
        $allotLists->setDay($date);
        $allotLists->setWeek($this->week());
        $allotLists->setMonth($this->month());
        $allotLists->setYear($this->year());
        $allotLists->setTotalWl($workload);
        $allotLists->setRealWl(0);
        $allotLists->setAcceptDay(0);
        $allotLists->setAddTime(nowTime());
        $allotLists->setTypes(1);
        $allotLists->setTaskId($taks->getId());
        $this->save($allotLists)->flush();
    }

    /**
     * 执行人员：完成任务，转派他人；操作人：取消任务，指派任务
     * @param $id
     * @param int $types
     */
    public function updateAllot(Task $task, $userId, $types, $newUserId = 0) {

        if (!is_array($newUserId)) $newUserId = explode(",", $newUserId);

        $date = date("Ymd", strtotime(totime($task->getAddTime())));
        $workload = explode(",", $task->getWorkload());
        $workload = ($workload[0] * 24) + $workload[1] + ($workload[2] / 60);

        if ($types == "done") {//执行已完成
            if ($workload == 0) {
                $this->name("ts")->select("ts")->where("ts.taskId=" . $task->getId() . " AND ts.sid=" . $task->getSid() . " AND ts.userId=" . $userId . "AND ts.execute>0")->update(array("ts.execute" => 0));
            } else {
                $this->name("ts")->select("ts")->where("ts.taskId=" . $task->getId() . " AND ts.sid=" . $task->getSid() . " AND ts.userId=" . $userId . "AND ts.execute>0")->update(array("ts.allotCount" => 1, "ts.execute" => 0));
            }
        } elseif ($types == "redeploy") {//转派
            $lists = $this->name("ts")
                ->select("ts")
                ->where("ts.taskId=" . $task->getId() . " AND ts.sid=" . $task->getSid() . " AND ts.userId=" . $userId . "AND ts.execute>0")
                ->getOneObject();

            if ($lists) {
                $lists->setTypes(2);
                $this->save($lists)->flush();
                $allotLists = $this->newEntity();
                foreach ($newUserId as $Id) {
                    $allotLists->setUserId($Id);
                    $allotLists->setSid($task->getSid());
                    $allotLists->setAllotCount(0);
                    $allotLists->setExecute(1);
                    $allotLists->setIssueCount(0);
                    $allotLists->setAcceptCount(0);
                    if ($task->getAcceptId() != $Id) {
                        $allotLists->setAccept(0);
                    } else {
                        $allotLists->setAccept(1);
                    }
                    $allotLists->setQuality(0);//质量
                    $allotLists->setCoefficient(0);//系数
                    $allotLists->setDay($date);
                    $allotLists->setWeek($this->week($task->getAddTime()));
                    $allotLists->setMonth($this->month($task->getAddTime()));
                    $allotLists->setYear($this->year($task->getAddTime()));
                    $allotLists->setTotalWl($workload);
                    $allotLists->setRealWl(0);//真实总数任务量
                    $allotLists->setAcceptDay(0);
                    $allotLists->setAddTime(new \DateTime(date("Y-m-d H:i:s", strtotime(totime($task->getAddTime())))));
                    $allotLists->setTypes(1);
                    $allotLists->setTaskId($task->getId());
                    $this->save($allotLists)->flush();
                }
            }
        } elseif ($types == "cancel") {
            //发布人的操作：指派他人，取消
            if ($task->getTypes() == 2) {
                $lists = $this->name("ts")
                    ->select("ts")
                    ->where("ts.taskId=" . $task->getId() . " AND ts.sid=" . $task->getSid())
                    ->getArray();
                foreach ($lists as $key => $item) {
                    $this->name("ts")->select("ts")->where("ts.taskId=" . $item['taskId'] . " AND ts.userId=" . $item['userId'])->update(array("ts.types" => 0, "ts.allotCount" => 1, "ts.execute" => 0));
                }
            }

        } else if ($types == "allot") {//指派了新成员
            $allotLists = $this->newEntity();
            foreach ($newUserId as $Id) {
                $allotLists->setUserId($Id);
                $allotLists->setSid($task->getSid());
                $allotLists->setAllotCount(0);
                $allotLists->setExecute(1);
                $allotLists->setIssueCount(0);
                $allotLists->setAcceptCount(0);
                $allotLists->setAccept(0);
                $allotLists->setQuality(0);//质量
                $allotLists->setCoefficient(0);//系数
                $allotLists->setDay(date("Ymd"));
                $allotLists->setWeek($this->week());
                $allotLists->setMonth($this->month());
                $allotLists->setYear($this->year());
                $allotLists->setTotalWl($workload);
                $allotLists->setRealWl(0);//真实总数任务量
                $allotLists->setAcceptDay(0);
                $allotLists->setAddTime(nowTime());
                $allotLists->setTypes(1);
                $allotLists->setTaskId($task->getId());
                $this->save($allotLists)->flush();
            }
        } else if ($types == "noPass") {
            $this->name("ts")->select("ts")->where("ts.taskId=" . $task->getId() . " AND ts.sid=" . $task->getSid() . " AND ts.userId=" . $userId . " AND ts.allotCount>0")->update(array("ts.execute" => 1, "ts.allotCount" => 0));
        }
    }

    /**
     * 验收人操作：验收任务，更新真实任务量和耗时
     * @param TaskAllot $taskAll
     * @param $userId
     * @param $sid
     */
    public function checkAccept(TaskAllot $taskAll, $userId, $sid) {
        $workload = explode(",", $taskAll->getWorkload());
        $relaload = ($workload[0] * 24) + $workload[1] + ($workload[2] / 60);
        $where = "ts.taskId=" . $taskAll->getTid() . " AND ts.sid=" . $sid . " AND ts.userId=" . $userId;

        $lists = $this->name("ts")
            ->select("ts")
            ->where($where)
            ->getOneObject();
        if ($lists) {
            $acceptHard = explode(",", $taskAll->getAcceptDay());
            $acceptHard = ($acceptHard[0] * 24) + $acceptHard[1] + ($acceptHard[2] / 60);
            if ($lists->getAccept() == 0) {//不是验收人的时候
                $lists->setRealWl($relaload);
                $lists->setTotalWl($relaload);
            } elseif ($lists->getAccept() == 1 && $lists->getAllotCount() > 0) {//即是验收人又是执行人
                $lists->setAcceptCount(1);
                $lists->setRealWl($relaload);
                $lists->setTotalWl($relaload);
            } else {//验收人
                $lists->setAcceptCount(1);
            }
            $lists->setAcceptDay($acceptHard);//耗时
            $lists->setQuality($taskAll->getAcceptQuality());//质量
            $lists->setCoefficient($taskAll->getAcceptHard());//系数

            if ($acceptHard == 0) {
                $lists->setQuality(0);//质量
                $lists->setCoefficient(0);//系数
            }
            $this->save($lists)->flush();
        }
    }


    /**
     * 选择条件
     * @param $bTypes
     * @return array
     */
    public function selectCondition($bTypes) {
        if ($bTypes == 1) {
            $start = date("Y-m-d 00:00:01", strtotime("-1 day"));
            $end = date("Y-m-d 23:59:59", strtotime("-1 day"));
            $where = "t.addTime Between :start AND :end";
        } elseif ($bTypes == 2) {
            $start = date("Y-m-d 00:00:01", strtotime("-7 day"));
            $end = date("Y-m-d 23:59:59");
            $where = "t.addTime Between :start AND :end";
        } elseif ($bTypes == 3) {
            $start = date("Y-m-d 00:00:01", strtotime("-30 day"));
            $end = date("Y-m-d 23:59:59");
            $where = "t.addTime Between :start AND :end";
        }
        $params = array(
            "start" => $start,
            "end" => $end
        );
        $lists = array(
            "where" => $where,
            "params" => $params,
        );
        return $lists;
    }


    /**
     * 首页任务统计
     * @param $bTypes
     * @param $sid
     * @return \array[]
     */
    public function taskStatistics($bTypes, $sid) {
        $selectCondition = $this->selectCondition($bTypes);
        $where = $selectCondition['where'];
        $params = $selectCondition['params'];

        if ($where) {
            $where = $where . " AND t.sid=" . $sid;
        } else {
            $where = "t.sid=" . $sid;
        }
        $lists = $this->name("t")->leftJoin("User", "u", "u.id=t.userId")
            ->select("t.id as id,t.userId as userId,u.fullName as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->setMax(13)
            ->groupBy("t.userId")
            ->order("realWl", "DESC")
            ->getArray();

        foreach ($lists as  &$item) {
            $item['proportion'] = round($item['realWl'] / $item['totalWl'], 2) * 100;
            $item['efficiency'] = round($item['realWl'] / $item['acceptDay'], 2) * 100;
            $count = $this->name("t")->select("t.coefficient")->where("t.userId=" . $item['userId'] . "AND t.coefficient>0")->count();
            $item['coefficientAverage'] = round($item['coefficient'] / $count, 2)*100 ?: 0;//平均难度系数
            $count1 = $this->name("t")->select("t")->where("t.userId=" . $item['userId'] . "AND t.quality>0")->count();
            $item['qualityAverage'] = round($item['quality'] / $count1, 2) ?: 0;//平均质量系数
        }

        return $lists;
    }

}