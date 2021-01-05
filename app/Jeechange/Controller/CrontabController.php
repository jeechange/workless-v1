<?php


namespace Jeechange\Controller;

use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Admin\Entity\Task;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/12
 * Time: 10:18
 */
class CrontabController extends CommonController {
    //周期任务自动脚本
    public function cycleAutoCreate() {
        $getDay = Q()->get->get("d");
        $time = $getDay ? strtotime($getDay) : time();
        $cycleNext = date("Ymd", $time);

        $sql = "select * from __task_cycle__ tc where tc.status=1 and tc.cycle_next<='{$cycleNext}' order by tc.id asc limit 0,300";
        $d = DM();
        $d->execute($sql);
        $stat = $d->getLastStatement();
        if (!$stat->rowCount()) {
            if (Q()->get->has("shell")) return $this->getResponse("9999");
            return $this->ajaxReturn(array("info" => "无符合条件的记录", "sql" => $sql, $next = "n"), "CORS");
        }
        while ($item = $stat->fetch()) {
            $startTime = $this->getTime($time, $item, 1);
            $endTime = $this->getTime($time, $item, 2);

            $nextTime = $this->getCycleNext(strtotime($startTime), $item['cycle_times'], $item["cycle_types"]);

            $d->execute("select * from __task__ where `code_no` = {$item["code_no"]} and `sid`={$item['sid']} and  `add_time`='{$startTime}' limit 0,1");
            $tstat = $d->getLastStatement();
            $oldTaskCount = $tstat->rowCount();
            if ($oldTaskCount) {
                $d->execute("update __task_cycle__ set `cycle_next` = '{$nextTime}' where id={$item['id']}");
                continue;
            }
            $taskSql = "select * from __task__ where `code_no` = {$item["code_no"]} and `sid`={$item['sid']} order by `cycle_use` desc limit 0,1";
            $d->execute($taskSql);
            $task = $d->getLastStatement()->fetch();

            if (!$task["executors"]) {
                $d->execute("update __task_cycle__ set `cycle_next` = '{$nextTime}' where id={$item['id']}");
                continue;
            }
            $executors = explode(",", $task["executors"]);

            if (!$executors) {
                $d->execute("update __task_cycle__ set `cycle_next` = '{$nextTime}' where id={$item['id']}");
                continue;
            }

            $taskMetas = $task;

            unset($taskMetas["id"]);

            $taskMetas["cycle_use"] = $task["cycle_use"] + 1;
            $taskMetas["cycle_next"] = $nextTime;
            $taskMetas['add_time'] = $startTime;
            $taskMetas['deadline'] = $endTime;
            $taskMetas['status'] = 0;
            $taskMetas['astatus'] = 0;

            $taskKeys = "`" . join("`,`", array_keys($taskMetas)) . "`";
            $taskVals = ":" . join(",:", array_keys($taskMetas));

            $taskSql = "insert into __task__ ({$taskKeys}) VALUE ({$taskVals})";


            $d->execute($taskSql, $taskMetas);
            $tid = $d->getManager()->getConnection()->lastInsertId();
            $nowIds = array();
            $taskDM = TaskDModel::getInstance();
            foreach ($executors as $executor) {
                if (in_array($executor, $nowIds)) continue;
                $lsql = "select * from __task_allot__ a where a.tid={$tid} and a.user_id={$executor} order by a.id desc limit 0,1";
                $d->execute($lsql);
                $lstat = $d->getLastStatement();
                $allot = $lstat->fetch();
                if ($allot) {
                    if ($allot['status'] > 1) continue;
                }
                $nowIds[] = $executor;
                $params = array(
                    "tid" => $tid,
                    "user_id" => $executor,
                    "types" => 4,
                    "from_id" => 0,
                    "add_time" => $startTime,
                    "end_time" => $endTime,
                    "acorn" => 0,
                    "rating" => 0,
                    "medal" => 0,
                    "learns" => "",
                    "accept" => 0,
                    "status" => 0,
                    "accept_day" => "0",
                    "accept_hard" => "0",
                    "accept_quality" => "0",
                );
                $keys = "`" . join("`,`", array_keys($params)) . "`";
                $vals = "'" . join("','", $params) . "'";
                $sql = "insert into __task_allot__ ({$keys}) VALUE ({$vals})";
                $d->execute($sql);
                //$tid = $d->getManager()->getConnection()->lastInsertId();
            }
            if (!$nowIds) {
                $d->execute("delete from __task__ where id = {$tid}");
            } else {
                /** @var Task $task */
                $task = $taskDM->find($tid);
                if ($task) TodoDModel::createTaskTodo($task);
            }
            $d->execute("update __task_cycle__ set `cycle_next` = '{$nextTime}' where id={$item['id']}");
        }

        if (Q()->get->has("shell")) return $this->getResponse($stat->rowCount() < 300 ? "9999" : "0000");
        return $this->ajaxReturn(array("info" => "成功生成了" . $stat->rowCount() . "条任务记录", "day" => $getDay ?: date("Y-m-d"), "count" => $stat->rowCount(), $next = "y"), "CORS");

    }

