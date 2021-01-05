<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class SurveyAcornDModel extends DModel {

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
        return new \Admin\Entity\SurveyAcorn();
    }


    public function getScIdMemo($scId) {
        return $this->scIdMemo[$scId] ?: "--";
    }

    public function toUserId($executors) {
        if (!$executors) return "";

        $executorIds = explode(",", $executors);
        $staffDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $staffDM->find($id);
            if ($user) $users[]['id'] = $user->getId();
        }
        return $users;
    }

    /*
     * 添加影响力
     * 括号内为数据库字段
     * $sid(sid) => 区别公司, $userId(userId) => 区别员工, $fromUser(from_user) => 申请人, $auditor（auditor）=> 审核人,$standardClassifyId（sc_id）=> 标准大项ID
     * $standardId（names）=>标准小项ID, $acorn(acorn)=>影响力,$memo(memo)=>备注,$sysMemo(sysMemo)=>备注2
     */
    public function addAcorn($sid, $userId, $fromUser, $auditor, $standardClassifyId, $standardId, $acorn, $memo = "", $sysMemo = "", $addTime = "") {
        if (!$sid) {
            $this->error = "公司ID不能为空";
            return false;
        }
        if (!$userId) {
            $this->error = "员工ID不能为空";
            return false;
        }
        if ($acorn == 0) {
            return true;

        }
        if ($acorn > 0) {
            $parameter = array("sid" => $sid, "userId" => $userId, "surveyAcorn" => $acorn);
            $stat = CompanyMemberDModel::getInstance()->name("s")->where("s.userId = :userId AND s.sid = :sid")
                ->setParameter($parameter)
                ->setInc("s.surveyAcorn", ":surveyAcorn");
            if (!$stat) {
                $this->error = sprintf("员工不存在：%d", $userId);
                return false;
            }
        } else {
            $parameter = array("sid" => $sid, "userId" => $userId, "surveyAcorn" => abs($acorn));
            $stat = CompanyMemberDModel::getInstance()->name("s")->where("s.userId = :userId and s.sid = :sid")
                ->setParameter($parameter)
                ->setDec("s.surveyAcorn", ":surveyAcorn");
            if (!$stat) {
                $this->error = sprintf("员工不存在：%d", $userId);
                return false;
            }
        }
        $dqlParameter = array(
            "sid" => $sid,
            "userId" => $userId,
            "fromUser" => $fromUser,
            "auditor" => $auditor,
            "scId" => $standardClassifyId,
            "names" => $standardId,
            "acorn" => $acorn,
            "addTime" => now(),
            "status" => 1,
            "types" => $acorn > 0 ? 1 : 2,
            "memo" => $memo,
            "sysMemo" => $sysMemo,
        );
        $dql = "INSERT INTO #prefix#survey_acorn "
            . "(sid,user_id,from_user,auditor,sc_id,names,acorn,balance,add_time,status,types,memo,sys_memo)"
            . "SELECT s.sid,s.user_id,:fromUser,:auditor,:scId,:names,:acorn,s.survey_acorn,:addTime,:status,:types,:memo,:sysMemo"
            . " FROM #prefix#company_member s WHERE s.user_id = :userId AND s.sid = :sid";
        $sql = $this->convertSQL($dql);
        try {
            $stmt = $this->manager->getConnection()->prepare($sql);
            foreach ($dqlParameter as $key => $val) {
                $stmt->bindValue(":{$key}", $val);
            }
            $stmt->execute($dqlParameter);

            $addRankingDM = new \Admin\DModel\RankingDModel();
            $addRankingDM->addRanking($dqlParameter, $addTime);

            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }

    }

    private function convertSQL($dql) {
        $config = C("database.default");
        return str_replace("#prefix#", $config["prefix"], $dql);
    }

    /**影响力的产生时间
     * @param $the_time
     * @return string
     */
    public function time_tran($the_time) {
        $now_time = date("Y-m-d H:i:s", time());
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $t = $now_time - $show_time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }

}