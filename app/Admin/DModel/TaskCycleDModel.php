<?php

namespace Admin\DModel;

use Admin\Entity\Task;
use Admin\Entity\TaskCycle;
use phpex\DModel\DModel;

class TaskCycleDModel extends DModel {


    public $statusMemo = array(
        1 => "正常",
        2 => "已结束",
        3 => "暂停任务",
    );

    /**
     * 自动填充规则
     */
    public function _fill() {
        $this->addFill("status", "1", self::FILL_STRING, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {
        $result["statusMemo"] = $this->statusMemo[$result["status"]];
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskCycle();
    }


    /**
     * @param Task $task
     * @return TaskCycle|null
     */

    public function getCycleInfo(Task $task) {

        /** @var TaskCycle $cycle */
        $cycle = $this->findOneBy(array("sid" => $task->getSid(), "codeNo" => $task->getCodeNo()));
        if ($cycle) return $cycle;

        $cycle = $this->newEntity();

        $taskDM = TaskDModel::getInstance();
        $maxTask = $taskDM->findOneBy(array("sid" => $task->getSid(), "codeNo" => $task->getCodeNo()), array("cycleUse" => "desc"));
        $minTask = $taskDM->findOneBy(array("sid" => $task->getSid(), "codeNo" => $task->getCodeNo()), array("cycleUse" => "asc"));

        $cycle->setSid($task->getSid());
        $cycle->setCodeNo($task->getCodeNo());
        $cycle->setCycleTypes($task->getCycleTypes());
        $cycle->setCycleTimes($task->getCycleTimes());
        $cycle->setCycleStart($task->getCycleStart());
        $cycle->setCycleEnd($task->getCycleEnd());
        $cycle->setFirstTime($minTask->getAddTime());
        $cycle->setLastTime($maxTask->getAddtime());
        $cycle->setCycleNext($maxTask->getCycleNext());
        $this->add($cycle)->flush($cycle);
        return $cycle;
    }

    public function getCycleInfoForArr($task) {


        /** @var TaskCycle $cycle */
        $cycle = $this->findOneBy(array("sid" => $task['t_sid'], "codeNo" => $task["t_codeNo"]));

        if ($cycle) return $this->toArray($cycle);

        $weeks = array(
            1 => "星期一",
            2 => "星期二",
            3 => "星期三",
            4 => "星期四",
            5 => "星期五",
            6 => "星期六",
            7 => "星期日",
        );

        $cycle = $this->newEntity();

        $taskDM = TaskDModel::getInstance();

        $minTask = $taskDM->findOneBy(array("sid" => $task['t_sid'], "codeNo" => $task["t_codeNo"]), array("cycleUse" => "asc"));
        $maxTask = $taskDM->findOneBy(array("sid" => $task['t_sid'], "codeNo" => $task["t_codeNo"]), array("cycleUse" => "desc"));

        if ($task["t_cycleTypes"] == 2 && in_array($task["t_cycleStart"], $weeks)) {
            $task["t_cycleStart"] = getkey($weeks, $task["t_cycleStart"]);
            $task["t_cycleEnd"] = getkey($weeks, $task["t_cycleEnd"]);
        }
        $task["t_cycleStart"] = str_replace(" : ", ":", $task["t_cycleStart"]);
        $task["t_cycleEnd"] = str_replace(" : ", ":", $task["t_cycleEnd"]);

        if (strlen($task["t_cycleStart"]) == 8) {
            $task["t_cycleStart"] = substr($task["t_cycleStart"], 0, 5);
            $task["t_cycleEnd"] = substr($task["t_cycleEnd"], 0, 5);
        }

        $cycle->setSid($task['t_sid']);
        $cycle->setCodeNo($task["t_codeNo"]);
        $cycle->setCycleTypes($task["t_cycleTypes"]);
        $cycle->setCycleTimes($task["t_cycleTimes"] ?: 1);
        $cycle->setCycleStart($task["t_cycleStart"]);
        $cycle->setCycleEnd($task["t_cycleEnd"]);
        $cycle->setFirstTime($minTask->getAddTime());
        $cycle->setLastTime($maxTask->getAddtime());

        $taskDM->setCycleNext($maxTask);
        $cycle->setCycleNext($maxTask->getCycleNext());
        $cycle->setStatus($maxTask->getAstatus() == 1 ? 2 : 1);


        $this->add($cycle)->flush($cycle);
        return $this->toArray($cycle);
    }


}