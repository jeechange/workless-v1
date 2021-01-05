<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class StaffStationDModel extends DModel {


    public $statusMemo = array(
        0 => "停用",
        1 => "启用",
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

        if ($this->scalar) {
            $result["statusMemo"] = $this->statusMemo[$result["s_status"]];
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\StaffStation();
    }


    public function riseAcorn($sid, $userId) {
        $staffDM = StaffDModel::getInstance();
        $staff = $staffDM->name("s")->where("s.sid =" . $sid . " and s.userId = " . $userId)->order("s.id", "DESC")->setMax(1)->getArray();
        if (!$staff) {
            $this->error = sprintf("该员工不存在");
            return false;
        }
        if (!$staff[0]['station']) {
            $this->error = sprintf("该员工没有设置职位");
            return false;
        }
        $staffStationDM = StaffStationDModel::getInstance();

//        $station = $staffStationDM->name("ss")->where("ss.sid = " . $sid)->getArray();
        $stationSingle = $staffStationDM->name("ss")->where("ss.id =" . $staff[0]['station'])->getOneArray();
        if ($stationSingle['limitAcorn'] == 1) {
            $riseAcorn = $stationSingle['riseAcorn'];
        } else {
            $riseAcorn = '';
        }
        try {
            return $riseAcorn;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }
}