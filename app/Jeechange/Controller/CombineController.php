<?php

namespace Jeechange\Controller;

use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCycleDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\Todo;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/27
 * Time: 9:27
 */
class CombineController extends CommonController {

    private $fullNames = array();
    private $groupNames = array();


    /**
     * 周期任务升级更新
     */
    public function cycle() {
        $taskDM = TaskDModel::getInstance();
        $taskCycleDM = TaskCycleDModel::getInstance();


        $lists = $taskDM->name("t")->where("t.types=3")->order("t.id")->getArray(false, false);

        $i = 0;

        $weeks = array(
            1 => "星期一",
            2 => "星期二",
            3 => "星期三",
            4 => "星期四",
            5 => "星期五",
            6 => "星期六",
            7 => "星期日",
        );


//        1 => "正常",
//        2 => "已结束",
//
//        0 => "周期未终结",
//        1 => "周期已终结",
//        2 => "已生成新的周期",
        foreach ($lists as $task) {
            if (!$task["codeNo"]) {
                $codeNo = $taskDM->getCodeNo($task['sid']);
                $taskDM->name("t")->where("t.id= {$task['id']}")->update(array("t.codeNo" => $codeNo));
                $task["codeNo"] = $codeNo;
            }
            $cycle = $taskCycleDM->findOneBy(array("sid" => $task['sid'], "codeNo" => $task["codeNo"]));

            if ($cycle) continue;
            $cycle = $taskCycleDM->newEntity();

            $minTask = $taskDM->findOneBy(array("sid" => $task['sid'], "codeNo" => $task["codeNo"]), array("cycleUse" => "asc"));
            $maxTask = $taskDM->findOneBy(array("sid" => $task['sid'], "codeNo" => $task["codeNo"]), array("cycleUse" => "desc"));

            if ($task["cycleTypes"] == 2 && in_array($task["cycleStart"], $weeks)) {
                $task["cycleStart"] = getkey($weeks, $task["cycleStart"]);
                $task["cycleEnd"] = getkey($weeks, $task["cycleEnd"]);
            }
            $task["cycleStart"] = str_replace(" : ", ":", $task["cycleStart"]);
            $task["cycleEnd"] = str_replace(" : ", ":", $task["cycleEnd"]);

            if (strlen($task["cycleStart"]) == 8) {
                $task["cycleStart"] = substr($task["cycleStart"], 0, 5);
                $task["cycleEnd"] = substr($task["cycleEnd"], 0, 5);
            }

            $cycle->setSid($task['sid']);
            $cycle->setCodeNo($task["codeNo"]);
            $cycle->setCycleTypes($task["cycleTypes"]);
            $cycle->setCycleTimes($task["cycleTimes"] ?: 1);
            $cycle->setCycleStart($task["cycleStart"]);
            $cycle->setCycleEnd($task["cycleEnd"]);
            $cycle->setFirstTime($minTask->getAddTime());
            $cycle->setLastTime($maxTask->getAddtime());

            $taskDM->setCycleNext($maxTask);
            $cycle->setCycleNext($maxTask->getCycleNext());
            $cycle->setStatus($maxTask->getAstatus() == 1 ? 2 : 1);


            $taskCycleDM->add($cycle)->flush($cycle);
            $i++;
        }

        return $this->getResponse(sprintf("生成%d条记录", $i));
    }


    /**
     * 更新TODO
     */
    public function updateTodo1() {
        $taskDM = TaskDModel::getInstance();
        /** @var Task[] $tasks */
        $tasks = $taskDM->findBy(array("status" => 0), array("id" => "asc"));

        $taskAllotDM = TaskAllotDModel::getInstance();


        foreach ($tasks as $task) {
            /** @var TaskAllot[] $allots */
            $allots = $taskAllotDM->findBy(array("tid" => $task->getId(), "status" => 0));
            $executors = array();
            foreach ($allots as $allot) {
                $executors[] = $allot->getUserId();
            }
            $this->createExecutorsTodo($task, $executors);
        }

        /** @var Task[] $accepts */

        $accepts = $taskDM->findBy(array("status" => 1), array("id" => "asc"));
        foreach ($accepts as $accept) {
            $this->createAcceptTodo($accept);
        }

        /** @var TaskAllot[] $allots */

        $allots = $taskAllotDM->name("a")->where("a.recheckId>0 and a.accept=2")->getObject();

        foreach ($allots as $allot) {
            /** @var Task $task */
            $task = $taskDM->find($allot->getTid());
            if ($task) TodoDModel::submitRecheckTask($allot->getRecheckId(), $task);
        }

        return $this->getResponse(sprintf("执行成功"));
    }

