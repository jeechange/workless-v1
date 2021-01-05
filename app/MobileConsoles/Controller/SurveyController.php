<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-14
 * Time: 10:33
 */

namespace MobileConsoles\Controller;


use Admin\Entity\Survey;

class SurveyController extends CommonController {
    public function lists() {
        $active = Q()->get->get("active") ?: 'Partake';
        $surveyDM = new \Admin\DModel\SurveyDModel();
        $where = "s.sid=" . $this->sid;
        $lists = $surveyDM->name("s")
            ->leftJoin("User", "u", "u.id=s.issue")
            ->leftJoin("Standard", "sd", "sd.id=s.standId")
            ->select("s,sd.names as s_standName,u.fullName as s_issue")
            ->where($where)
            ->order("s.status", "ASC")
            ->order("s.id", "DESC")
            ->getArray(true);
        if ($active == "Partake") {
            $lists = $this->mylists();
        }
        $this->assign("active", $active);
        $this->assign("lists", $lists);
        if ($active == "Release") {
            return $this->display("release.latte");
        }


        return $this->display();

    }

    public function mylists() {
        $surveyDM = new \Admin\DModel\SurveyDModel();
        $surveyResultDM = new \Admin\DModel\SurveyResultDModel();
        $where = "s.sid=" . $this->sid . " AND sr.userId=" . $this->getUser('id');
        $lists = $surveyDM->name("s")
            ->leftJoin("User", "u", "u.id=s.issue")
            ->leftJoin("Standard", "sd", "sd.id=s.standId")
            ->leftJoin("SurveyResult", "sr", "sr.surveyId=s.id")
            ->select("s,sr.id as s_resultId,sd.names as s_standName,u.fullName as s_issue")
            ->where($where)
            ->order("s.status", "ASC")
            ->order("s.id", "DESC")
            ->getArray(true);
        foreach ($lists as $key => $item) {
            $surveyResult = $surveyResultDM->name("sr")->select("sr.status")->where("sr.surveyId=" . $item['s_id'] . "AND sr.userId=" . $this->getUser("id"))->getOneArray();

            if ($surveyResult['status'] == 1) {
                $surveyResult['status'] = "已评分";
            } else {
                $surveyResult['status'] = "未评分";
            }
            $lists[$key]['result'] = $surveyResult['status'];
        }
        return $lists;

    }

