<?php

namespace MobileConsoles\Controller;


use Admin\DModel\AcornDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\StudyDetailDModel;
use Admin\DModel\StudyDModel;
use Admin\DModel\StudySettingDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WorkTypeDModel;

class StudyController extends CommonController {
    public function lists() {
        $types = Q()->get->get("types") ?: 'all';
        $this->assign("active", $types);
        $studyDM = StudyDModel::getInstance();
        $where = "s.sid =" . $this->sid;
        if ($types == "rec") $where .= " and s.showcase=1";
        $lists = $studyDM->name("s")->select("s,u,sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->leftJoin("StudyDetail", "sd", "sd.stuId=s.id")
            ->where($where)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function rec() {
        $types = Q()->get->get("types") ?: 'rec';
        $this->assign("active", $types);

        $studyDM = StudyDModel::getInstance();
        $where = "s.sid =" . $this->sid;
        if ($types == "rec") $where .= " and s.showcase=1";
        $lists = $studyDM->name("s")->select("s,u,sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->leftJoin("StudyDetail", "sd", "sd.stuId=s.id")
            ->where($where)
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function allDetail($id) {
        $studyDM = StudyDModel::getInstance();
        $where = "s.id = '{$id}' AND s.sid =" . $this->sid;
        $lists = $studyDM->name("s")->select("s,u,sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->leftJoin("StudyDetail", "sd", "sd.stuId=s.id")
            ->where($where)
            ->getArray(true);
//        dump($lists);exit;
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    /**领取学习任务
     */
    public function receiveTask($id) {
        $studyDM = StudyDModel::getInstance();
        $studyDateilDM = StudyDetailDModel::getInstance();
        $study = $studyDM->find($id);
        if (!$study) {
            return $this->error("亲，该学习任务不存在，请选择其他学习任务吧");
        }
        $studyDateil = $studyDateilDM->newEntity();
        $post['sid'] = $this->sid;
        $post['stuId'] = $id;
        $post['userId'] = $this->getUser('id');
        $post['taskCount'] = 0;
        $post['totalCount'] = $study->getAuditTask();
        $post['addTime'] = nowTime();
        $post['status'] = 0;
        $studyDateilDM->create($post, $studyDateil);
        $studyDateilDM->add($studyDateil)->flush();
        return $this->success(url("mobileConsoles_Study_lists"));
    }


    public function add() {
        $types = Q()->get->get("types") ?: 'all';
        $this->assign("active", $types);
        if (Q()->isGet()) {
            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workers($this->sid, array());
            $this->assign("accept", $executors);
            $workTypeDM = WorkTypeDModel::getInstance();
            $workTypes = $workTypeDM->name("w")->where("w.sid=" . $this->sid)->getArray();
            $this->assign("workTypes", $workTypes);

            $standardDM = StandardDModel::getInstance();
            $lists = $standardDM->name("sd")->where("sd.sid = " . $this->sid . " and sd.classify = 6 and sd.status = 1")->getArray();
            $this->assign("lists", $lists);
            return $this->display();
        }

        $post = Q()->post->all();
        $studyDM = StudyDModel::getInstance();
        $old = $studyDM->findOneBy(array("names" => $post["names"]));
        if ($old) {
            return $this->error("学习名称必须唯一");
        }
        if (!$post['standardId']) {
            return $this->error("请先添加学习标准后，再添加学习");
        }
        $post["showcase"] = isset($post["showcase"]) ? 1 : 0;
        $post["apply"] = isset($post["apply"]) ? join(",", $post["apply"]) : "";
        $post["acorn"] = join("-", $post["acorn"]);
        $post["sid"] = $this->sid;
        $post["issueId"] = $this->getUser("id");
        $post["addTime"] = nowTime();
        $studyDM->create($post, $study = $studyDM->newEntity());
        $studyDM->add($study)->flush($study);
        return $this->success(url("mobileConsoles_study_lists"));
//        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => url("mobileConsoles_study_rec", "types=rec")));
//      return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => url("mobileConsoles_study_rec","types=rec")));

    }

    /*
     * 我的学习
     */
    public function study() {
        $types = Q()->get->get("types") ?: 'study';
        $tabs_two = Q()->get->get("tabs_two") ?: 'study';
        $this->assign("active", $types);
        $this->assign("tabs_two", $tabs_two);

        $studyDetailDM = StudyDetailDModel::getInstance();
        $userDM = UserDModel::getInstance();

        $where = "s.sid =" . $this->sid . "AND sd.userId=" . $this->getUser('id');
        $lists = $studyDetailDM->name("sd")
            ->leftJoin("Study", "s", "s.id=sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->select("sd,s,u.id as userId")
            ->where($where)
            ->getArray(true);
        foreach ($lists as &$item) {
            $item['studyName'] = $userDM->getUserName($item['sd_userId']);
        }
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function release() {
        $types = Q()->get->get("types") ?: 'study';
        $tabs_two = Q()->get->get("tabs_two") ?: 'release';
        $this->assign("active", $types);
        $this->assign("tabs_two", $tabs_two);
        $studyDM = StudyDModel::getInstance();
        $study = $studyDM->name("s")->select("s,u")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->where("s.sid =" . $this->sid)
            ->getArray(true);
        $this->assign("study", $study);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function numbers($id) {
        $types = Q()->get->get("types") ?: 'study';
        $tabs_two = Q()->get->get("tabs_two") ?: 'release';
        $this->assign("active", $types);
        $this->assign("tabs_two", $tabs_two);
        $studyDetailDM = StudyDetailDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $where = "s.sid =" . $this->sid . "AND sd.stuId=" . $id;
        $lists = $studyDetailDM->name("sd")
            ->leftJoin("Study", "s", "s.id=sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->select("sd,s,u.id as userId")
            ->where($where)
            ->getArray(true);
        foreach ($lists as &$item) {
            $item['studyName'] = $userDM->getUserName($item['sd_userId']);
        }
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function judgement() {
        $types = Q()->get->get("types") ?: 'study';
        $tabs_two = Q()->get->get("tabs_two") ?: 'judgement';
        $this->assign("active", $types);
        $this->assign("tabs_two", $tabs_two);
        $studyDetailDM = StudyDetailDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $where = "sd.sid=" . $this->sid . "AND s.auditUser=" . $this->getUser('id') . "AND sd.status=0";
        $lists = $studyDetailDM->name("sd")
            ->leftJoin("Study", "s", "s.id=sd.stuId")
            ->leftJoin("User", "u", "u.id=s.issueId")
            ->select("sd,s,u.id as userId")
            ->where($where)
            ->order("sd.taskCount", "DESC")
            ->order("sd.status", "ASC")
            ->getArray(true);
        foreach ($lists as &$item) {
            $item['studyName'] = $userDM->getUserName($item['sd_userId']);
        }
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function check($id) {
        $studyDetailDM = StudyDetailDModel::getInstance();
        $where = "sd.sid=" . $this->sid . "AND s.auditUser=" . $this->getUser('id') . "AND sd.id=" . $id;
        $lists = $studyDetailDM->name("sd")
            ->leftJoin("Study", "s", "s.id=sd.stuId")
            ->leftJoin("Standard", "st", "st.id = s.standardId")
            ->select("sd,s,st.names as stNames")
            ->where($where)
            ->getOneArray(true);
        if (Q()->isGet()) {
            $types = Q()->get->get("types") ?: 'study';
            $tabs_two = Q()->get->get("tabs_two") ?: 'judgement';
            $this->assign("active", $types);
            $this->assign("tabs_two", $tabs_two);
            $this->assign("id", $id);
            $this->assign("lists", $lists);
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['acorn']) {
            return $this->error("请填写该学习的积分");
        }
        $acorn = explode("-", $lists['s_acorn']);
        if ($post['acorn'] < $acorn[0] || $post['acorn'] > $acorn[1]) {
            return $this->error("请填写该学习的积分范围内");
        }
        $acornDM = AcornDModel::getInstance();
        $result = $acornDM->addAcorn($this->sid, $lists['sd_userId'], $lists['sd_userId'], $lists['s_auditUser'], "6", $lists['s_standardId'], $post['acorn'], "完成[" . $lists['stNames'] . "]学习");
        if (!$result) {
            return $this->error($acornDM->getError());
        }
        //更新学习状态
        $studyDetailDM->name("sd")
            ->select("sd")
            ->where("sd.sid=" . $this->sid . "AND sd.id=" . $id)
            ->update(array("sd.status" => 1, "sd.doneTime" => nowTime()));
        return $this->success(url('mobileConsoles_study_judgement', 'tabs_two=judgement'));
    }

    public function studySetting() {
        $types = Q()->get->get("types") ?: 'studySetting';
        $this->assign("active", $types);
        $studySettingDM = StudySettingDModel::getInstance();
        $lists = $studySettingDM->name("ss")
            ->select("ss")
            ->where("ss.sid=" . $this->sid)
            ->order("ss.sort")
            ->getArray();
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function studySettingAdd() {
        $studySettingDM = StudySettingDModel::getInstance();
        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        $studySetting = $studySettingDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($studySetting) return $this->ajaxReturn(array("status" => "n", "info" => "存在同名的设置，请检查"));
        $post["types"] = 1;
        $post["sid"] = $this->sid;
        $studySettingDM->create($post, $studySetting = $studySettingDM->newEntity());
        $studySettingDM->add($studySetting)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "添加设置成功", "url" => url("mobileConsoles_study_studySetting")));
    }

    public function studySettingModify($id) {
        $studySettingDM = StudySettingDModel::getInstance();
        $lists = $studySettingDM->name("ss")
            ->select("ss")
            ->where("ss.sid=" . $this->sid . "AND ss.id=" . $id)
            ->getOneArray();

        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "记录获取失败", "url" => url("mobileConsoles_study_studySetting")));
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            return $this->display();
        }

        $post = Q()->post->all();

        $oldStudySetting = $studySettingDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($oldStudySetting && $oldStudySetting->getId() != $id) return $this->ajaxReturn(array("status" => "n", "info" => "存在同名的设置，请检查"));

        $taskSetting = $studySettingDM->find($id);
        $post["types"] = 1;
        $post["sid"] = $this->sid;
        $studySettingDM->create($post, $taskSetting);
        $studySettingDM->save($taskSetting)->flush($taskSetting);
        return $this->ajaxReturn(array("status" => "y", "info" => "修改设置成功", "url" => url("mobileConsoles_study_studySetting")));
    }

}