    public function updateTodo() {
        $taskAllotDM = TaskAllotDModel::getInstance();

        $first = Q()->get->get("offset") ?: 0;

        /** @var TaskAllot[] $allots */
        $allots = $taskAllotDM->name("a")->limit($first, 300)->order("a.id", "ASC")->getObject();

        if (!$allots) return $this->getResponse("完成");

        $todoDM = TodoDModel::getInstance();
        $taskDM = TaskDModel::getInstance();

        foreach ($allots as $allot) {
            /** @var Task $task */
            $task = $taskDM->find($allot->getTid() ?: 0);
            if (!$task) continue;
            switch ($allot->getStatus()) {
                case 0:
                    $todoDM->createTodoForAllot($allot);
                    break;
                case 1:
                    if ($task) TodoDModel::createTaskDoneTodo($task);
                    break;
                case 2:
                case 3:
                    $todoDM->name("t")->where("t.relateId={$allot->getTid()} and t.types = 1 and t.userId={$allot->getUserId()}")->update(array("t.status" => 1));
                    break;
            }
        }

        $nextFirst = $first + 300;
        $nextEnd = $nextFirst + 300;
        $url = url("jeechange_combine_updateTodo", array("offset" => $nextFirst));
        return $this->getResponse("<a href='{$url}'>第{$nextFirst}到{$nextEnd}条</a>");

    }


    public function taskTagsAndResolves() {

        $taskDM = TaskDModel::getInstance();

        /** @var Task[] $tasks */

        $tasks = $taskDM->name("t")->where("t.tags is null or t.tags=''")->setMax(300)->order("t.id", "asc")->getObject();


        foreach ($tasks as $task) {
            $taskDM->updateTagsAndResolves($task);
        }
        $taskAllotDM = TaskAllotDModel::getInstance();

        $allots = $taskAllotDM->name("a")->select("a.tid")->where("a.recheckId>0")->getArray();

        foreach ($allots as $allot) {
            $taskDM->updateRechecksById($allot["tid"]);
        }


        return $this->getResponse(sprintf("执行成功"));
    }


