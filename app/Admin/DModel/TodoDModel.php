<?php

namespace Admin\DModel;

use Admin\Entity\AcornAudit;
use Admin\Entity\AcornAuditDetail;
use Admin\Entity\Task;
use Admin\Entity\TaskAllot;
use Admin\Entity\Todo;
use phpex\DModel\DModel;

class TodoDModel extends DModel {

    public $typesMemo = array(
        1 => "执行任务",
        2 => "验收任务",
        3 => "审核验收任务",
        4 => "审核积分",
        5 => "积分抄送人",
        6 => "核销小吃柜",
        7 => "确认验收结果",

    );

    public $statusMemo = array(
        0 => "待处理",
        1 => "已处理",
        2 => "已取消",
    );

    public $informTypesMemo = array(
        1 => "立即通知",
        2 => "每天通知",
        3 => "每月通知",
    );

    public $priorityMemo = array(
        1 => "A",
        2 => "B",
        3 => "C",
        4 => "D",
    );

    public $detailsPages = array(
        1 => "consoles_task_details",
        2 => "consoles_task_details",
        3 => "consoles_task_details",
        4 => "consoles_acorn_audits",
        5 => "consoles_acorn_audits",
        6 => "consoles_task_details",
        7 => "consoles_task_details",
    );
    public $detailsMobilePages = array(
        1 => "mobileConsoles_task_details",
        2 => "mobileConsoles_task_details",
        3 => "mobileConsoles_task_details",
        4 => "mobileConsoles_acorn_auditDetail",
        5 => "mobileConsoles_acorn_applyDetail",
        6 => "mobileConsoles_welfare_checkDetail",
    );