    public function modify($id) {
        $active = Q()->get->get("active") ?: 'Partake';
        $surveyDM = new \Admin\DModel\SurveyDModel();
        $userDM = new \Admin\DModel\UserDModel();
        $surveyResulteDM = new \Admin\DModel\SurveyResultDModel();
        $surveyAcornDM = new \Admin\DModel\SurveyAcornDModel();
        $standardDM = new \Admin\DModel\StandardDModel();

        if (Q()->isGet()) {
            $where = "s.sid =:sid AND s.id=:sId";
            $parameter = array(
                "sid" => $this->sid,
                "sId" => $id
            );
            $lists = $surveyDM->name("s")
                ->leftJoin("User", "u", "u.id=s.issue")
                ->leftJoin("Standard", "sd", "sd.id=s.standId")
                ->leftJoin("SurveyGroup", "sg", "sg.id=s.surveyTeam")
                ->select("s,u.fullName as s_issue,sd.names as s_standName,sg.names as s_surveyGroup,sd.acorn as s_acorn")
                ->where($where)
                ->setParameter($parameter)
                ->getOneArray(true);
            $surveyResulte = $surveyResulteDM->name("sr")->select("sr")->where("sr.surveyId=" . $lists['s_id'] . " AND sr.userId=" . $this->getUser('id'))->getOneArray();

            $lists['surveyObject'] = $userDM->name("u")->select("u.id,u.fullName")->where("u.id in({$lists["s_surveyObject"]})")->getArray();
            $lists['userScore'] = json_decode($lists['s_userScore'], true);
            $lists['userCount'] = count(explode(",", $lists["s_surveyObject"]));

            $total = 0;
            $acorn = array();
            foreach ($lists['userScore'] as $item) {
                $total += $item['acorn'];
                $acorn[] = $item['acorn'];
            }
            // 获取重复数据的数组
            $unique_arr = array_count_values($acorn);
            $lists['s_repeat'] = max($unique_arr);//重复得分的个数
            $lists['s_max'] = max($acorn);//最大值
            $lists['s_min'] = min($acorn);//最小值
            arsort($unique_arr);//key倒序排序
            $first_key = key($unique_arr);
            $lists['s_repeatAcorn'] = $first_key;//重复最多的分数
            $lists['s_userCount'] = count(explode(",", $lists["s_surveyObject"]));//被调查对象的数目
            $lists['s_average'] = round($total / $lists['s_userCount'], 2);//平均分
            $this->assign("lists", $lists);
            $this->assign("surveyResulte", $surveyResulte);
            $this->assign("active", $active);
            return $this->display();
        }
        $post = Q()->post->all();
        $surveyResulte = $surveyResulteDM->find($post['sResulteId']);
        if (!$surveyResulte) {
            return $this->ajaxReturn(array("status" => "n", "info" => "该评分记录不存在，请检查"));
        }
        $total = 0;
        foreach ($post['names'] as $key => $item) {
            $userScore[$key] = array(
                "fullName" => $item,
                "acorn" => intval($post['acorn'][$key]) ?: 0,
            );
            $acorn = intval($post['acorn'][$key]) ?: 0;
            if ($acorn > $post['single']) {
                return $this->ajaxReturn(array("status" => "n", "info" => "单个调查成员对所有被调查对象在此项调查内容中的总评分不得超过" . $post['single'] . "分"));
            }
            $total += intval($post['acorn'][$key]) ?: 0;
        }
        if ($total > $surveyResulte->getTotal()) {
            return $this->ajaxReturn(array("status" => "n", "info" => "总评分不得超过" . $surveyResulte->getTotal() . "分"));
        }

        //相同分数者不能超过总数的 30%
        $unique_arr = array_count_values($post['acorn']);
        $repeat = max($unique_arr);//重复得分最大个数
        if ($repeat > 2) {
            foreach ($unique_arr as $a => $num) {
                if (($num * $a) > $surveyResulte->getTotal() * 0.3) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "相同分数者不能超过总数的 30%"));
                }
                continue;
            }
        }
        $post['scoreTime'] = nowTime();
        $post['userScore'] = version_compare(phpversion(), '5.4.0') > 0 ? json_encode($userScore, JSON_UNESCAPED_UNICODE) : json_encode($userScore);
        $surveyResulte->setUserScore($post['userScore']);
        $surveyResulte->setScoreTime(nowTime());
        $surveyResulte->setStatus(1);
        $surveyResulteDM->save($surveyResulte)->flush();

        //查出同一调查项目未完成评价的数量
        $nofinishSurveyResulte = $surveyResulteDM->name("sr")->where("sr.surveyId=" . $surveyResulte->getSurveyId() . "AND sr.status=0")->count();

        //如果等于0，那么该调查评价已经完成，更新调查表的状态
        if ($nofinishSurveyResulte == 0) {
            //算出所有的评价表中该用户的总分
            $acorn = array();
            $resulteLists = $surveyResulteDM->name("sr")->where("sr.surveyId=" . $surveyResulte->getSurveyId() . "AND sr.status=1")->getArray();
            $count = count($resulteLists);
            foreach ($resulteLists as $key => $item) {
                $userScore = json_decode($item['userScore'], true);
                foreach ($userScore as $k => $values) {
                    $acorn[$k][] = $values['acorn'];
                }
            }
            $UserScore = array();
            /**@var  $survey Survey */
            $survey = $surveyDM->name("s")->where("s.id=" . $surveyResulte->getSurveyId() . " AND s.status=1")->getOneObject();
            if (!$survey) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "该发布调查状态有误，请联系相关人员"));
            }
            foreach ($acorn as $i => $val) {
                if ($count < 0) {
                    continue;
                }
                $userAcorn = array_sum($val) / $count;//平均调查分
                $UserScore[$i] = array(
                    "fullName" => $userDM->getUserName($i),
                    "acorn" => round(array_sum($val) / $count, 2)//平均调查分用来存survey的依据
                );
                $result = $surveyAcornDM->addAcorn($this->sid, $i, $survey->getIssue(), 0, $survey->getScId(), $survey->getStandId(), $userAcorn, "完成[" . $standardDM->getStandName($survey->getStandId(), $this->sid) . "]标准", $standardDM->getStandName($survey->getStandId(), $this->sid) . "的调查", nowTime());
                if (!$result) {
                    return $this->ajaxReturn(array("status" => "n", "info" => $surveyAcornDM->getError()));
                }
            }
            $survey->setStatus(2);
            $survey->setUserScore(version_compare(phpversion(), '5.4.0') > 0 ? json_encode($UserScore, JSON_UNESCAPED_UNICODE) : json_encode($UserScore));
            $surveyDM->save($survey)->flush();
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "评分成功"));
    }


}