    private function createExecutorsTodo(Task $task, $executors) {
        if (!$executors) return;
        $resolves = array();
        if ($task->getPid()) {
            if (!isset($this->groupNames[$task->getPid()])) {
                $group = TaskGroupDModel::getInstance()->find($task->getPid());
                if ($group) {
                    $this->groupNames[$task->getPid()] = $group->getNames();
                } else {
                    $this->groupNames[$task->getPid()] = "";
                }
            }
            $resolves["groupName"][$task->getPid()] = $this->groupNames[$task->getPid()];
        }
        $userIds = array_merge($executors, explode(",", $task->getExecutors()), array($task->getAcceptId(), $task->getIssueId()));
        $userIds = array_unique($userIds);
        foreach ($userIds as $userId) {
            if (!isset($this->fullNames[$userId])) {
                $user = UserDModel::getInstance()->find($userId);
                if ($user) {
                    $this->fullNames[$userId] = $user->getFullName();
                } else {
                    $this->fullNames[$userId] = "";
                }
            }
            $resolves["fullName"][$userId] = $this->fullNames[$userId];
        }
        $tags = join(",", $resolves["fullName"]);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $todoDM = TodoDModel::getInstance();
        foreach ($executors as $userId) {
            $todo = $todoDM->findOneBy(array("userId" => $userId, "relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 1));
            if ($todo) continue;
            $item = $todoDM->newEntity();
            $item->setTypes(1);
            $item->setSid($task->getSid());
            $item->setUserId($userId);
            $item->setIssueId($task->getIssueId());
            $item->setExecutorsId($task->getExecutors());
            $item->setAcceptId($task->getAcceptId());
            $item->setIssueTypes(1);
            $item->setCodeNo($task->getCodeNo());
            $item->setSubCodeNo($task->getCycleUse());
            $item->setGroupId($task->getPid());
            $item->setRelateId($task->getId());
            $item->setPriority($task->getPriority());
            $item->setContent($task->getNames());
            $item->setTags($tags);
            $item->setResolves($resolvesStr);
            $item->setInformTypes(1);
            $item->setInform("");
            $item->setInformTime(nowTime());
            $item->setAddTime(nowTime());
            $item->setDeadline($task->getDeadline() ? clone $task->getDeadline() : null);
            $item->setStatus(0);
            $todoDM->add($item)->flush($item);
        }

    }


    private function createAcceptTodo(Task $task) {
        $todo = TodoDModel::getInstance()->findOneBy(array("userId" => $task->getAcceptId(), "relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 2));
        if ($todo) return;

        $resolves = array();
        if ($task->getPid()) {
            if (!isset($this->groupNames[$task->getPid()])) {
                $group = TaskGroupDModel::getInstance()->find($task->getPid());
                if ($group) {
                    $this->groupNames[$task->getPid()] = $group->getNames();
                } else {
                    $this->groupNames[$task->getPid()] = "";
                }
            }
            $resolves["groupName"][$task->getPid()] = $this->groupNames[$task->getPid()];
        }
        $userIds = array_merge(explode(",", $task->getExecutors()), array($task->getAcceptId(), $task->getIssueId()));
        $userIds = array_unique($userIds);
        foreach ($userIds as $userId) {
            if (!isset($this->fullNames[$userId])) {
                $user = UserDModel::getInstance()->find($userId);
                if ($user) {
                    $this->fullNames[$userId] = $user->getFullName();
                } else {
                    $this->fullNames[$userId] = "";
                }
            }
            $resolves["fullName"][$userId] = $this->fullNames[$userId];
        }
        $tags = join(",", $resolves["fullName"]);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $item = TodoDModel::getInstance()->newEntity();
        $item->setTypes(2);
        $item->setSid($task->getSid());
        $item->setUserId($task->getAcceptId());
        $item->setIssueId($task->getIssueId());
        $item->setExecutorsId($task->getExecutors());
        $item->setAcceptId($task->getAcceptId());
        $item->setIssueTypes(1);
        $item->setCodeNo($task->getCodeNo());
        $item->setSubCodeNo($task->getCycleUse());
        $item->setGroupId($task->getPid());
        $item->setRelateId($task->getId());
        $item->setPriority($task->getPriority());
        $item->setContent($task->getNames());
        $item->setTags($tags);
        $item->setResolves($resolvesStr);
        $item->setInformTypes(1);
        $item->setInform("");
        $item->setInformTime(nowTime());
        $item->setAddTime(nowTime());
        $item->setDeadline(nowTime(time() + 3600));
        $item->setStatus(0);
        TodoDModel::getInstance()->add($item)->flush($item);
    }


    public function updateLastAcceptTime() {
        $taskDM = TaskDModel::getInstance();

        $lists = $taskDM->name("t")->where("t.status>0 and (t.acceptTime is null or t.acceptTime ='')")->setMax(300)->getObject();

        foreach ($lists as $item) {
            $taskDM->updateAcceptTime($item);
        }

        return $this->getResponse(sprintf("执行成功"));

    }

    public function updateTodoCodeNo() {
        $first = Q()->get->get("offset") ?: 0;
        $todoDM = TodoDModel::getInstance();


        /** @var Todo[] $lists */
        $lists = $todoDM->name("t")->where("t.types>3 and (t.codeNo=0 or t.codeNo is null or t.codeNo='')")->limit($first, 300)->order("t.id", "ASC")->getObject();


        if (!$lists) return $this->getResponse("完成");
        foreach ($lists as $todo) {
            $todo->setCodeNo($todoDM->buildCodeNo("", $todo->getSid(), $todo->getTypes()));
            if (!preg_match(sprintf("#(^|,)%s(,|$)#", $todo->getCodeNo()), $todo->getTags())) {
                $tags = sprintf("%s,%s", $todo->getCodeNo(), trim($todo->getTags(), ","));
                $todo->setTags(trim($tags, ","));
            }
            $todoDM->save($todo)->flush($todo);
        }

        $nextFirst = $first + 300;
        $nextEnd = $nextFirst + 300;
        $url = url("jeechange_combine_updateTodoCodeNo", array("offset" => $nextFirst));
        return $this->getResponse("<a href='{$url}'>第{$nextFirst}到{$nextEnd}条</a>");


    }

}
