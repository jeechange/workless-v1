<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class DepartmentDModel extends DModel {
    public $getParentName = false;

    public $statusMemo = array(
        1 => "正常",
        0 => "停用",

    );

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
        if ($this->getParentName) {
            $result["d_parentName"] = $this->getParentNames($result["d_parentid"]);
        }

        $result["statusMemo"] = $this->statusMemo[$result["d_status"]];
    }

    protected function resolveObject($result = null) {

    }

    public function getParentNames($parentid) {
        if (!$parentid) return "--";

        $pEN = $this->find($parentid);

        return $pEN ? $pEN->getNames() : "--";

    }

    public function newEntity() {
        return new \Admin\Entity\Department();
    }

    public function getOptions($sid, $selectId, $pid = 0, $depth = 0) {
        $where = "d.parentid = $pid and d.sid=$sid";
        $section = $this->name('d')->where($where)->getArray(false, false);
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= "<option value=\"{$sec["id"]}\"{$selected}>{$depthstr}{$sec["names"]}</option>";
            $options .= $this->getOptions($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }


    private function depth($i, $count, $depth) {
        if ($depth == 0) {
            return "";
        }
        $nbsp = '&nbsp;';
        $return = "";
        for ($j = 0; $j < $depth; $j++) {
            $return .= $nbsp;
        }
        return ($i + 1) < $count ? $return . "├" : $return . "└";
    }


    /**部门递归，单选项（PC专用）
     * @param $sid
     * @param $pid
     * @param $layers
     * @return array
     */
    public function departmentPC($sid, $pid, $layers) {
        $departmentDM = DepartmentDModel::getInstance();
        $department = $departmentDM->name("d")->where("d.sid = {$sid} and d.parentid = {$pid} and d.status = 1")->getArray();

        $lists = array();
        foreach ($department as &$v) {
            $prefix = '';
            $Depart = $this->departmentPC($sid, $v['id'], $layers + 1);
            $count = count($Depart);

            foreach ($Depart as $kk => &$vv) {
                if ($kk + 1 < $count) {
                    $prefix = '├ ';
                } else {
                    $prefix = '└ ';
                }
                $vv['names'] = $prefix . $vv['names'];
            }

            $lists[] = $v;
            $lists = array_merge($lists, $Depart);
        }

        return $lists;
    }


    /**
     * 查询职位
     */
    public function position($sid = 0, $phone = 0, $upId = 0) {
        $StaffQuery = $this->StaffQuery($sid, $phone, $upId);

        $userDM = UserDModel::getInstance();
        $userEN = $userDM->name("u")->where("u.phone = '{$phone}'")->getOneArray();
        $userId = $userEN['id'];

        $sgDM = StaffGroupDModel::getInstance();
        $sgLists = $sgDM->name("sg")->where("(sg.leader = {$userId} or sg.helper = {$userId}) and sg.status = 1 and sg.sid = {$sid}")->getArray();
        $StaffGroup = '0';
        foreach ($sgLists as $v) {
            $members = $v['members'] ?: 0;
            $StaffGroup .= "," . $members;
        }
        $StaffGroup = explode(",", $StaffGroup);

        $personnel = array_unique(array_merge($StaffQuery, $StaffGroup));

        return $personnel;
    }


    public function StaffQuery($sid = 0, $phone = 0, $upId = 0) {
        $staffDM = StaffDModel::getInstance();
        if ($upId == 0) {
            $lists = $this->name('d')->select("d.id")->where("d.phone = '{$phone}' and d.sid = {$sid}")->getArray();
        } else {
            $lists = $this->name('d')->select("d.id")->where("d.parentid = '{$upId}'")->getArray();
            if (!$lists) {
                return null;
            }
        }
        $allS = array();
        foreach ($lists as $v) {
            $subordinate = $this->StaffQuery(0, 0, $v['id']);
            $slists = $staffDM->name("s")->select("s.userId")->where("s.department = {$v['id']}")->getArray();
            foreach ($slists as $vv) {
                $allS[] = $vv['userId'];
            }
            if ($subordinate) {
                $allS = array_merge($allS, $subordinate);
            }
        }

        return $allS;
    }


    /**
     * 部门上级查询
     */
    public function Superior($thisId, $postId) {
        if ($postId == 0) {
            return true;
        }

        $one = 0;
        $upId = $postId;
        do {
            $upEN = $this->name('d')->where("d.id = {$upId}")->getOneArray();
            if ($upEN['parentid'] != $thisId) {
                $upId = $upEN['parentid'] ?: 0;
            } else {
                $one = 1;
                $upId = 0;
            }
        } while ($upId != 0);

        if ($one != 0) {
            return false;
        }
        return true;
    }


}