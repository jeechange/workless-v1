<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/8/21
 * Time: 16:45
 */

namespace MobileConsoles\Controller;

use Admin\DModel\AcornDModel;
use Admin\DModel\RankingDModel;

class SortController extends CommonController {
    public function lists() {
        if (Q()->isGet()) {
            $get = Q()->get->get("types") ?: "0";
            $this->assign("active", $get);
            return $this->display();
        }
    }

    public function add_effect($types) {
        if (Q()->isGet()) {
            return $this->display();
        }
    }

    /**查询积分
     * @return \phpex\Foundation\Response
     */
    public function findAcorn() {
        $get = Q()->get->get("types") ?: "0";
        $acornDM = AcornDModel::getInstance();
        $lists = $acornDM->name("a")
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("Standard", "s", "s.id = a.names")
            ->select("a,u.fullName,s.names as sNames")
            ->where("a.status = 1 and a.sid = " . $this->sid)
            ->order("a.id", "DESC")
            ->getArray(true);
        foreach ($lists as $k => $v) {
            $lists[$k]['addTimes'] = $acornDM->time_tran(totime($v['a_addTime']));
        }
        $this->assign("lists", $lists);
        $this->assign("active", $get);
        return $this->display();
    }

    /**
     * 排行榜
     * @return \phpex\Foundation\Response
     */
    public function rankings() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 1;
        if ($types == 1) {
            $lists = $this->personalR($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentR($this->sid, $bTypes);
        } elseif ($types == 3) {
            $lists = $this->groupR($this->sid, $bTypes);
        }
        $this->assign("lists", $lists);
        $this->assign("title", $this->title($bTypes));
        $this->assign("types", $types);
        $this->assign("bTypes", $bTypes);
        return $this->display();

    }

    /**
     * @name 个人排行
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function personalR($sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("u.id")
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);

        $where = $ringRatio['where'];
        $parameter = $ringRatio['parameter'];
        foreach ($lists as &$item) {
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where . " AND rk.userId=" . $item['userId'])
                    ->setParameter($parameter)
                    ->groupBy("u.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']) * 100, 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1) * 100, 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }

            }
        }
        return $lists;
    }

    /**
     * @name 部门排行
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function departmentR($sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];
        $where .= " AND d.sid=" . $sid;

        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("sum(rk.acorn) as acorn,d.names as userName,d.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("d.id")
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);

        $where = $ringRatio['where'];
        $parameter = $ringRatio['parameter'];
        foreach ($lists as &$item) {
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->leftJoin("Staff", "s", "s.userId=u.id")
                    ->leftJoin("Department", "d", "d.id=s.department")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where . " AND d.id=" . $item['userId'] . " AND d.sid=" . $sid)
                    ->setParameter($parameter)
                    ->groupBy("d.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']) * 100, 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1) * 100, 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }

            }
        }
        return $lists;
    }

    /**
     * @name 小组排行
     * @param $where
     * @param $parameter
     * @param $bTypes
     * @return \array[]
     */
    public function groupR($sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $condition = $RankingDM->selectRanking($sid, $bTypes);
        $where = $condition['where'];
        $parameter = $condition['parameter'];
        $where = $where . " AND rk.groupId>0";

        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names,sg.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("sg.id")
            ->order("acorn", "DESC")
            ->limit(0, 15)
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);