    // https://www.xiangshuyun.com/jeechange/crontab_cycleautocreate

    public function cycleAutoCreate2() {
        $getDay = Q()->get->get("d");

        $time = $getDay ? strtotime($getDay) : time();


        $cycleNext = date("Ymd", $time);


        $sql = "select * from __task__ t where t.status<>2 and t.types=3 and t.astatus<1 and t.cycle_next<='{$cycleNext}' order by t.id asc limit 0,300";
        $d = DM();
        $d->execute($sql);

        $stat = $d->getLastStatement();

        if (!$stat->rowCount()) {
            if (Q()->get->has("shell")) return $this->getResponse("9999");
            return $this->ajaxReturn(array("info" => "无符合条件的记录", "sql" => $sql, $next = "n"), "CORS");
        }

        while ($item = $stat->fetch()) {
            if (!$item["executors"]) {
                $d->execute("update __task__ set `astatus` = 3 where id={$item['id']}");
                continue;
            }
            $executors = explode(",", $item["executors"]);

            if (!$executors) {
                $d->execute("update __task__ set `astatus` = 3 where id={$item['id']}");
                continue;
            }

            $startTime = $this->getTime($time, $item, 1);
            $endTime = $this->getTime($time, $item, 2);
            if (!$startTime || !$endTime) {
                $d->execute("update __task__ set `astatus` = 3 where id={$item['id']}");
                continue;
            }
            $d->execute("select * from __task__ where `code_no` = {$item["code_no"]} and `add_time`='{$startTime}' limit 0,1");
            $tstat = $d->getLastStatement();
            $oldTaskCount = $tstat->rowCount();
            if ($oldTaskCount) {
                $d->execute("update __task__ set `astatus` = 3 where id={$item['id']}");
                continue;
            }

            $d->execute("select `cycle_use` from __task__ where `code_no` = {$item["code_no"]} order by `cycle_use` desc limit 0,1");
            $cycle_use = $d->getLastStatement()->fetchColumn(0);

            $taskMetas = $item;

            unset($taskMetas["id"]);

            $taskMetas["cycle_use"] = $cycle_use + 1;
            $taskMetas["cycle_next"] = $this->getCycleNext(strtotime($startTime), $item['cycle_times'], $item["cycle_types"]);
            $taskMetas['add_time'] = $startTime;
            $taskMetas['deadline'] = $endTime;
            $taskMetas['status'] = 0;
            $taskMetas['astatus'] = 0;

            $taskKeys = "`" . join("`,`", array_keys($taskMetas)) . "`";
            $taskVals = ":" . join(",:", array_keys($taskMetas));

            $taskSql = "insert into __task__ ({$taskKeys}) VALUE ({$taskVals})";

            $d->execute($taskSql, $taskMetas);
            $tid = $d->getManager()->getConnection()->lastInsertId();
            $nowIds = array();
            foreach ($executors as $executor) {
                if (in_array($executor, $nowIds)) continue;
                $lsql = "select * from __task_allot__ a where a.tid={$item['id']} and a.user_id={$executor} order by a.id desc limit 0,1";
                $d->execute($lsql);
                $lstat = $d->getLastStatement();
                $allot = $lstat->fetch();
                if ($allot) {
                    if ($allot['status'] > 1) continue;
                }
                $nowIds[] = $executor;
                $params = array(
                    "tid" => $tid,
                    "user_id" => $executor,
                    "types" => 4,
                    "from_id" => 0,
                    "add_time" => $startTime,
                    "end_time" => $endTime,
                    "acorn" => 0,
                    "rating" => 0,
                    "medal" => 0,
                    "learns" => "",
                    "accept" => 0,
                    "status" => 0,
                    "accept_day" => "0",
                    "accept_hard" => "0",
                    "accept_quality" => "0",
                );
                $keys = "`" . join("`,`", array_keys($params)) . "`";
                $vals = "'" . join("','", $params) . "'";
                $sql = "insert into __task_allot__ ({$keys}) VALUE ({$vals})";
                $d->execute($sql);
                $tid = $d->getManager()->getConnection()->lastInsertId();
            }
            if (!$nowIds) {
                $d->execute("delete from __task__ where id = {$tid}");
            }
            $d->execute("update __task__ set `astatus` = 3 where id={$item['id']}");
        }

        if (Q()->get->has("shell")) return $this->getResponse($stat->rowCount() < 300 ? "9999" : "0000");
        return $this->ajaxReturn(array("info" => "成功生成了" . $stat->rowCount() . "条任务记录", "day" => $getDay ?: date("Y-m-d"), "count" => $stat->rowCount(), $next = "y"), "CORS");
    }