    private $pushItem = array();

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
        $result["typesMemo"] = $this->typesMemo[$result["types"]];
        $result["statusMemo"] = $this->statusMemo[$result["status"]];
        $result["informTypesMemo"] = $this->statusMemo[$result["informTypes"]];
        $result["informTypesMemo"] = $this->statusMemo[$result["informTypes"]];
        $result["priorityMemo"] = $this->priorityMemo[$result["priority"]];
        $result["issueFullName"] = $this->resolvesField($result["issueId"], $result["resolves"], "fullName", $result["id"]);
        $result["executorsMemo"] = $this->resolvesField($result["executorsId"], $result["resolves"], "fullName", $result["id"]);
        $result["acceptMemo"] = $this->resolvesField($result["acceptId"], $result["resolves"], "fullName", $result["id"]);
        $result["groupName"] = $this->resolvesField($result["groupId"], $result["resolves"], "groupName");
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Todo();
    }


    public function setPriority($priority) {
        $this->pushItem["priority"] = $priority;
        return $this;
    }

    public function setTypes($types) {
        $this->pushItem["types"] = $types;
        return $this;
    }

    public function setRelateId($relateId) {
        $this->pushItem["relateId"] = $relateId;
        return $this;
    }

    public function setInformTypes($informTypes) {
        $this->pushItem["informTypes"] = $informTypes;
        return $this;
    }

    public function setInform($inform) {
        $this->pushItem["inform"] = $inform;
        return $this;
    }

    public function setIssueId($userId) {
        $this->pushItem["issueId"] = $userId;
        return $this;
    }

    public function setIssueTypes($issueTypes) {
        $this->pushItem["issueTypes"] = $issueTypes;
        return $this;
    }

    public function setAcceptId($userId) {
        $this->pushItem["acceptId"] = $userId;
        return $this;
    }

    public function setExecutorsId($executorsId) {
        $this->pushItem["executorsId"] = $executorsId;
        return $this;
    }


    public function setInformTime(\DateTime $dateTime) {
        $this->pushItem["informTime"] = $dateTime;
        return $this;
    }

    public function setTags($tags) {
        $this->pushItem["tags"] = $tags;
        return $this;
    }

    public function setCodeNo($codeNo) {
        $this->pushItem["codeNo"] = $codeNo;
        return $this;
    }

    public function setSubCodeNo($subCodeNo) {
        $this->pushItem["subCodeNo"] = $subCodeNo;
        return $this;
    }

    public function setGroupId($groupId) {
        $this->pushItem["groupId"] = $groupId;
        return $this;
    }

    public function setContent($content) {
        $this->pushItem["content"] = $content;
        return $this;
    }

    public function setDeadline($deadline) {
        $this->pushItem["deadline"] = $deadline;
        return $this;
    }

    public function buildCodeNo($codeNo, $sid, $type) {
        if ($codeNo && preg_match("#^[1-9][0-9]*$#", $codeNo)) return $codeNo;
        if ($type < 4) return $codeNo;
        $codeNo = $this->name("t")->select('max(t.codeNo) as mCodeNo')->where("t.sid=$sid and t.types=$type")->setMax(1)->getOneArray();
        return $codeNo ? $codeNo["mCodeNo"] + 1 : 1;
    }


    public function pushTodo($sid, $userIds, $resolves = array(), $flush = true) {
        $pushItem = $this->pushItem;
        $this->pushItem = array();
        if (is_string($userIds) || is_int($userIds)) $userIds = explode(",", $userIds);

        $userDM = UserDModel::getInstance();

        $findUserIds = $userIds;
        if ($pushItem["issueId"]) {
            $findUserIds = array_merge($findUserIds, array($pushItem["issueId"], $pushItem["acceptId"]));
        }

        if ($pushItem["executorsId"]) {
            $findUserIds = array_merge($findUserIds, explode(",", $pushItem["executorsId"]));
        }
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

        if (!$users) return false;

        $fullNames = array();
        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }


        $tags = isset($pushItem["tags"]) ? trim(join(",", $fullNames) . "," . $pushItem["tags"], ",") : join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $pushItem["codeNo"] . "," . $tags;

        $types = isset($pushItem["types"]) && isset($this->typesMemo[$pushItem["types"]]) ? $pushItem["types"] : 1;

        $priority = isset($pushItem["priority"]) && isset($this->priorityMemo[$pushItem["priority"]]) ? $pushItem["priority"] : 1;
        $inform_types = isset($pushItem["informTypes"]) && isset($this->informTypesMemo[$pushItem["informTypes"]]) ? $pushItem["informTypes"] : 1;
        $inform_time = isset($pushItem["informTime"]) ? $pushItem["informTime"] : nowTime();
        $deadline = isset($pushItem["deadline"]) ? $pushItem["deadline"] : null;
        $inform = isset($pushItem["inform"]) ? $pushItem["inform"] : "";

        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        foreach ($userIds as $userId) {
            $item = $this->findOneBy(array("relateId" => $pushItem["relateId"], "sid" => $sid, "types" => $types, "userId" => $userId));
            if ($item && !$flush) continue;
            if (!$item) $item = $this->newEntity();
            $item->setTypes($types);
            $item->setSid($sid);
            $item->setUserId($userId);
            $item->setIssueId($pushItem["issueId"]);
            $item->setExecutorsId($pushItem["executorsId"]);
            $item->setAcceptId($pushItem["acceptId"]);
            $item->setIssueTypes($pushItem["issueTypes"]);
            $item->setCodeNo($this->buildCodeNo($pushItem["codeNo"], $sid, $types));
            $item->setSubCodeNo($pushItem["subCodeNo"]);
            $item->setGroupId($pushItem["groupId"]);
            $item->setRelateId($pushItem["relateId"]);
            $item->setPriority($priority);
            $item->setContent($pushItem["content"]);
            $item->setTags($tags);
            $item->setResolves($resolvesStr);
            $item->setInformTypes($inform_types);
            $item->setInform($inform);
            $item->setInformTime(clone $inform_time);
            $item->setAddTime(nowTime());
            $item->setDeadline($deadline ? clone $deadline : null);
            $item->setStatus(0);
            $this->add($item)->flush($item);
            if ($inform_types == 1 && $inform) {
                $this->sendInform($userId, $inform);
            }
        }
        return true;
    }


    public function sendInform($userId, $inform) {

    }

    public function resolvesField($val, $resolves, $type, $todoId = 0) {
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
            $this->name("t")->where("t.id=$todoId")->update(array('t.resolves' => sprintf("'%s'", json_encode($jsons))));
        return join(",", $results);
    }


    public static function createTaskTodo(Task $task, $executor = false) {
        $self = self::getInstance();
        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }
        if ($task->getStatus() == 0 || $executor) {
            $self->setTypes(1)
                ->setIssueId($task->getIssueId())
                ->setAcceptId($task->getAcceptId())
                ->setExecutorsId($task->getExecutors())
                ->setIssueTypes(1)
                ->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getCodeNo(), 1))
                ->setSubCodeNo($task->getCycleUse())
                ->setGroupId($task->getPid())
                ->setRelateId($task->getId())
                ->setPriority($task->getPriority())
                ->setContent($task->getNames())
                ->setDeadline(clone $task->getDeadline())
                ->pushTodo($task->getSid(), $task->getExecutors(), $resolves, false);
        } elseif ($task->getStatus() == 1) {
            $self->setTypes(2)
                ->setIssueId($task->getIssueId())
                ->setAcceptId($task->getAcceptId())
                ->setExecutorsId($task->getExecutors())
                ->setIssueTypes(1)
                ->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getCodeNo(), 2))
                ->setSubCodeNo($task->getCycleUse())
                ->setGroupId($task->getPid())
                ->setRelateId($task->getId())
                ->setPriority($task->getPriority())
                ->setContent($task->getNames())
                ->setDeadline(clone $task->getDeadline())
                ->pushTodo($task->getSid(), $task->getAcceptId(), $resolves);
        }
    }


    public static function updateTaskTodo(Task $task) {

        $self = self::getInstance();
        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }
        $executors = explode(",", $task->getExecutors());
        $findUserIds = $executors;
        $findUserIds = array_merge($findUserIds, array($task->getIssueId(), $task->getAcceptId()));

        $userDM = UserDModel::getInstance();

        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();
        $fullNames = array();
        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;

        $self->name("t")->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types in (1,2,3)")
            ->update(array(
                "t.executorsId" => sprintf("'%s'", join(",", $executors)),
                "t.tags" => sprintf("'%s'", $tags),
                "t.resolves" => sprintf("'%s'", $resolvesStr),
                "t.groupId" => $task->getPid(),
                "t.priority" => $task->getPriority(),
                "t.acceptId" => $task->getAcceptId(),
                "t.content" => sprintf("'%s'", $task->getNames()),
            ));
        $self->name("t")->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types = 1")
            ->update(array(
                "t.deadline" => sprintf("'%s'", totime($task->getDeadline())),
            ));


    }

    public static function createTaskRedeployTodo(Task $task, $userId, $redeployId) {
        $self = self::getInstance();
        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $redeployIds = is_array($redeployId) ? $redeployId : explode(",", $redeployId);

        $executors = explode(",", $task->getExecutors());
        $executors = array_merge($executors, $redeployIds);

        $executors = array_diff($executors, array($userId, $task->getAcceptId()));

        $findUserIds = $executors;
        $findUserIds = array_merge($findUserIds, array($task->getIssueId()));

        $userDM = UserDModel::getInstance();

        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();
        $fullNames = array();
        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $self->name("t")
            ->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types in (1,2,3)")
            ->update(array(
                "t.executorsId" => sprintf("'%s'", join(",", $executors)),
                "t.tags" => sprintf("'%s'", $tags),
                "t.resolves" => sprintf("'%s'", $resolvesStr),
            ));
        $self->name("t")
            ->where("t.relateId={$task->getId()} and t.sid = {$task->getSid()} and t.types =1 and t.userId={$userId}")
            ->update(array("t.status" => 1, "t.doneTime" => sprintf("'%s'", nowTime())));
        foreach ($redeployIds as $redeployId) {
            /** @var Todo $old */
            $old = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 1, "userId" => $redeployId));

            if ($old) {
                $old->setStatus(0);
                $self->add($old)->flush($old);
            } else {
                $item = $self->newEntity();
                $item->setTypes(1);
                $item->setSid($task->getSid());
                $item->setUserId($redeployId);
                $item->setIssueId($task->getIssueId());
                $item->setExecutorsId(join(",", $executors));
                $item->setAcceptId($task->getAcceptId());
                $item->setIssueTypes(1);
                $item->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getSid(), 1));
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
                $self->add($item)->flush($item);
            }
            // $self->sendInform($userId, $inform);
        }
        return true;
    }

    public static function createTaskDoneTodo(Task $task, $userId = 0) {
        $self = self::getInstance();

        $taskAllotDM = TaskAllotDModel::getInstance();

        $allots = $taskAllotDM->name("a")->select("a.userId,a.id")->where("a.tid={$task->getId()} and a.status>0")->getArray(false, false);
        foreach ($allots as $allot) {
            /** @var Todo $todo */
            $todo = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 1, "userId" => $allot["userId"]));
            if ($todo && $todo->getStatus() == 0) {
                $todo->setStatus(1);
                $todo->setDoneTime(nowTime());
                $self->save($todo)->flush($todo);
            }
            $todoId = $todo ? $todo->getId() : 0;
            $self->name("t")
                ->where("t.relateId={$task->getId()} and t.sid={$task->getSid()} and t.userId={$allot["userId"]} and t.id<>{$todoId}")
                ->delete();
        }
        if ($task->getStatus() == 1) {
            $resolves = array();
            if ($task->getPid()) {
                $group = TaskGroupDModel::getInstance()->find($task->getPid());
                if ($group) {
                    $resolves["groupName"][$task->getPid()] = $group->getNames();
                }
            }

            $fullNames = array();

            $userDM = UserDModel::getInstance();

            $findUserIds = explode(",", $task->getExecutors());
            $rechecks = explode(",", $task->getRechecks());
            $findUserIds = array_merge($findUserIds, array($task->getIssueId(), $task->getAcceptId()), $rechecks);
            $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

            foreach ($users as $user) {
                $fullNames[] = $user["fullName"];
                $resolves["fullName"][$user["id"]] = $user["fullName"];
            }

            $tags = join(",", $fullNames);
            if (isset($resolves["groupName"]) && $resolves["groupName"]) {
                $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
            }
            $tags = $task->getCodeNo() . "," . $tags;
            $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

            $item = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 2, "userId" => $task->getAcceptId()));
            if (!$item) $item = $self->newEntity();
            $item->setTypes(2);
            $item->setSid($task->getSid());
            $item->setUserId($task->getAcceptId());
            $item->setIssueId($task->getIssueId());
            $item->setExecutorsId($task->getExecutors());
            $item->setAcceptId($task->getAcceptId());
            $item->setIssueTypes(1);
            $item->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getSid(), 2));
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
            $self->add($item)->flush($item);
        }
    }


    /**
     * 提交复审
     * @param $userId
     * @param Task $task
     */

    public static function submitRecheckTask($userId, Task $task) {


        $self = self::getInstance();

        $taskAllotDM = TaskAllotDModel::getInstance();

        $allotCount = $taskAllotDM->name("a")->where("a.tid={$task->getId()} and (a.recheckId is null or a.recheckId=0) and a.accept=0")->count();
        if (!$allotCount) {
            self::doneAcceptTask($task->getAcceptId(), $task);
        }

        /** @var Todo $todo */

        $todo = $self->findOneBy(array("userId" => $userId, "relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 3));

        if ($todo) {
            $allotCount = $taskAllotDM->name("a")->where("a.tid={$task->getId()} and a.recheckId ={$userId} and a.accept=2")->count();
            if ($allotCount) {
                $todo->setStatus(0);
                $todo->setDeadline(nowTime(time() + 3600));
            } else {
                $todo->setStatus(1);
            }
            $self->save($todo)->flush($todo);
            return;
        }


        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $fullNames = array();

        $userDM = UserDModel::getInstance();

        $executors = explode(",", $task->getExecutors());
        $rechecks = explode(",", $task->getRechecks());

        $findUserIds = $executors;
        $findUserIds = array_merge($findUserIds, array($task->getIssueId(), $task->getAcceptId()), $rechecks);
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $item = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 3, "userId" => $userId));
        if (!$item) $item = $self->newEntity();
        $item->setTypes(3);
        $item->setSid($task->getSid());
        $item->setUserId($userId);
        $item->setIssueId($task->getIssueId());
        $item->setExecutorsId($task->getExecutors());
        $item->setAcceptId($task->getAcceptId());
        $item->setIssueTypes(1);
        $item->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getSid(), 3));
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
        $self->add($item)->flush($item);
    }

    public static function doneRecheckTask($userId, Task $task) {

        $self = self::getInstance();
        /** @var Todo $todo */
        $todo = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 3, "userId" => $userId));

        $todo->setStatus(1);
        $todo->setDoneTime(nowTime());
        $self->save($todo)->flush($todo);
        return true;
    }

    public static function doneAcceptTask($userId, Task $task) {

        $self = self::getInstance();
        /** @var Todo $todo */
        $todo = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 2, "userId" => $userId));

        if ($todo) {
            $todo->setStatus(1);
            $todo->setDoneTime(nowTime());
            $self->save($todo)->flush($todo);
        }
        return true;
    }

    public static function NoPassTask($acceptId, $userId, Task $task) {

        if ($acceptId == $task->getAcceptId()) {
            self::doneAcceptTask($acceptId, $task);
        } else {
            self::doneRecheckTask($acceptId, $task);
        }
        $self = self::getInstance();
        /** @var Todo $todo */
        $todo = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 2, "userId" => $userId));

        if ($todo) {
            $todo->setStatus(0);
            $todo->setDoneTime(null);
            $self->save($todo)->flush($todo);
            return true;
        }

        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $fullNames = array();

        $userDM = UserDModel::getInstance();

        $executors = explode(",", $task->getExecutors());

        $executors = array_diff($executors, array($userId, $task->getAcceptId()));
        $findUserIds = $executors;
        $findUserIds = array_merge($findUserIds, array($task->getIssueId()));
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);


        $item = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 1, "userId" => $userId));
        if (!$item) $item = $self->newEntity();
        $item->setTypes(1);
        $item->setSid($task->getSid());
        $item->setUserId($userId);
        $item->setIssueId($task->getIssueId());
        $item->setExecutorsId($task->getExecutors());
        $item->setAcceptId($task->getAcceptId());
        $item->setIssueTypes(1);
        $item->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getSid(), 1));
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
        $self->add($item)->flush($item);
    }


    public static function removeTaskTodo(Task $task) {


        $self = self::getInstance();

        $self->name("t")->where("t.types<4 and t.relateId={$task->getId()}")->delete();
        return true;

    }

    public static function createTaskDoneTodoAll(Task $task) {
        switch ($task->getStatus()) {
            case 1:
                $count = TaskAllotDModel::getInstance()->name("a")->where("a.tid={$task->getId()} and a.status=0")->count();
                $types = $count ? 2 : 4;
                TodoDModel::getInstance()->name("t")->where("t.relateId={$task->getId()} and t.types<$types")->update(array("t.status" => 1));

                break;
            case 2:
            case 3:
                TodoDModel::getInstance()->name("t")->where("t.relateId={$task->getId()} and t.types<4")->update(array("t.status" => 1));
        }
    }

    public function createTodoForAllot(TaskAllot $allot) {
        if (!$allot || $allot->getStatus() != 0) return false;

        $taskDM = TaskDModel::getInstance();

        /** @var Task $task */

        $task = $taskDM->find($allot->getTid() ?: 0);

        if (!$task) return false;

        $resolves = array();
        $fullNames = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $executors = explode(",", $task->getExecutors());
        $executors[] = $task->getAcceptId();
        $executors[] = $task->getIssueId();
        $findUserIds = array_merge($executors, explode(",", $task->getRechecks()));


        $userDM = UserDModel::getInstance();
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }
        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);
        $item = $this->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 1, "userId" => $allot->getUserId()));
        if (!$item) $item = $this->newEntity();
        $item->setTypes(1);
        $item->setSid($task->getSid());
        $item->setUserId($allot->getUserId());
        $item->setIssueId($task->getIssueId());
        $item->setExecutorsId($task->getExecutors());
        $item->setAcceptId($task->getAcceptId());
        $item->setIssueTypes(1);
        $item->setCodeNo($this->buildCodeNo($task->getCodeNo(), $task->getSid(), 1));
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
        $item->setAddTime($allot->getAddTime() ? clone $allot->getEndTime() : nowTime());
        $item->setDeadline($allot->getEndTime() ? clone $allot->getEndTime() : null);
        $item->setStatus(0);
        $this->add($item)->flush($item);
        return true;
    }


    public static function taskAcceptFeedback(Task $task, $allotUserId) {

        $self = self::getInstance();
        $resolves = array();
        if ($task->getPid()) {
            $group = TaskGroupDModel::getInstance()->find($task->getPid());
            if ($group) {
                $resolves["groupName"][$task->getPid()] = $group->getNames();
            }
        }

        $fullNames = array();

        $userDM = UserDModel::getInstance();

        $findUserIds = explode(",", $task->getExecutors());
        $rechecks = explode(",", $task->getRechecks());
        $findUserIds = array_merge($findUserIds, array($task->getIssueId(), $task->getAcceptId()), $rechecks);
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();

        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }

        $tags = join(",", $fullNames);
        if (isset($resolves["groupName"]) && $resolves["groupName"]) {
            $tags = trim($tags . "," . join(",", $resolves["groupName"]), ",");
        }
        $tags = $task->getCodeNo() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $item = $self->findOneBy(array("relateId" => $task->getId(), "sid" => $task->getSid(), "types" => 7, "userId" => $allotUserId));
        if (!$item) $item = $self->newEntity();
        $item->setTypes(7);
        $item->setSid($task->getSid());
        $item->setUserId($allotUserId);
        $item->setIssueId($task->getIssueId());
        $item->setExecutorsId($task->getExecutors());
        $item->setAcceptId($task->getAcceptId());
        $item->setIssueTypes(1);
        $item->setCodeNo($self->buildCodeNo($task->getCodeNo(), $task->getSid(), 2));
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
        $self->add($item)->flush($item);


    }

    /**
     * 审核人、抄送人到todo
     */
    public function createTodoAcorn(AcornAuditDetail $todoAuditDetail) {
        $standardDM = new \Admin\DModel\StandardDModel();
        $acornAuditDM = new \Admin\DModel\AcornAuditDModel();
        $todoAudit = $acornAuditDM->findOneBy(array("id" => $todoAuditDetail->getAuditId()));

        $executors = explode(",", $todoAudit->getAuditor());
        $executors[] = explode(",", $todoAudit->getCPerson());
        $findUserIds = array_merge($executors, explode(",", $todoAudit->getToUser()));

        $userDM = UserDModel::getInstance();
        $users = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in (:ids)")->setParameter(array("ids" => $findUserIds))->getArray();
        $fullNames = array();
        $resolves = array();
        foreach ($users as $user) {
            $fullNames[] = $user["fullName"];
            $resolves["fullName"][$user["id"]] = $user["fullName"];
        }
        $tags = join(",", $fullNames);
        $tags = rand_string() . "," . $tags;
        $resolvesStr = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($resolves, JSON_UNESCAPED_UNICODE) : json_encode($resolves);

        $Content = $standardDM->getStandName($todoAudit->getNames(), $todoAudit->getSid());
        //添加到todo
        $tyeps = 4;//审核人
        if ($todoAuditDetail->getTypes() == 1) {
            $tyeps = 5;//抄送人
        }
        $item = $this->findOneBy(array("relateId" => $todoAuditDetail->getId(), "sid" => $todoAuditDetail->getSid(), "types" => $tyeps, "userId" => $todoAuditDetail->getUserId()));
        if (!$item) $item = $this->newEntity();

        $item->setTypes($tyeps);
        $item->setSid($todoAudit->getSid());
        $item->setUserId($todoAuditDetail->getUserId());
        $item->setIssueId($todoAudit->getUserId());
        $item->setExecutorsId($todoAudit->getToUser());
        $item->setAcceptId($todoAudit->getAuditor());
        $item->setIssueTypes(0);
        $codeNo = $this->buildCodeNo("", $todoAudit->getSid(), 4);
        $item->setCodeNo($codeNo);
        $item->setSubCodeNo(0);
        $item->setGroupId(0);
        $item->setRelateId($todoAuditDetail->getId()); //关联的记录ID
        $item->setPriority(4);
        $item->setContent($Content);
        $item->setTags($tags);
        $item->setResolves($resolvesStr);
        $item->setInformTypes(1);
        $item->setInform("");
        $item->setInformTime(nowTime());
        $item->setAddTime(nowTime());
        $item->setDeadline(null);
        $item->setStatus(0);
        $this->add($item)->flush($item);
        return true;
    }

    public static function cleanForUserId($userId, $sid) {
        if (!$userId || !$sid) return false;

        $self = self::getInstance();
        $self->name("t")->where("t.sid= $sid and t.userId=$userId")->delete();

        return true;
    }

}