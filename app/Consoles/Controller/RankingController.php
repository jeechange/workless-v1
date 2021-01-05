<?php
/**
 * 排行榜
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-09-19
 * Time: 16:37
 */

namespace Consoles\Controller;


class RankingController extends CommonController {

    /**个人排行
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $get = Q()->get->all();
        $types = $get['types'] ? $get['types'] : 1;
        $bTypes = $get['bTypes'] ? $get['bTypes'] : 5;
        $RankingDM = new \Admin\DModel\RankingDModel();
        $departmentDM = new \Admin\DModel\DepartmentDModel();
        if ($types == 1) {
            $department = $departmentDM->name("d")->select("d")->where("d.sid=" . $this->sid)->getArray();
            $departmentSelect = array("" => "全部");
            foreach ($department as $key => $item) {
                $departmentSelect[$item['id']] = $item['names'];

            }
            $search = $this->search();
            $search->labelType("placeholder");
            $search->addSelect("d.id", "部门", $departmentSelect);
            $search->addDateRange("rk.addTime");
            $search->addExport("excel", "导出到excel");
            $search->bindData(Q()->get->all());
            if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
                $where = "rk.sid=:sid";
                $parameter['sid'] = $this->sid;
            } else {
                $condition = $RankingDM->selectRanking($this->sid, $bTypes);
                $where = $condition['where'];
                $parameter = $condition['parameter'];
            }
            $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm


            if (Q()->get->has("__export__excel")) {
                return $this->personalRexprotLists($where, $parameter, $this->sid, $bTypes);
            }
            $RankingDM->name("rk")->leftJoin("User", "u", "u.id=rk.userId");
            if (Q()->get->get("did")) {//有部门选择的时候
                $where .= " AND d.sid=" . $this->sid;
                $RankingDM->leftJoin("Staff", "s", "s.userId=rk.userId")
                    ->leftJoin("Department", "d", "d.id=s.department");
            }
            $lists = $RankingDM->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
                ->where($where)
                ->setParameter($parameter)
                ->groupBy("u.id")
                ->setPage()
                ->data_sort()
                ->order("acorn", "DESC")
                ->getArray();

            $ringRatio = $RankingDM->ringRatio($this->sid, $bTypes);

            $where1 = $ringRatio['where'];
            $parameter1 = $ringRatio['parameter'];
            foreach ($lists as &$item) {
                $department = $departmentDM->name("d")
                    ->leftJoin("Staff", "s", "s.department=d.id")
                    ->select("d.names as department")
                    ->where("s.userId=" . $item['userId'])
                    ->setMax(1)
                    ->getOneArray();
                if ($bTypes < 10) {
                    $old = $RankingDM->name("rk")
                        ->leftJoin("User", "u", "u.id=rk.userId")
                        ->select("sum(rk.acorn) as acorn")
                        ->where($where1 . " AND rk.userId=" . $item['userId'])
                        ->setParameter($parameter1)
                        ->groupBy("u.id")
                        ->getOneArray();
                    if ($old['acorn'] < 0) {
                        $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                    } elseif ($old['acorn'] > 0) {
                        $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                    } elseif ($old['acorn'] == 0) {
                        $item['ringRatio'] = 0;
                    }
                    if ($item['ringRatio'] != 0) {
                        $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                    }

                }
                $item['department'] = $department['department'];

            }
        } elseif ($types == 2) {
            $search = $this->search();
            $search->addDateRange("rk.addTime", "日期");
            $search->addExport("excel", "导出到excel");
            $search->bindData(Q()->get->all());

            $condition = $RankingDM->selectRanking($this->sid, $bTypes);
            if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
                $where = "rk.sid=:sid";
                $parameter['sid'] = $this->sid;
            } else {
                $where = $condition['where'];
                $parameter = $condition['parameter'];
            }
            $where .= " AND d.sid=" . $this->sid;
            $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm

            if (Q()->get->has("__export__excel")) {
                return $this->exprotLists($where, $parameter, $bTypes);
            }

            $lists = $RankingDM->name("rk")
                ->leftJoin("User", "u", "u.id=rk.userId")
                ->leftJoin("Staff", "s", "s.userId=u.id")
                ->leftJoin("Department", "d", "d.id=s.department")
                ->select("sum(rk.acorn) as acorn,d.names as userName,d.id as depid")
                ->where($where)
                ->setParameter($parameter)
                ->groupBy("d.id")
                ->setPage()
                ->data_sort()
                ->order("acorn", "DESC")
                ->getArray();
            $ringRatio = $RankingDM->ringRatio($this->sid, $bTypes);
            $where = $ringRatio['where'];
            $parameter = $ringRatio['parameter'];

            foreach ($lists as &$item) {
                if ($bTypes < 10) {
                    $old = $RankingDM->name("rk")
                        ->leftJoin("User", "u", "u.id=rk.userId")
                        ->leftJoin("Staff", "s", "s.userId=u.id")
                        ->leftJoin("Department", "d", "d.id=s.department")
                        ->select("sum(rk.acorn) as acorn")
                        ->where($where . " AND d.id=" . $item['depid'] . " AND d.sid=" . $this->sid)
                        ->setParameter($parameter)
                        ->groupBy("d.id")
                        ->getOneArray();
                    if ($old['acorn'] < 0) {
                        $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                    } elseif ($old['acorn'] > 0) {
                        $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                    } elseif ($old['acorn'] == 0) {
                        $item['ringRatio'] = 0;
                    }
                    if ($item['ringRatio'] != 0) {
                        $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                    }
                }
            }
        } elseif ($types == 3) {
            $search = $this->search();
            $search->addKeyword("sg.names", "小组");
            $search->addDateRange("rk.addTime", "日期");
            $search->addExport("excel", "导出到excel");
            $search->bindData(Q()->get->all());
            if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
                $where = "rk.sid=:sid";
                $parameter['sid'] = $this->sid;
            } else {
                $condition = $RankingDM->selectRanking($this->sid, $bTypes);
                $where = $condition['where'];
                $parameter = $condition['parameter'];
            }

            $where = $where . " AND rk.groupId>0";
            $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm

            if (Q()->get->has("__export__excel")) {
                return $this->gExprotLists($where, $parameter, $bTypes);
            }
            $lists = $RankingDM->name("rk")
                ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
                ->select("sum(rk.acorn) as acorn,sg.names as names,sg.id as sgId")
                ->where($where)
                ->setParameter($parameter)
                ->groupBy("sg.id")
                ->setPage()
                ->data_sort()
                ->order("acorn", "DESC")
                ->getArray();
            $ringRatio = $RankingDM->ringRatio($this->sid, $bTypes);

            $where = $ringRatio['where'];
            $parameter = $ringRatio['parameter'];
            foreach ($lists as &$item) {
                if ($bTypes < 10) {
                    $old = $RankingDM->name("rk")
                        ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
                        ->select("sum(rk.acorn) as acorn")
                        ->where($where . " AND rk.groupId=" . $item['sgId'])
                        ->setParameter($parameter)
                        ->groupBy("sg.id")
                        ->getOneArray();
                    if ($old['acorn'] < 0) {
                        $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                    } elseif ($old['acorn'] > 0) {
                        $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                    } elseif ($old['acorn'] == 0) {
                        $item['ringRatio'] = 0;
                    }
                    if ($item['ringRatio'] != 0) {
                        $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                    }

                }
            }

        }

        $this->assign("lists", $lists);
        $this->assign("active", $types);
        $this->assign("bTypes", $bTypes);

        if ($types == 1) {
            return $this->display();
        } elseif ($types == 2) {
            return $this->display("departmentR.latte");
        } elseif ($types == 3) {
            return $this->display("groupR.latte");
        }
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
        $departmentDM = new \Admin\DModel\DepartmentDModel();
        $department = $departmentDM->name("d")->select("d")->where("d.sid=" . $this->sid)->getArray();
        $departmentSelect = array("" => "全部");
        foreach ($department as $key => $item) {
            $departmentSelect[$item['id']] = $item['names'];

        }
        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("d.id", "部门", $departmentSelect);
        $search->addDateRange("rk.addTime");
        $search->addExport("excel", "导出到excel");
        $search->bindData(Q()->get->all());
        if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
            $where = "rk.sid=:sid";
            $parameter['sid'] = $this->sid;
        } else {
            $condition = $RankingDM->selectRanking($sid, $bTypes);
            $where = $condition['where'];
            $parameter = $condition['parameter'];
        }
        $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm


        if (Q()->get->has("__export__excel")) {
            return $this->personalRexprotLists($where, $parameter, $sid, $bTypes);
        }
        $RankingDM->name("rk")->leftJoin("User", "u", "u.id=rk.userId");
        if (Q()->get->get("did")) {//有部门选择的时候
            $where .= " AND d.sid=" . $this->sid;
            $RankingDM->leftJoin("Staff", "s", "s.userId=rk.userId")
                ->leftJoin("Department", "d", "d.id=s.department");
        }
        $lists = $RankingDM->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("u.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
            ->getArray();

        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);

        $where1 = $ringRatio['where'];
        $parameter1 = $ringRatio['parameter'];
        foreach ($lists as &$item) {
            $department = $departmentDM->name("d")
                ->leftJoin("Staff", "s", "s.department=d.id")
                ->select("d.names as department")
                ->where("s.userId=" . $item['userId'])
                ->setMax(1)
                ->getOneArray();
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where1 . " AND rk.userId=" . $item['userId'])
                    ->setParameter($parameter1)
                    ->groupBy("u.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }

            }
            $item['department'] = $department['department'];

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

        $search = $this->search();
        $search->addDateRange("rk.addTime", "日期");
        $search->addExport("excel", "导出到excel");
        $search->bindData(Q()->get->all());

        $condition = $RankingDM->selectRanking($sid, $bTypes);
        if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
            $where = "rk.sid=:sid";
            $parameter['sid'] = $this->sid;
        } else {
            $where = $condition['where'];
            $parameter = $condition['parameter'];
        }
        $where .= " AND d.sid=" . $sid;

        $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("sum(rk.acorn) as acorn,d.names as userName,d.id as depid")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("d.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
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
                    ->where($where . " AND d.id=" . $item['depid'] . " AND d.sid=" . $sid)
                    ->setParameter($parameter)
                    ->groupBy("d.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
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
        $search = $this->search();
        $search->addKeyword("sg.names", "小组");
        $search->addDateRange("rk.addTime", "日期");
        $search->addExport("excel", "导出到excel");
        $search->bindData(Q()->get->all());
        if (Q()->get->get("rkaddTime_start") || Q()->get->get("rkaddTime_end")) {
            $where = "rk.sid=:sid";
            $parameter['sid'] = $this->sid;
        } else {
            $condition = $RankingDM->selectRanking($sid, $bTypes);
            $where = $condition['where'];
            $parameter = $condition['parameter'];
        }

        $where = $where . " AND rk.groupId>0";
        $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm

//        if (Q()->get->has("__export__excel")) {
//            return $this->exprotLists($where, $parameter);
//        }


        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names,sg.id as sgId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("sg.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);

        $where = $ringRatio['where'];
        $parameter = $ringRatio['parameter'];
        foreach ($lists as &$item) {
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where . " AND rk.groupId=" . $item['sgId'])
                    ->setParameter($parameter)
                    ->groupBy("sg.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
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

    public function personalRexprotLists($where, $params, $sid, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $departmentDM = new \Admin\DModel\DepartmentDModel();
        $RankingDM->name("rk")->leftJoin("User", "u", "u.id=rk.userId");
        if (Q()->get->get("did")) {//有部门选择的时候
            $where .= " AND d.sid=" . $this->sid;
            $RankingDM->leftJoin("Staff", "s", "s.userId=rk.userId")
                ->leftJoin("Department", "d", "d.id=s.department");
        }
        $lists = $RankingDM->select("sum(rk.acorn) as acorn,u.fullName as userName,u.id as userId")
            ->where($where)
            ->setParameter($params)
            ->groupBy("u.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
            ->getArray(true);
        if (!$lists) {
            return $this->error("导出数据不存在");
        }

        $ringRatio = $RankingDM->ringRatio($sid, $bTypes);
        $where1 = $ringRatio['where'];
        $parameter1 = $ringRatio['parameter'];

        foreach ($lists as $key => &$item) {
            $department = $departmentDM->name("d")
                ->leftJoin("Staff", "s", "s.department=d.id")
                ->select("d.names as department")
                ->where("s.userId=" . $item['userId'])
                ->setMax(1)
                ->getOneArray();
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("User", "u", "u.id=rk.userId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where1 . " AND rk.userId=" . $item['userId'])
                    ->setParameter($parameter1)
                    ->groupBy("u.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }

            }
            $item['department'] = $department['department'];
            $item['ranking'] = $key + 1;
        }
        $headder = array(
            'A' => 'userName:姓名%s',
            'B' => 'acorn:积分',
            'C' => 'ranking:排名',
            'D' => 'ringRatio:积分环比',
            'E' => 'department:部门%s',
        );
        $title = "个人" . $RankingDM->getBTypesMemo($bTypes) . "排名";
        return excelExprot(sprintf($title . "%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }

    public function exprotLists($where, $parameter, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("sum(rk.acorn) as acorn,d.names as userName,d.id as depid")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("d.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($this->sid, $bTypes);
        $where1 = $ringRatio['where'];
        $parameter1 = $ringRatio['parameter'];

        foreach ($lists as $key => &$item) {
            if ($bTypes < 10) {
                if ($where1 != "") {
                    $old = $RankingDM->name("rk")
                        ->leftJoin("User", "u", "u.id=rk.userId")
                        ->leftJoin("Staff", "s", "s.userId=u.id")
                        ->leftJoin("Department", "d", "d.id=s.department")
                        ->select("sum(rk.acorn) as acorn")
                        ->where($where1 . " AND d.id=" . $item['depid'] . " AND d.sid=" . $this->sid)
                        ->setParameter($parameter1)
                        ->groupBy("d.id")
                        ->getOneArray();
                    if ($old['acorn'] < 0) {
                        $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                    } elseif ($old['acorn'] > 0) {
                        $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                    } elseif ($old['acorn'] == 0) {
                        $item['ringRatio'] = 0;
                    }
                }

                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }
                $item["ranking"] = $key + 1;

            }
        }
        $headder = array(
            'A' => 'userName:部门%s',
            'B' => 'acorn:积分',
            'C' => 'ranking:排名',
            'D' => 'ringRatio:积分环比',
        );
        $title = "部门" . $RankingDM->getBTypesMemo($bTypes) . "排名";
        return excelExprot(sprintf($title . "%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }

    public function gExprotLists($where, $parameter, $bTypes) {
        $RankingDM = new \Admin\DModel\RankingDModel();

        $lists = $RankingDM->name("rk")
            ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
            ->select("sum(rk.acorn) as acorn,sg.names as names,sg.id as sgId")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("sg.id")
            ->setPage()
            ->data_sort()
            ->order("acorn", "DESC")
            ->getArray();
        $ringRatio = $RankingDM->ringRatio($this->sid, $bTypes);

        $where = $ringRatio['where'];
        $parameter = $ringRatio['parameter'];
        foreach ($lists as $key => &$item) {
            if ($bTypes < 10) {
                $old = $RankingDM->name("rk")
                    ->leftJoin("StaffGroup", "sg", "sg.id=rk.groupId")
                    ->select("sum(rk.acorn) as acorn")
                    ->where($where . " AND rk.groupId=" . $item['sgId'])
                    ->setParameter($parameter)
                    ->groupBy("sg.id")
                    ->getOneArray();
                if ($old['acorn'] < 0) {
                    $item['ringRatio'] = round((1 - $item['acorn'] / $old['acorn']), 2);
                } elseif ($old['acorn'] > 0) {
                    $item['ringRatio'] = round(($item['acorn'] / $old['acorn'] - 1), 2);
                } elseif ($old['acorn'] == 0) {
                    $item['ringRatio'] = 0;
                }
                if ($item['ringRatio'] != 0) {
                    $item['ratioTypes'] = $item['acorn'] > $old['acorn'] ? 1 : 2;
                }
                $item['ranking'] = $key + 1;
            }
        }

        $headder = array(
            'A' => 'names:组名%s',
            'B' => 'acorn:积分',
            'C' => 'ranking:排名',
            'D' => 'ringRatio:积分环比',
        );
        $title = "小组" . $RankingDM->getBTypesMemo($bTypes) . "排名";
        return excelExprot(sprintf($title . "%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }


    /**个人加分、扣分、得分统计,types为2则得到加分，1得到扣分，3得到得分
     * @return \phpex\Foundation\Response
     */
    public function personalPoints() {
        $types = Q()->get->get("types") ?: 2;
        $userId = Q()->get->get("userId") ?: $this->getUser('id');

        $departmentDM = new \Admin\DModel\DepartmentDModel();
        $staffDM = new \Admin\DModel\StaffDModel();


        $userDM = new \Admin\DModel\UserDModel();
        $user = $userDM->name("u")->select("u")->where("u.id=" . $userId)->getOneArray();
        $lists = $this->personal($userId, $types);
        //部门
        $department = $departmentDM->departmentPC($this->sid, 0, 0);
        foreach ($department as $k => $v) {
            //加入到任何部门的此公司的同事
            $department[$k]['staff'] = $staffDM->name('s')
                ->where("s.department={$v['id']} and s.sid={$this->sid} and s.status=1")
                ->getArray();
            $department[$k]['count'] = count($department[$k]['staff']);
        }
        //未加入到任何部门的此公司的同事
        $staff = $staffDM->name("s")->where("s.sid={$this->sid} and (s.department=0 or s.department is null)")->getArray();
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("user", $user);
            $this->assign("department", $department);
            $this->assign("staff", $staff);
            $this->assign("active", 1);
            $this->assign("types", $types);//个人分析的类型2则得到加分，1得到扣分，3得到得分
            $this->assign("formUrl", url('consoles_ranking_personalPoints'));
            return $this->display();
        }
    }

    //个人加分、扣分、得分统计
    public function personal($userId, $types) {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid AND rk.types != :types AND rk.userId=:userId";
        $parameter = array(
            "sid" => $this->sid,
            "types" => $types,
            "userId" => $userId,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->select("sum(rk.acorn) as acorn,u.fullName as userName,rk.addTime as addTime,rk.memo as memo")
            ->where($where)
            ->setParameter($parameter)
            ->groupBy("rk.id")
            ->setPage()
            ->data_sort()
            ->order("rk.id", "DESC")
            ->getArray();
        return $lists;
    }

    /**
     * 部门加分、扣分、得分统计,types为2则得到加分，1得到扣分，3得到得分
     * @return \phpex\Foundation\Response
     */
    public function departmentPoints() {
        $types = Q()->get->get("types") ?: 2;
        //用户自己所属部门
        $staffDM = new \Admin\DModel\StaffDModel();
        $department = $staffDM->findOneBy(array('userId' => $this->getUser('id')));
        if (!$department) {
            $yourdepartmentId = 0;
        } else {
            $yourdepartmentId = $department->getDepartment();
        }
        //所查询的部门
        $departmentId = Q()->get->get("departmentId") ?: $yourdepartmentId;

        $departmentDM = new \Admin\DModel\DepartmentDModel();
        //部门分类
        $department = $departmentDM->departmentPC($this->sid, 0, 0);

        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid AND rk.types != :types AND d.id=:departmentId";
        $parameter = array(
            "sid" => $this->sid,
            "types" => $types,
            "departmentId" => $departmentId,
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("sum(rk.acorn) as acorn,d.names as userName")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("d.id")
            ->getArray();
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("departmentId", $departmentId);
            $this->assign("department", $department);
            $this->assign("active", 2);
            $this->assign("types", $types);
            return $this->display();
        }
    }

    /**部门人均得分统计
     * @return \phpex\Foundation\Response
     */
    public function deAveragePoints() {
        //月，年
        if (Q()->get->get('time')) {
            $year = substr(Q()->get->get('time'), 0, 4);
            $month = substr(Q()->get->get('time'), 4, 2) ?: '01';
        } else {
            $month = Q()->get->get('month') ?: date('m');
            $year = Q()->get->get('year') ?: date('Y');
        }
        $staffDM = new \Admin\DModel\StaffDModel();
        $RankingDM = new \Admin\DModel\RankingDModel();
        //未加入到任何部门的同事
        $staff = $staffDM->name("s")->select("s.userId,s.fullName as userName")->where("s.sid={$this->sid} and (s.department=0 or s.department is null)")->getArray();
        foreach ($staff as $k => &$v) {
            $v['acorn'] = $RankingDM->name("rk")->leftJoin("Staff", "s", "rk.userId=s.userId")->where("s.userId=" . $v['userId'] . " AND rk.month=" . $month . " AND rk.year=" . $year)->sum('rk.acorn') ?: 0;
            $v['num'] = 1;
        }
        //部门
        $where = "rk.sid=:sid AND rk.types != :types AND rk.month=" . $month . " AND rk.year=" . $year . " AND d.sid=:dSid";
        $parameter = array(
            "sid" => $this->sid,
            "types" => 0,
            "dSid" => $this->sid
        );
        $lists = $RankingDM->name("rk")
            ->leftJoin("User", "u", "u.id=rk.userId")
            ->leftJoin("Staff", "s", "s.userId=u.id")
            ->leftJoin("Department", "d", "d.id=s.department")
            ->select("sum(rk.acorn) as acorn,count(distinct s.userId) as num,d.names as userName")
            ->where($where)
            ->setParameter($parameter)
            ->order("acorn", "DESC")
            ->groupBy("d.id")
            ->getArray();
        //年月数组,显示12个
        $timeArr = array();
        for ($i = 0; $i < 12; $i++) {
            if (date("m") - $i >= 1) {
                $mon = date("m") - $i;
                if (date("m") - $i < 10) $mon = '0' . $mon;
                $timeArr[$i]['str'] = date('Y') . $mon;
                $timeArr[$i]['year'] = date('Y');
                $timeArr[$i]['month'] = $mon;
            } else {
                $timeArr[$i]['str'] = (date('Y') - 1) . (12 + date("m") - $i);
                $timeArr[$i]['year'] = date('Y') - 1;
                $timeArr[$i]['month'] = 12 + date("m") - $i;
            }
        }
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("staff", $staff);
            $this->assign("timeArr", $timeArr);
            $this->assign("month", $month);
            $this->assign("year", $year);
            $this->assign("formUrl", url('consoles_ranking_deAveragePoints'));
            return $this->display();
        }
    }

    /**个人加分、扣分、得分分析
     * @return \phpex\Foundation\Response
     */
    public function analysis() {
        $rankingDM = new \Admin\DModel\RankingDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $id = $this->getUser("id");
        $types = Q()->get->get("types") ?: 0;//默认得分分析

        $lists = $rankingDM->name('rk')
            ->select("rk")
            ->groupBy("rk.standId")
            ->where("rk.sid =" . $this->sid . "AND rk.userId=" . $id . "AND rk.types!=" . $types)
            ->getArray();
        $vss = array();
        $ass = array();

        foreach ($lists as $k => $v) {
            $standardEN = $standardDM->name('s')->where("s.sid=" . $this->sid . "AND s.id=" . $v['standId'])->getOneArray();
            if (!$standardEN) {
                continue;
            }
            $lists[$k]['total'] = array(
                'names' => $standardEN['names'] ?: "其他",
                'ratio' => $rankingDM->name('rk')
                    ->select("rk")
                    ->where("rk.sid =" . $this->sid . "AND rk.standId=" . $standardEN['id'] . "AND rk.userId=" . $id)
                    ->sum("rk.acorn")
            );

            $ass[] = array("value" => $lists[$k]['total']['ratio'], "name" => $lists[$k]['total']['names']);
            $vss[] = $lists[$k]['total']['names'];

        }

        $this->assign("lists", $lists);
        $this->assign("vss", $vss);
        $this->assign("ass", $ass);
        $this->assign("active", $types = 3);
        $this->assign("types", $types);//个人分析的类型1则为加分，2为扣分，0为得分
        return $this->display();
    }


}
