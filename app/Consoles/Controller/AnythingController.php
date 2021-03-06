<?php

namespace Consoles\Controller;

use Admin\DModel\AnythingDModel;

class AnythingController extends CommonController {

    public function anythingAdd() {
        $taskGroupDM = new \Admin\DModel\TaskGroupDModel();//项目组

        if (Q()->getMethod() == "GET") {
            $lists = $taskGroupDM->name("t")
                ->select("t")
                ->where("t.sid=" . $this->sid)
                ->getArray();
            $this->assign("lists", $lists);
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['types'] || !$post['content']) {
            return $this->error("请填写内容并选择完成时间");
        }
        if ($post['types'] == "today") {
            $post['certainTime'] = nowTime(strtotime(date("Y-m-d 23:59:59")));
        } else if ($post['types'] == "tomorrow") {
            $post['certainTime'] = nowTime(strtotime("+1 days", strtotime(date("Y-m-d 23:59:59"))));
        } else if ($post['types'] == 'someday') {
            unset($post['certainTime']);
        } else {
            if (!$post['certainTime']) {
                return $this->error("请选择具体时间");
            }
            $post['certainTime'] = nowTime(strtotime($post['certainTime']), "Y-m-d H:i:s");
        }
        $post['createTime'] = nowTime();
        $post['userId'] = $this->getUser("id");
        $post['status'] = 0;
        $post['sid'] = $this->sid;
        $post['tgNames'] = $taskGroupDM->getGroupName($this->sid, $post['tgId']);//项目组名称
        $anythingDM = AnythingDModel::getInstance();
        $anythingDM->create($post, $anythingEN = $anythingDM->newEntity());
        if (!$anythingDM->check($post, $anythingEN)) {
            return $this->error($anythingDM->getError());
        }
        $anythingDM->add($anythingEN)->flush();
        return $this->success("添加备忘成功");
    }

    public function anythingLists() {
        $anythingDM = AnythingDModel::getInstance();
        $todayStart = date("Y-m-d 00:00:01");
        $todayEnd = date("Y-m-d 23:59:59");
        $yesterdayStart = date("Y-m-d 00:00:00", strtotime("-1 days", strtotime(date("Y-m-d 00:00:00"))));
        $yesterdayEnd = date("Y-m-d 23:59:59", strtotime("-1 days", strtotime(date("Y-m-d 23:59:00"))));
        $tomorrowStart = date("Y-m-d 00:00:00", strtotime("+1 days", strtotime(date("Y-m-d 00:00:00"))));
        $tomorrowEnd = date("Y-m-d 23:59:59", strtotime("+1 days", strtotime(date("Y-m-d 23:59:00"))));

        $todayLists = $anythingDM->name('a1')
            ->where("a1.certainTime<='{$todayEnd}' and a1.status<>1 and a1.userId={$this->getUser('id')}")
            ->order("a1.certainTime", "asc")
            ->order("a1.createTime", "desc")
            ->getArray();

        $somedayLists = $anythingDM->name('a1')
            ->where("a1.userId={$this->getUser('id')} and a1.types='someday' and a1.status<>1")
//            ->order("a1.status","desc")
            ->order("a1.createTime", "desc")
            ->getArray();

        $logLists = $anythingDM->name('a1')
            ->where("a1.userId={$this->getUser('id')}")
//            ->order("a1.types","desc")
            ->order("a1.status", "asc")
            ->order("a1.createTime", "desc")
            ->getArray();


        $todayArr = $somedayArr = $otherArr = $yesterdayArr = $tomorrowArr = array();
        foreach ($logLists as $k => $v) {
            $v['certainTime'] = totime($v['certainTime']);
            if ($v['certainTime'] >= $todayStart && $v['certainTime'] <= $todayEnd && $v['types'] != 'someday') {
                $todayArr[] = $v;
            } else if ($v['types'] == 'someday') {
                $somedayArr[] = $v;
            } else if ($v['certainTime'] >= $yesterdayStart && $v['certainTime'] <= $yesterdayEnd && $v['types'] != 'someday') {
                $yesterdayArr[] = $v;
            } else if ($v['certainTime'] >= $tomorrowStart && $v['certainTime'] <= $tomorrowEnd && $v['types'] != 'someday') {
                $tomorrowArr[] = $v;
            } else {
                $otherArr[] = $v;
            }
        }

        $get = Q()->get->all();
        $current = $get['types'] ?: 'today';
        $this->assign([
            "current" => $current,
            "todayLists" => $todayLists,
            "somedayLists" => $somedayLists,
            "logLists" => $logLists,
            "todayArr" => $todayArr,
            "somedayArr" => $somedayArr,
            "otherArr" => $otherArr,
            "yesterdayArr" => $yesterdayArr,
            "tomorrowArr" => $tomorrowArr,
        ]);
        return $this->display();
    }

    public function anythingCheck($id) {
        $anythingDM = AnythingDModel::getInstance();
        $curAny = $anythingDM->find($id);
        if (!$curAny) {
            return $this->error("未找到备忘");
        }
        $curAny->setCompleteTime(nowTime());
        $curAny->setStatus(1);
        $anythingDM->save($curAny)->flush();
        return $this->success("已完成的备忘自动进入日志", url("consoles_anything_anythingLists"));
    }

    public function anythingRevoke($id) {
        $anythingDM = AnythingDModel::getInstance();
        $curAny = $anythingDM->find($id);
        if (!$curAny) {
            return $this->error($anythingDM->getError());
        }
        $curAny->setCompleteTime(null);
        $curAny->setStatus(0);
        $anythingDM->save($curAny)->flush();
        return $this->success("已撤回", url("consoles_anything_anythingLists"));
    }

    public function anythingDelete($id) {
        $anythingDM = AnythingDModel::getInstance();
        $curAny = $anythingDM->find($id);
        if (!$curAny) return $this->error("未找到备忘");
        $anythingDM->remove($curAny)->flush();
        return $this->success("删除成功", url("consoles_anything_anythingLists"));
    }

}