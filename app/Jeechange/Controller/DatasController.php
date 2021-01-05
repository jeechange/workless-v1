<?php


namespace Jeechange\Controller;

use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskCommentDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskDynamicDModel;
use Admin\DModel\TaskStatisticsDModel;
use Admin\DModel\TodoDModel;
use Admin\Entity\Task;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/15
 * Time: 10:24
 */
class DatasController extends CommonController {

    public function delTask() {

        $codeNo = Q()->get->get("code_no");

        $taskDM = TaskDModel::getInstance();
        /** @var Task[] $tasks */
        $tasks = $taskDM->findBy(array("codeNo" => $codeNo));
        if (!$tasks) $this->getResponse("fail:$codeNo");;

        $allotDM = TaskAllotDModel::getInstance();
        $dynamicDM = TaskDynamicDModel::getInstance();
        $commentDM = TaskCommentDModel::getInstance();
        $statisticsDM = TaskStatisticsDModel::getInstance();
        $todoDM = TodoDModel::getInstance();
        foreach ($tasks as $task) {
            $tid = $task->getId();
            $allotDM->name("a")->where("a.tid=$tid")->delete();
            $dynamicDM->name("d")->where("d.tid=$tid")->delete();
            $commentDM->name("c")->where("c.tid=$tid")->delete();
            $statisticsDM->name("s")->where("s.taskId=$tid")->delete();
            $todoDM->name("t")->where("t.relateId=$tid and t.types<4")->delete();
            $taskDM->remove($task)->flush($task);
        }

        return $this->getResponse("success:$codeNo");
    }
}