        $where = $ringRatio['where'];
        $parameter = $ringRatio['parameter'];
        foreach ($lists as &$item) {
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where . " AND rk.groupId=" . $item['userId'])
                    ->setParameter($parameter)
                    ->groupBy("sg.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']) * 100, 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1) * 100, 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }

            }
        }

        return $lists;
    }

    /**
     * 选中标题的显示
     * @param $bTypes
     * @return string
     */
    public function title($bTypes) {
        switch ($bTypes) {
            case 1:
                $title = "今日排名";
                break;
            case 2:
                $title = "昨日排名";
                break;
            case 3:
                $title = "本周排名";
                break;
            case 4:
                $title = "上周排名";
                break;
            case 5:
                $title = "本月排名";
                break;
            case 6:
                $title = "上月排名";
                break;
            case 7:
                $title = "本季排名";
                break;
            case 8:
                $title = "本年排名";
                break;
            case 9:
                $title = "去年排名";
                break;
            case 10:
                $title = "总分榜";
                break;
            default:
                $title = "今日排名";
        }
        return $title;
    }


    /**
     * 排行详情
     * @param $id
     * @param $types
     * @return \phpex\Foundation\Response
     */
    public function detail($id, $types) {
        $acornDM = AcornDModel::getInstance();
        $rankingDM = new \Admin\DModel\RankingDModel();
        $get = Q()->get->all();
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 1;

        if ($types == 1) {
            $lists = $rankingDM->personalRdetail($id, $this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $rankingDM->departmentRDetail($id, $this->sid, $bTypes);
        } elseif ($types == 3) {
            $lists = $rankingDM->groupRDetail($id, $this->sid, $bTypes);
        }
        $curDetail = array();
        foreach ($lists as $k => $v) {
            $v['addTime'] = $acornDM->time_tran(totime($v['addTime']));
            $curDetail[] = $v;
        }

        $this->assign("lists", $curDetail);
        $this->assign("title", $this->title($bTypes));
        return $this->display();
    }


    /**
     * 加分统计
     * @return \phpex\Foundation\Response
     */
    public function effectAdd() {
        if (Q()->isGet()) {
            return $this->display();
        }
    }

    /**
     * 加、减、扣分统计
     * @param int $sTypes积分类型 ，如：加分、扣分、得分
     * @return \phpex\Foundation\Response
     */
    public function effectDeduction($sTypes) {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;//统计类型，如：个人，部门
        $bTypes = $get['bTypes'] ? $get['bTypes'] : intval(date("m"));//时间

        if (Q()->isGet()) {
            $RankingDM = new \Admin\DModel\RankingDModel();
            $where = "rk.sid=:sid AND rk.month=:month AND rk.year=:year";
            $parameter = array(
                "sid" => $this->sid,
                "year" => date("Y"),
                "month" => $bTypes,
            );
            if ($sTypes != 0) {
                $where .= " AND rk.types =" . $sTypes;
            }
            if ($types == 1) {
                $lists = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->select("sum(rk.acorn) as acorn,u.fullName as userName,rk.userId as userId")
                    ->where($where)
                    ->setParameter($parameter)
                    ->groupBy("rk.userId")
                    ->order("rk.id", "DESC")
                    ->getArray();
            } elseif ($types == 2) {
                if ($where) {
                    $where .= " AND d.sid=" . $this->sid;
                }
                $lists = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->leftJoin("Staff", "s", "s.userId=u.id")
                    ->leftJoin("Department", "d", "d.id=s.department")
                    ->select("sum(rk.acorn) as acorn,d.names as userName,d.id as userId")
                    ->where($where)
                    ->setParameter($parameter)
                    ->order("acorn", "DESC")
                    ->groupBy("d.id")
                    ->getArray();
            }
            $this->assign("lists", $lists);
            $this->assign("types", $types);
            $this->assign("bTypes", $bTypes);
            $this->assign("sTypes", $sTypes);
            $this->assign("tabs", 'effectDeduction');
            return $this->display();
        }
    }

    /**
     * 統計详情
     * @param $id
     * @param $sTypes积分类型 ，如：加分、扣分、得分
     * @return \phpex\Foundation\Response
     */
    public function detailStatistics($id, $sTypes) {
        $acornDM = AcornDModel::getInstance();
        $rankingDM = new \Admin\DModel\RankingDModel();
        $get = Q()->get->all();
        $bTypes = $get['bTypes'] ? $get['bTypes'] : intval(date("m"));
        $types = $get['t'] ? $get['t'] : 1;
        if ($types == 1) {
            $lists = $rankingDM->personalSdetail($id, $this->sid, $bTypes, $sTypes);
        } elseif ($types == 2) {
            $lists = $rankingDM->departmentSdetail($id, $this->sid, $bTypes, $sTypes);
        }
        $curDetail = array();
        foreach ($lists as $k => $v) {
            $v['addTime'] = $acornDM->time_tran(totime($v['addTime']));
            $curDetail[] = $v;
        }

        $this->assign("lists", $curDetail);
        $this->assign("title", $this->title($bTypes));
        return $this->display();
    }

    /**部门人均得分统计
     * @return \phpex\Foundation\Response
     */
    public function deAveragePoints() {
        $get = Q()->get->all();
        $bTypes = $get['bTypes'] ? $get['bTypes'] : intval(date("m"));//时间
        $year = Q()->get->get('year') ?: date('Y');
        $RankingDM = new \Admin\DModel\RankingDModel();
        $UserDM = new \Admin\DModel\UserDModel();

        //部门
        $where = "rk.sid=:sid  AND rk.month=" . $bTypes . " AND rk.year=" . $year . " AND d.sid=:dSid";
        $parameter = array(
            "sid" => $this->sid,
            "dSid" => $this->sid
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("d.id as dId,sum(rk.acorn) as acorn,count(distinct s.userId) as num,d.names as userName")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("d.id")
            ->getArray();

        foreach ($lists as $key => $item) {
            $lists[$key]['num'] = $UserDM->name("u")
                ->leftJoin("Staff", "s", "s.userId=u.id")
                ->leftJoin("Department", "d", "d.id=s.department")
                ->select("u.id as userId,u.fullName")
                ->where("d.id=" . $item['dId'] . " AND u.sid=" . $this->sid . " AND d.sid=" . $this->sid)
                ->count();
            $lists[$key]['deAveragePoints'] = round($item['acorn'] / $lists[$key]['num'], 2);
        }
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("tabs", 'deAveragePoints');
            $this->assign("bTypes", $bTypes);

            return $this->display();
        }
    }


}
