<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-20
 * Time: 18:01
 */

namespace Consoles\Controller;


class TaskStatisticsController extends CommonController {


    /**
     * 任务量统计
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 1;

        $lists = 0;
        $names = array();
        $issueVlues = array();
        $allotVlues = array();
        $acceptVlues = array();
        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        } elseif ($types == 3) {
            $lists = $this->pAnalysis($this->sid, $bTypes);
        } elseif ($types == 4) {
            $lists = $this->dAnalysis($this->sid, $bTypes);
        }

        foreach ($lists as $key => $item) {
            if ($item['issueCount'] || $item['allotCount'] || $item['acceptCount']) {
                $names[] = $item['userName'] ? $item['userName'] : "暂无数据";
                $issueVlues[] = $item['issueCount'] ? $item['issueCount'] : 0;
                $allotVlues[] = $item['allotCount'] ? $item['allotCount'] : 0;
                $acceptVlues[] = $item['acceptCount'] ? $item['acceptCount'] : 0;
            }
        }
        if (!$lists) {
            $names[] = "暂无数据";
            $issueVlues[] = 0;
            $allotVlues[] = 0;
            $acceptVlues[] = 0;

        }

        $this->assign("names", $names);
        $this->assign("issueVlues", $issueVlues);
        $this->assign("allotVlues", $allotVlues);
        $this->assign("acceptVlues", $acceptVlues);
        $this->assign("lists", $lists);
        $this->assign("active", $types);
        $this->assign("bTypes", $bTypes);
        if ($types == 2) {
            return $this->display("departmentT.latte");
        } elseif ($types == 3) {
            return $this->display("pAnalysis.latte");
        } elseif ($types == 4) {
            return $this->display("dAnalysis.latte");
        }
        return $this->display();
    }

    /**
     * 个人
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function personalT($sid, $bTypes) {
        $TaskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $departmentDM = new \Admin\DModel\DepartmentDModel();
        $department = $departmentDM->name("d")->select("d")->where("d.sid=" . $this->sid)->getArray();
        $departmentSelect = array("" => "全部");
        foreach ($department as $key => $item) {
            $departmentSelect[$item['id']] = $item['names'];

        }
        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("d.id", "部门", $departmentSelect);
        $search->addDateRange("t.addTime", "时间");
        $search->bindData(Q()->get->all());

        if (!Q()->get->get("taddTime_start")) {
            $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
            $where = $selectCondition['where'];
            $params = $selectCondition['params'];
        }
        if ($where) {
            $where = $where . " AND t.sid=" . $sid;
        } else {
            $where = "t.sid=" . $sid;
        }
        $search->build($where, $searchForm, $params); //构建$where和$searchForm
        $TaskStatisticsDM->name("t")->leftJoin("User", "u", "u.id=t.userId");

        if (Q()->get->get("did")) {//有部门选择的时候
            $where .= " AND d.sid=" . $this->sid;
            $TaskStatisticsDM->leftJoin("Staff", "s", "s.userId=t.userId")
                ->leftJoin("Department", "d", "d.id=s.department");
        }
        $lists = $TaskStatisticsDM->select("t.id as id,t.userId as userId,u.fullName as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("t.userId")
            ->setPage()
            ->data_sort()
            ->order("t.id")
            ->getArray();

        foreach ($lists as $key => &$item) {
            $item['proportion'] = round($item['realWl'] / $item['totalWl'], 2) * 100;
            $item['efficiency'] = round($item['realWl'] / $item['acceptDay'], 2) * 100;
            $count = $TaskStatisticsDM->name("t")->select("t.coefficient")->where("t.userId=" . $item['userId'] . "AND t.coefficient>0")->count();
            $item['coefficientAverage'] = round($item['coefficient'] / $count, 2) ?: 0;

            $count1 = $TaskStatisticsDM->name("t")->select("t")->where("t.userId=" . $item['userId'] . "AND t.quality>0")->count();

            $item['qualityAverage'] = round($item['quality'] / $count1, 2) ?: 0;
        }
        return $lists;
    }

    /**
     * 部门
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function departmentT($sid, $bTypes) {
        $TaskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $search = $this->search();
        $search->addDateRange("t.addTime", "时间");
        $search->bindData(Q()->get->all());
        if (!Q()->get->get("taddTime_start")) {
            $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
            $where = $selectCondition['where'];
            $params = $selectCondition['params'];
        }
        if ($where) {
            $where = $where . " AND t.sid=" . $sid . " AND d.sid=" . $this->sid;
        } else {
            $where = "t.sid=" . $sid . " AND d.sid=" . $this->sid;
        }

        $search->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $TaskStatisticsDM->name("t")
            ->leftJoin("Staff", "s", "s.userId=t.userId")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("t.id as id,d.names as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,d.id as depid ,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("d.id")
            ->setPage()
            ->data_sort()
            ->order("t.id")
            ->getArray();

        foreach ($lists as $key => &$item) {
            $item['proportion'] = round($item['realWl'] / $item['totalWl'], 2) * 100;
            $item['efficiency'] = round($item['realWl'] / $item['acceptDay'], 2) * 100;
            $count = $TaskStatisticsDM->name("t")
                ->leftJoin("Staff", "s", "s.userId=t.userId")
                ->leftJoin("Department", "d", "d.id=s.department")
                ->select("t.coefficient")->where("d.id=" . $item['depid'] . "AND t.coefficient>0")->count();
            $item['coefficientAverage'] = round($item['coefficient'] / $count, 2) ?: 0;

            $count1 = $TaskStatisticsDM->name("t")
                ->leftJoin("Staff", "s", "s.userId=t.userId")
                ->leftJoin("Department", "d", "d.id=s.department")->select("t")->where("d.id=" . $item['depid'] . "AND t.quality>0")->count();
            $item['qualityAverage'] = round($item['quality'] / $count1, 2) ?: 0;
        }
        return $lists;
    }

    /**
     *个人分析
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function pAnalysis($sid, $bTypes) {
        $TaskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $search = $this->search();
        $search->addTimeRange("t.addTime");
        $search->bindData(Q()->get->all());
        if (!Q()->get->get("taddTime_start")) {
            $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
            $where = $selectCondition['where'];
            $params = $selectCondition['params'];
        }
        if ($where) {
            $where = $where . " AND t.sid=" . $sid;
        } else {
            $where = "t.sid=" . $sid;
        }
        $search->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $TaskStatisticsDM->name("t")
            ->leftJoin("User", "u", "u.id=t.userId")
            ->select("t.id as id,t.userId as userId,u.fullName as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("t.userId")
            ->data_sort()
            ->order("t.id")
            ->setMax(13)
            ->getArray();

        foreach ($lists as $key => &$item) {
            $item['proportion'] = round($item['realWl'] / $item['totalWl'], 2) * 100;
            $item['efficiency'] = round($item['realWl'] / $item['acceptDay'], 2) * 100;
            $count = $TaskStatisticsDM->name("t")->select("t.coefficient")->where("t.userId=" . $item['userId'] . "AND t.coefficient>0")->count();
            $item['coefficientAverage'] = round($item['coefficient'] / $count, 2) ?: 0;

            $count1 = $TaskStatisticsDM->name("t")->select("t")->where("t.userId=" . $item['userId'] . "AND t.quality>0")->count();

            $item['qualityAverage'] = round($item['quality'] / $count1, 2) ?: 0;
        }
        return $lists;
    }

    /**
     *部门分析
     * @param $sid
     * @param $bTypes
     * @return \array[]
     */
    public function dAnalysis($sid, $bTypes) {
        $TaskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();

        $search = $this->search();
        $search->addTimeRange("t.addTime");
        $search->bindData(Q()->get->all());
        if (!Q()->get->get("taddTime_start")) {
            $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
            $where = $selectCondition['where'];
            $params = $selectCondition['params'];
        }
        if ($where) {
            $where = $where . " AND t.sid=" . $sid . " AND d.sid=" . $this->sid;
        } else {
            $where = "t.sid=" . $sid . " AND d.sid=" . $this->sid;
        }
        $search->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $TaskStatisticsDM->name("t")
            ->leftJoin("Staff", "s", "s.userId=t.userId")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("t.id as id,d.names as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,d.id as depid ,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("d.id")
            ->order("t.id")
            ->setMax(13)
            ->getArray();

        foreach ($lists as $key => &$item) {
            $item['proportion'] = round($item['realWl'] / $item['totalWl'], 2) * 100;
            $item['efficiency'] = round($item['realWl'] / $item['acceptDay'], 2) * 100;
            $count = $TaskStatisticsDM->name("t")
                ->leftJoin("Staff", "s", "s.userId=t.userId")
                ->leftJoin("Department", "d", "d.id=s.department")
                ->select("t.coefficient")->where("d.id=" . $item['depid'] . "AND t.coefficient>0")->count();
            $item['coefficientAverage'] = round($item['coefficient'] / $count, 2) ?: 0;

            $count1 = $TaskStatisticsDM->name("t")
                ->leftJoin("Staff", "s", "s.userId=t.userId")
                ->leftJoin("Department", "d", "d.id=s.department")->select("t")->where("d.id=" . $item['depid'] . "AND t.quality>0")->count();
            $item['qualityAverage'] = round($item['quality'] / $count1, 2) ?: 0;
        }
        return $lists;
    }

    /**
     * 效率统计
     * @return \phpex\Foundation\Response
     */
    public function efficiency() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 1;

        $lists = 0;
        $names = array();
        $efficiencyVlues = array();

        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        } elseif ($types == 3) {
            $lists = $this->pAnalysis($this->sid, $bTypes);
        } elseif ($types == 4) {
            $lists = $this->dAnalysis($this->sid, $bTypes);
        }