    // https://www.xiangshuyun.com/jeechange/crontab_cycleautocreate


    public function getTime($time, $item, $type) {
        if ($type == 1) {
            if ($item["cycle_types"] == 1) {
                return date("Y-m-d " . $item['cycle_start'] . ":00", $time);
            } elseif ($item["cycle_types"] == 2) {
                $w = date("w", $time);
                $time += (($item['cycle_start'] - $w) * 86400);
                return date("Y-m-d 00:00:00", $time);
            } elseif ($item["cycle_types"] == 3) {
                return date("Y-m-d 00:00:00", $time);
            }
            return null;
        }
        $start = (int)str_replace(":", "", $item['cycle_start']);
        $end = (int)str_replace(":", "", $item['cycle_end']);

        $endNext = $start > $end;

        if ($item["cycle_types"] == 1) {
            return $endNext ? date("Y-m-d " . $item['cycle_end'], $time + 86400) : date("Y-m-d " . $item['cycle_end'], $time);
        } elseif ($item["cycle_types"] == 2) {
            $w = date("w", $time);
            if ($endNext) $w -= 7;
            $time += (($item['cycle_end'] - $w) * 86400);
            return date('Y-m-d 23:59:59', $time);
        } elseif ($item["cycle_types"] == 3) {
            if ($endNext) return date("Y-m-{$item['cycle_end']}  23:59:59", strtotime("+1 month", $time));
        }
        return null;
    }
    //获取下一次执行开始时间
    public function getCycleNext($time, $cycleTimes, $types) {
        $cycleTypes = array(
            1 => "day",
            2 => "week",
            3 => "month"
        );
        $exp = sprintf("+%d %s", $cycleTimes, $cycleTypes[$types]);
        return date("Ymd", strtotime($exp, $time));
    }


    public function creatTaskStatistics() {

        $sql = "select * from __task__ t where t.sid=11 order by t.id DESC limit 0,300";
        $d = DM();
        $d->execute($sql);
        $stat = $d->getLastStatement();
        if (!$stat->rowCount()) {
            if (Q()->get->has("shell")) return $this->getResponse("9999");
            return $this->ajaxReturn(array("info" => "无符合条件的记录", "sql" => $sql, $next = "n"), "CORS");
        }

        $item = $stat->fetch();

        dump($item);
        exit;


    }


}
