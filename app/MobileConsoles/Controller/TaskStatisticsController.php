<?php
/**
 * 任务统计
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-30
 * Time: 16:39
 */

namespace MobileConsoles\Controller;


class TaskStatisticsController extends CommonController {

    private $menu = "task";

    public function _initialize() {
        parent::_initialize();
        $this->assign("menu", $this->menu);
    }

    /**
     * 任务统计
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $this->assign("tabs_sub", "lobby");
        $this->assign("curTabs", "statistics");
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
        $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
        $where = $selectCondition['where'];
        $params = $selectCondition['params'];

        if ($where) {
            $where = $where . " AND t.sid=" . $sid;
        } else {
            $where = "t.sid=" . $sid;
            $params = "";
        }

        $lists = $TaskStatisticsDM->name("t")
            ->leftJoin("User", "u", "u.id=t.userId")
            ->select("t.id as id,t.userId as userId,u.fullName as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("t.userId")
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
        $selectCondition = $TaskStatisticsDM->selectCondition($bTypes);
        $where = $selectCondition['where'];
        $params = $selectCondition['params'];

        if ($where) {
            $where = $where . " AND t.sid=" . $sid . " AND d.sid=" . $this->sid;
        } else {
            $where = "t.sid=" . $sid . " AND d.sid=" . $this->sid;
            $params = "";
        }

        $lists = $TaskStatisticsDM->name("t")
            ->leftJoin("Staff", "s", "s.userId=t.userId")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("t.id as id,d.names as userName,sum(t.issueCount) as issueCount,sum(t.allotCount) as allotCount,sum(t.acceptCount) as acceptCount,sum(t.execute) as execute,sum(t.realWl) as realWl,sum(t.totalWl) as totalWl,d.id as depid ,sum(t.acceptDay) as acceptDay,sum(t.coefficient) as coefficient,sum(t.quality) as quality")
            ->where($where)
            ->setParameter($params)
            ->groupBy("d.id")
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
     * 选中标题的显示
     * @param $bTypes
     * @return string
     */
    public function tsTitle($bTypes) {
        switch ($bTypes) {
            case 1:
                $title = "昨日";
                break;
            case 2:
                $title = "7天内";
                break;
            case 3:
                $title = "30天内";
                break;
            case 4:
                $title = "所有";
                break;
            default:
                $title = "所有";
        }
        return $title;
    }

    /**
     * 任务量统计
     * @return \phpex\Foundation\Response
     */
    public function taskList() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 4;

        $lists = 0;
        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        }
        foreach ($lists as $key => &$item) {
            $proportion[$key] = $item['proportion'] / 100;
        }

        array_multisort($proportion, SORT_STRING, SORT_DESC, $lists);//排序

        $this->assign("lists", $lists);
        $this->assign("types", $types);
        $this->assign("bTypes", $bTypes);
        $this->assign("title", $this->tsTitle($bTypes));
        return $this->display();
    }


    /**
     * 效率统计
     * @return \phpex\Foundation\Response
     */
    public function efficiency() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 4;

        $lists = 0;
        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        }

        foreach ($lists as $key => &$item) {
            $efficiency[$key] = round($item['realWl'] / $item['acceptDay'], 2);
        }
        array_multisort($efficiency, SORT_STRING, SORT_DESC, $lists);//排序

        if (!$lists) {
            $names[] = "暂无数据";
            $efficiencyVlues[] = 0;
        }

        $this->assign("lists", $lists);
        $this->assign("types", $types);
        $this->assign("bTypes", $bTypes);
        $this->assign("title", $this->tsTitle($bTypes));
        return $this->display();
    }

    /**
     * 质量统计
     * @return \phpex\Foundation\Response
     */
    public function quality() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 4;

        $lists = 0;

        if ($types == 1) {
            $lists = $this->personalT($this->sid, $bTypes);
        } elseif ($types == 2) {
            $lists = $this->departmentT($this->sid, $bTypes);
        }

        foreach ($lists as $key => &$item) {
            $qualityAverage[$key] = $item['qualityAverage']/100;
        }
        array_multisort($qualityAverage, SORT_STRING, SORT_DESC, $lists);//排序
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        $this->assign("bTypes", $bTypes);
        $this->assign("title", $this->tsTitle($bTypes));
        return $this->display();
    }

}