        foreach ($lists as $key => &$item) {
            $efficiency[$key] = round($item['realWl'] / $item['acceptDay'], 2);
            $names[] = $item['userName'] ? $item['userName'] : "暂无数据";
            $efficiencyVlues[] = round($item['realWl'] / $item['acceptDay'], 2) ? round($item['realWl'] / $item['acceptDay'], 2) : 0;
        }
        array_multisort($efficiency, SORT_STRING, SORT_DESC, $lists);//排序

        if (!$lists) {
            $names[] = "暂无数据";
            $efficiencyVlues[] = 0;
        }
        $this->assign("names", $names);
        $this->assign("efficiencyVlues", $efficiencyVlues);
        $this->assign("lists", $lists);
        $this->assign("active", $types);
        $this->assign("bTypes", $bTypes);

        if ($types == 2) {
            return $this->display("departmentE.latte");
        } elseif ($types == 3) {
            return $this->display("pAnalysisE.latte");
        } elseif ($types == 4) {
            return $this->display("dAnalysisE.latte");
        }
        return $this->display();
    }

    /**
     * 质量统计
     * @return \phpex\Foundation\Response
     */
    public function quality() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 1;

        $lists = 0;
        $names = array();
        $qualityVlues = array();
        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        } elseif ($types == 3) {
            $lists = $this->pAnalysis($this->sid, $bTypes);
        } elseif ($types == 4) {
            $lists = $this->dAnalysis($this->sid, $bTypes);
        }

        foreach ($lists as $key => &$item) {
            $qualityAverage[$key] = $item['qualityAverage'] / 100;
            $names[] = $item['userName'] ? $item['userName'] : "暂无数据";
            $qualityVlues[] = $item['qualityAverage'];

        }
        array_multisort($qualityAverage, SORT_STRING, SORT_DESC, $lists);//排序
        if (!$lists) {
            $names[] = "暂无数据";
            $qualityVlues[] = 0;
        }
        $this->assign("names", $names);
        $this->assign("qualityVlues", $qualityVlues);
        $this->assign("lists", $lists);
        $this->assign("active", $types);
        $this->assign("bTypes", $bTypes);

        if ($types == 2) {
            return $this->display("departmentQ.latte");
        } elseif ($types == 3) {
            return $this->display("pAnalysisQ.latte");
        } elseif ($types == 4) {
            return $this->display("dAnalysisQ.latte");
        }
        return $this->display();
    }

}