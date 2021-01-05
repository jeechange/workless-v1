<?php

namespace Admin\DModel;

use Admin\Entity\Acorn;
use Admin\Entity\AcornAudit;
use phpex\DModel\DModel;
use phpex\Helper\Search\RichElement\Date;

class RankingDModel extends DModel {
    private $typeMemo = array(
        "0" => "特殊",
        "1" => "加分",
        "2" => "减分",
    );

    public $cycle = array(
        "1" => '今日',
        "2" => '昨日',
        "3" => '本周',
        "4" => '上周',
        "5" => '本月',
        "6" => '上月',
        "7" => '本季',
        "8" => '本年',
        "9" => '去年',
        "10" => '总分榜',
    );

    public function getBTypesMemo($bTypes = null) {
        return !$bTypes ?: $this->cycle[$bTypes] ?: $this->cycle;
    }


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
        return new \Admin\Entity\Ranking();
    }

    /**添加排行
     * @param array $result
     * @param string $addTime
     */
    public function addRanking($result, $addTime = "") {
        if (!$result) {
            return;
        }
        $groupId = 0; //组id
        $staffGroupDM = StaffGroupDModel::getInstance();
        $staffGroup = $staffGroupDM->name("sg")->select("sg")->where("sg.sid=" . $result['sid'])->getArray();
        if ($staffGroup) {
            foreach ($staffGroup as $item) {
                if (!in_array($result['userId'], explode(",", $item['members']))) {
                    continue;
                }
                $groupId = $item['id'];
            }
        }
        //部门id
        $StaffDM = StaffDModel::getInstance();
        $dep_id = $StaffDM->name('s')
            ->select('s.department')
            ->where("s.sid={$result['sid']} AND s.userId = {$result['userId']}")
            ->setMax(1)
            ->getOneArray(false, false)['department'];
        $dep_id = $dep_id ? $dep_id : 0;

        $day = $addTime ? date("Y-m-d", strtotime(totime($addTime))) : date("Y-m-d");
        $week = $addTime ? date("W", strtotime(totime($addTime))) : date("W");
        $year = $addTime ? date("Y", strtotime(totime($addTime))) : date("Y");
        $month = $addTime ? date("m", strtotime(totime($addTime))) : date("m");
        $this->newEntity();

        //查出父级
        $standardClassifyDM = StandardClassifyDModel::getInstance();
        $pid = $result['scId'];
        if ($result['scId']) {
            $standardClassify = $standardClassifyDM->name("sc")->select("sc.pid")->where("sc.id=" . $result['scId'] . " AND sc.pid!=0")->setMax(1)->getOneArray(false, false);
            $pid = $standardClassify['pid'];
            if ($pid == 0) {
                $pid = $result['scId'];
            }
        }

        $post = array(
            "sid" => $result['sid'],//企业id
            "userId" => $result['userId'],//用户id
            "issueId" => $result['auditor'],//审核人id
            "applicant" => $result['fromUser'],//申請人id
            "standId" => $result['names'],//标准id
            "scId" => $result['scId'],//标准类型id
            "acorn" => $result['acorn'],//影响力
            "addTime" => $addTime ? $addTime : nowTime(),//添加时间
            "types" => $result['acorn'] > 0 ? 1 : 2,//影响力类型
            "day" => $day,//日
            "week" => $week,//周
            "month" => $month,//月
            "seasons" => ceil($month / 3),//季度
            "year" => $year,//年
            "groupId" => $groupId,//组id
            "depId" => $dep_id,//部门id
            "status" => 1,
            "memo" => $result['memo'],//标准内容
            "pId" => $pid,//标准父级
        );
        $this->create($post, $lists = $this->newEntity());
        $this->save($lists)->flush();
    }

    /**
     * 选择多条件排名
     * @return \array[]
     */
    public function selectRanking($sid = 0, $bTypes = 1) {
        if ($bTypes == 1) {//今天
            $start = date("Y-m-d 00:00:00");//一天的 开始
            $end = date("Y-m-d 23:59:59");//一天的结束
            $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end)";
            $parameter = array(
                "sid" => $sid,
                'start' => $start,
                'end' => $end,
            );
        } elseif ($bTypes == 2) {//昨天
            $start = date("Y-m-d 00:00:00", strtotime("-1 day"));//一天的 开始
            $end = date("Y-m-d 23:59:59", strtotime("-1 day"));//一天的结束
            $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end)";
            $parameter = array(
                "sid" => $sid,
                'start' => $start,
                'end' => $end,
            );
        } elseif ($bTypes == 3) {//本周
            $week = date("W");
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "week" => $week,
                "year" => $year
            );
        } elseif ($bTypes == 4) {//上周
            $week = date("W", strtotime("-1 week"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "week" => $week,
                "year" => $year
            );
        } elseif ($bTypes == 5) {//本月
            $month = date("m");
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "month" => $month,
                "year" => $year
            );
        } elseif ($bTypes == 6) {//上月
            $month = date("m", strtotime("-1 month"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "month" => $month,
                "year" => $year
            );
        } elseif ($bTypes == 7) {//季度
            $seasons = ceil(date("m") / 3);
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.seasons=:seasons AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "seasons" => $seasons,
                "year" => $year
            );
        } elseif ($bTypes == 8) {//年
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "year" => $year
            );
        } elseif ($bTypes == 9) {//去年
            $year = date("Y", strtotime("-1 year"));
            $where = "rk.sid=:sid AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "year" => $year
            );
        } elseif ($bTypes == 10) {//总排行
            $where = "rk.sid=:sid";
            $parameter = array(
                "sid" => $sid,
            );
        }
        return array("where" => $where, "parameter" => $parameter);

    }

    /**
     * 环比数值
     */
    public function ringRatio($sid, $bTypes) {
        if ($bTypes == 1) {//今天
            $start = date("Y-m-d 00:00:00", strtotime("-1 day"));//一天的 开始
            $end = date("Y-m-d 23:59:59", strtotime("-1 day"));//一天的结束
            $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end)";
            $parameter = array(
                "sid" => $sid,
                'start' => $start,
                'end' => $end,
            );
        } elseif ($bTypes == 2) {//昨天
            $start = date("Y-m-d 00:00:00", strtotime("-2 day"));//一天的 开始
            $end = date("Y-m-d 23:59:59", strtotime("-2 day"));//一天的结束
            $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end)";
            $parameter = array(
                "sid" => $sid,
                'start' => $start,
                'end' => $end,
            );
        } elseif ($bTypes == 3) {//本周
            $week = date("W", strtotime("-1 week"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "week" => $week,
                "year" => $year
            );
        } elseif ($bTypes == 4) {//上周
            $week = date("W", strtotime("-2 week"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "week" => $week,
                "year" => $year
            );
        } elseif ($bTypes == 5) {//本月
            $month = date("m", strtotime("-1 month"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "month" => $month,
                "year" => $year
            );
        } elseif ($bTypes == 6) {//上月
            $month = date("m", strtotime("-2 month"));
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "month" => $month,
                "year" => $year
            );
        } elseif ($bTypes == 7) {//上个季度
            $seasons = ceil(date("m", strtotime("-1 month")) / 3);
            $year = date("Y");
            $where = "rk.sid=:sid AND rk.seasons=:seasons AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "seasons" => $seasons,
                "year" => $year
            );
        } elseif ($bTypes == 8) {//去年
            $year = date("Y", strtotime("-1 year"));
            $where = "rk.sid=:sid AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "year" => $year
            );
        } elseif ($bTypes == 9) {//前年
            $year = date("Y", strtotime("-2 year"));
            $where = "rk.sid=:sid AND rk.year=:year";
            $parameter = array(
                "sid" => $sid,
                "year" => $year
            );
        }
        return array("where" => $where, "parameter" => $parameter);
    }


    /**昨日排名
     * @return \array[]
     */
    public function yesRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $start = date("Y-m-d 00:00:00", strtotime("-1 day"));//一天的 开始
        $end = date("Y-m-d 23:59:59", strtotime("-1 day"));//一天的结束

        $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end)";
        $parameter = array(
            "sid" => $sid,
            'start' => $start,
            'end' => $end,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 20)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**周排名
     * @return \array[]
     */
    public function weekRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $week = date("W");
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "week" => $week,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();

        return $lists;
    }

    /**本月个人总积分，总奖分，总扣分
     * @return \array[]
     */
    public function personalMonthTotal($sid, $userId) {
        $RankingDM = RankingDModel::getInstance();
        $month = date("m");
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year AND rk.userId = :userId";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year,
            "userId" => $userId
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn,rk.types as types")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.types")
            ->getArray(false, false);
        $award = 0;
        $deduction = 0;
        foreach ($lists as $v) {
            if ($v['types'] == 1) {
                $award = $v['acorn'];
            } else {
                $deduction = $v['acorn'];
            }
        }
        $total = $award + $deduction;
        $deduction = abs($deduction);

        return array('total' => $total, 'award' => $award, 'deduction' => $deduction);
    }

    /**本月个人总排名
     * @return \array[]
     */
    public function personalMonthTotalRank($sid, $userId) {
        $RankingDM = RankingDModel::getInstance();
        $month = date("m");
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn,rk.userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.userId")
            ->getArray(false, false);
        $lists = $lists ?: [];
        $acorn = array_column($lists, 'acorn');
        array_multisort($acorn, SORT_DESC, $lists);
        $sort = count($lists) + 1;
        foreach ($lists as $k => $v) {
            if ($v['userId'] == $userId) {
                $sort = $k + 1;
                break;
            }
        }

        return $sort;
    }

    /**本月个人部门排名
     * @return \array[]
     */
    public function personalMonthDepartmentRank($sid, $userId) {
        $RankingDM = RankingDModel::getInstance();
        $month = date("m");
        $year = date("Y");

        //部门id
        $StaffDM = StaffDModel::getInstance();
        $dep_id = $StaffDM->name('s')->select('s.department')->where("s.sid={$sid} AND s.userId = {$userId}")->setMax(1)->getOneArray(false, false)['department'];
        if (!$dep_id) return 0;

        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year AND rk.depId=:depId";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year,
            "depId" => $dep_id
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn,rk.userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.userId")
            ->getArray(false, false);
        $lists = $lists ?: [];
        $acorn = array_column($lists, 'acorn');
        array_multisort($acorn, SORT_DESC, $lists);
        $sort = count($lists) + 1;
        foreach ($lists as $k => $v) {
            if ($v['userId'] == $userId) {
                $sort = $k + 1;
                break;
            }
        }

        return $sort;
    }

    /**连续7日积分数据
     * @return \array[]
     */
    public function sevenDayData($sid, $userId) {
        $RankingDM = RankingDModel::getInstance();

        $where = "rk.sid=:sid AND rk.userId = :userId  AND rk.types = 1 AND rk.day>=:day";
        $parameter = array(
            "sid" => $sid,
            "userId" => $userId,
            "day" => date("Y-m-d", strtotime('-6 days')),
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn,rk.day")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.day")
            ->getArray(false, false);
        $awardLists = [];
        foreach ($lists as $v) {
            $awardLists[$v['day']] = $v['acorn'];
        }
        $award = array(
            date("Y-m-d", strtotime('-6 days')) => 0,
            date("Y-m-d", strtotime('-5 days')) => 0,
            date("Y-m-d", strtotime('-4 days')) => 0,
            date("Y-m-d", strtotime('-3 days')) => 0,
            date("Y-m-d", strtotime('-2 days')) => 0,
            date("Y-m-d", strtotime('-1 days')) => 0,
            date("Y-m-d") => 0,
        );
        if (count($awardLists) < 7) {
            $awardLists = array_merge($award, $awardLists);
        }
        $where = "rk.sid=:sid AND rk.userId = :userId  AND rk.types = 2 AND rk.day>=:day";
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn,rk.day")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.day")
            ->getArray(false, false);
        $deAwardLists = [];
        foreach ($lists as $v) {
            $deAwardLists[$v['day']] = abs($v['acorn']);
        }
        if (count($deAwardLists) < 7) {
            $deAwardLists = array_merge($award, $deAwardLists);
        }
        return ['award' => $awardLists, 'deAward' => $deAwardLists];
    }


    /**
     * 积分分析数据-条件
     * @param int $sid 公司id
     * @param int $generalId 条件id
     * @param int $btypes 0:企业分析,1:部门分析,2:个人分析
     * @param int $types 1:加分 2.减分
     * @return array|bool
     */
    public function dayData($sid, $generalId = 0, $btypes = 0, $types = 0, $times = '', $startTime = '', $endTime = '') {
        $RankingDM = RankingDModel::getInstance();
        if (!$sid || $sid < 0) {
            $this->error = '缺少sid';
            return false;
        }

        $where = "rk.sid=" . $sid;

        if ($btypes == 1) {//部门
            $where .= " AND rk.depId=" . $generalId;
        } elseif ($btypes == 2) {//个人
            $where .= " AND rk.userId=" . $generalId;
        }

        if ($types != 0) {
            $where .= " AND rk.types=" . $types;
        }

        if ($times && !$startTime && !$endTime) {//条件
            $w = date("W");//本周
            $month = date("m");//本月
            $year = date("Y");//本年
            if ($times == 'week') {
                $where .= "AND rk.year= " . $year . " AND rk.week=" . $w;
            } elseif ($times == 'month') {
                $where .= "AND rk.year= " . $year . " AND rk.month=" . $month;
            } elseif ($times == 'year') {
                $where .= " AND rk.year=" . $year;
            }
        } else {
            if ($startTime && !$endTime) {
                $startTime = date("Y-m-01", strtotime($startTime));
                $endTime = date("Y-m-d", strtotime($startTime . "+" . date('t', strtotime($startTime)) . "day"));
                $where .= " AND rk.day>='" . $startTime . "' AND rk.day<'" . $endTime . "'";
            } elseif ($endTime && !$startTime) {
                $startTime = date("Y-m-01", strtotime($endTime));
                $endTime = date("Y-m-d", strtotime($startTime . "+" . date('t', strtotime($startTime)) . "day"));
                $where .= " AND rk.day>='" . $startTime . "' AND rk.day<'" . $endTime . "'";
            } else {
                $where .= " AND  rk.day>='" . $startTime . "' AND rk.day<='" . $endTime . "' ";
                $strtime = floor((strtotime($endTime) - strtotime($startTime)) / 84600);
                if ($strtime <= 7) {
                    $times = 'week';
                } elseif ($strtime > 7 && $strtime <= 28) {
                    $times = 'month';
                } elseif ($strtime > 28 && $strtime <= 365) {
                    $times = 'year';
                } else {
                    $times = 'crossYear';//跨年度
                }
            }
        }

        $awardLists = [];
        if ($times == 'year') {
            $lists = $RankingDM->name("rk")
                ->select("sum(rk.acorn) as acorn,rk.month as day,rk.year")
                ->where($where)
                ->groupBy("rk.month")
                ->getArray(false, false);

            foreach ($lists as $v) {
                $d = mktime("0", "0", "0", $v['day'], "01", $v['year']);
                $awardLists[date("Y-m", $d)] = $v['acorn'];
            }

        } elseif ($times == 'crossYear') {
            $lists = $RankingDM->name("rk")
                ->select("sum(rk.acorn) as acorn,rk.year as day")
                ->where($where)
                ->groupBy("rk.year")
                ->getArray(false, false);
            foreach ($lists as $v) {
                $awardLists[$v['day'] . "年"] = $v['acorn'];
            }

        } else {
            $lists = $RankingDM->name("rk")
                ->select("sum(rk.acorn) as acorn,rk.day")
                ->where($where)
                ->groupBy("rk.day")
                ->getArray(false, false);
            foreach ($lists as $v) {
                $awardLists[$v['day']] = $v['acorn'];
            }
        }
        $m = time();
        if ($startTime && !$endTime) {
            $m = strtotime(date("Y-m-01", strtotime($startTime)));
        } elseif ($endTime && !$startTime) {
            $m = strtotime(date("Y-m-01", strtotime($endTime)));
        } elseif ($endTime && $startTime) {
            $m = strtotime($startTime);
        }

        if ($times == 'week') {
            $timestr = $m;
            $now_day = date('w', $timestr);
            $sunday_str = $timestr - $now_day * 60 * 60 * 24;
            $sunday = date('Y-m-d', $sunday_str);
            for ($i = 0; $i < 7; $i++) {
                $award[date('Y-m-d', strtotime($sunday . "+ " . $i . 'day'))] = 0;
            }
        } elseif ($times == 'month') {
            $start = date("Y-m-01", $m);
            $count = date('t', $m);
            for ($i = 0; $i < $count; $i++) {
                $award[date('Y-m-d', strtotime($start . "+ " . $i . 'day'))] = 0;
            }
        } elseif ($times == 'year') {
            $start = date("Y-01", $m);
            for ($i = 0; $i < 12; $i++) {
                $award[date('Y-m', strtotime($start . "+ " . $i . 'month'))] = 0;
            }
        } elseif ($times == 'crossYear') {
            $start = date("Y-m-d", $m);
            for ($i = 0; $i < 5; $i++) {
                $award[date('Y', strtotime($start . "+ " . $i . 'year')) . "年"] = 0;
            }
        }
        $awardLists = array_merge($award, $awardLists);

        return $awardLists;
    }


    /**本月排名
     * @return \array[]
     */
    public function monthRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $month = date("m");
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**本年排名
     * @return \array[]
     */
    public function yearRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**总排行
     * @return \array[]
     */
    public function totalRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid";
        $parameter = array(
            "sid" => $sid
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**昨天小组排行
     * @return \array[]
     */
    public function yesGroupRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $start = date("Y-m-d 00:00:00", strtotime("-1 day"));//一天的 开始
        $end = date("Y-m-d 23:59:59", strtotime("-1 day"));//一天的结束
        $where = "rk.sid=:sid AND rk.groupId>0 AND (rk.addTime BETWEEN :start AND :end)";
        $parameter = array(
            "sid" => $sid,
            'start' => $start,
            'end' => $end,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("sg.id")
            ->getArray();
        return $lists;
    }

    /**本周小组排行
     * @return \array[]
     */
    public function weekGroupRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $week = date("W");
        $year = date("Y");

        $where = "rk.sid=:sid AND rk.groupId>0 AND rk.week=:week AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            'week' => $week,
            'year' => $year,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("sg.id")
            ->getArray();
        return $lists;
    }

    /**本年小组排行
     * @return \array[]
     */
    public
    function yearGroupRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date("Y");

        $where = "rk.sid=:sid AND rk.groupId>0 AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            'year' => $year,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("sg.id")
            ->getArray();
        return $lists;
    }

    /**个人排名
     * @return \array[]
     */
    public
    function persRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid";
        $parameter = array(
            "sid" => $sid,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**小组总排行
     * @return \array[]
     */
    public
    function totalGroupRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid AND rk.groupId>0";
        $parameter = array(
            "sid" => $sid,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("sg.id")
            ->getArray();
        return $lists;
    }

    /**上个月排行
     * @return \array[]
     */
    public function lastMonthRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $month = date("m", strtotime("-1 month"));
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**上周排行
     * @return \array[]
     */
    public function lastWeekRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $week = date("W", strtotime("-1 week"));
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.week=:week AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "week" => $week,
            "year" => $year
        );

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**去年排名
     * @return \array[]
     */
    public
    function lastYearRanking($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date("Y", strtotime("-1 year"));
        $where = "rk.sid=:sid AND rk.year=:year";
        $parameter = array(
            "sid" => $sid,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->groupBy("u.id")
            ->getArray();
        return $lists;
    }

    /**加分统计
     * @return \phpex\Foundation\Response
     */
    public
    function plusPoints($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.year=:year AND rk.types=1";
        $parameter = array(
            "sid" => $sid,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("Standard", "s", "s.id=rk.standId")
            ->select("sum(rk.acorn) as acorn,s.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("rk.standId")
            ->getArray();
        return $lists;
    }

    /**减分统计
     * @return \phpex\Foundation\Response
     */
    public function minusPoints($sid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.year=:year AND rk.types=2";
        $parameter = array(
            "sid" => $sid,
            "year" => $year
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("Standard", "s", "s.id=rk.standId")
            ->select("sum(rk.acorn) as acorn,s.names as names")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("rk.standId")
            ->getArray();

        return $lists;
    }

    /**
     * @name 个人排行詳情
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function personalRdetail($id, $sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];
        if ($where) {
            $where .= " AND rk.userId=" . $id;
        } else {
            $where = "rk.userId=" . $id;
        }
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Standard", "s", "s.id=rk.standId")
            ->select("rk.id,rk.acorn,rk.addTime,rk.memo,u.id as userId,u.fullName,s.names as standName")
            ->where($where)
            ->setParameter($parameter)
            ->order("rk.addTime", "DESC")
            ->getArray();
        return $lists;
    }

    /**
     * @name 部门排行詳情
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function departmentRDetail($id, $sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();

        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];
        if ($where) {
            $where .= " AND d.sid=" . $sid . "AND d.id=" . $id;
        } else {
            $where = "d.sid=" . $sid . "AND d.id=" . $id;
        }

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->leftJoin("Standard", "sd", "sd.id=rk.standId")
            ->select("rk.id,rk.acorn,rk.addTime,rk.memo,u.id as userId,u.fullName,sd.names as standName")
            ->where($where)
            ->setParameter($parameter)
            ->order("rk.addTime", "DESC")
            ->getArray();
        return $lists;
    }

    /**
     * @name 小组排行詳情
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function groupRDetail($id, $sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];
        if ($where) {
            $where = $where . " AND rk.groupId>0 AND sg.id=" . $id;
        } else {
            $where = "rk.groupId>0 AND sg.id=" . $id;
        }
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->leftJoin("Standard", "s", "s.id=rk.standId")
            ->select("rk.id,rk.acorn,rk.addTime,rk.memo,u.id as userId,u.fullName,s.names as standName")
            ->where($where)
            ->setParameter($parameter)
            ->order("rk.addTime", "DESC")
            ->getArray();
        return $lists;
    }


    /**
     *个人统计详情
     * @param $id
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function personalSdetail($id, $sid, $bTypes, $types) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "  rk.userId =:userId AND rk.sid =:sid AND rk.month=:month AND rk.year=:year";
        $parameter = array(
            "userId" => $id,
            "sid" => $sid,
            "month" => $bTypes,
            "year" => date("Y")
        );
        if ($types != 0) {
            $where .= " AND rk.types=" . $types;
        }

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Standard", "sd", "sd.id=rk.standId")
            ->select("rk.id,rk.acorn,rk.addTime,rk.memo,u.id as userId,u.fullName,sd.names as standName")
            ->where($where)
            ->setParameter($parameter)
            ->order("rk.addTime", "DESC")
            ->getArray();
        return $lists;
    }

    /**
     * 部门统计详情
     * @param $id
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function departmentSdetail($id, $sid, $bTypes, $types) {

        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "  d.id =:userId AND rk.sid =:sid AND rk.month=:month AND d.sid=:dSid AND rk.year=:year";
        $parameter = array(
            "userId" => $id,
            "sid" => $sid,
            "month" => $bTypes,
            "dSid" => $sid,
            "year" => date("Y")
        );
        if ($types != 0) {
            $where .= " AND rk.types=" . $types;
        }

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->leftJoin("Standard", "sd", "sd.id=rk.standId")
            ->select("rk.id,rk.acorn,rk.addTime,rk.memo,u.id as userId,u.fullName,sd.names as standName")
            ->where($where)
            ->setParameter($parameter)
            ->order("rk.addTime", "DESC")
            ->getArray();
        return $lists;
    }


    /**本月积分
     * @return \array[]
     */
    public function monthAcorn($sid = 0, $id) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $month = date("m");
        $year = date("Y");
        $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year AND rk.userId=:userId";
        $parameter = array(
            "sid" => $sid,
            "month" => $month,
            "year" => $year,
            "userId" => $id,
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("rk.userId")
            ->getOneArray();
        return $lists;
    }

    /**昨天积分
     * @return \array[]
     */
    public function yesAcorn($sid = 0, $id) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $start = date("Y-m-d 00:00:00", strtotime("-1 day"));//一天的 开始
        $end = date("Y-m-d 23:59:59", strtotime("-1 day"));//一天的结束
        $where = "rk.sid=:sid AND (rk.addTime BETWEEN :start AND :end) AND rk.userId=:userId";
        $parameter = array(
            "sid" => $sid,
            'start' => $start,
            'end' => $end,
            "userId" => $id,
        );
        $lists = $RankingDM->name("rk")
            ->select("sum(rk.acorn) as acorn")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("rk.userId")
            ->getOneArray();
//        return $lists['acorn'] > 0 ? $lists['acorn'] : 0;
        return $lists['acorn'] != '' ? $lists['acorn'] : 0;
    }

    /**
     * 2019-05-10
     * 具体个人排名第几位（全公司）
     * @param $sid
     * @param $bTypes
     * @param $userId
     * @return int|string
     */
    public function sort($sid, $bTypes, $userId, $pid = 0) {
        $RankingDM = new \Admin\DModel\RankingDModel();

        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];

        if ($pid > 0) {
            $where .= " AND s.sid=" . $sid . " AND s.department=" . $pid;
            $lists = $RankingDM->name("rk")
                ->leftJoin("Staff", "s", "s.userId=rk.userId")
                ->select("sum(rk.acorn) as acorn,s.fullName as userName,s.userId as userId")
                ->where($where)
                ->setParameter($parameter)
                ->groupBy("s.userId")
                ->order("acorn", "DESC")
                ->getArray();
        } else {
            $where .= " AND u.sid=" . $sid;
            $lists = $RankingDM->name("rk")
                ->leftJoin("User", "u", "u.id=rk.userId")
                ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
                ->where($where)
                ->setParameter($parameter)
                ->groupBy("u.id")
                ->order("acorn", "DESC")
                ->getArray();
        }
        $sort = 0;
        foreach ($lists as $key => $item) {
            if ($item['userId'] == $userId) {
                $sort = $key + 1;
            } else {
                continue;
            }
        }
        return $sort;
    }


}
