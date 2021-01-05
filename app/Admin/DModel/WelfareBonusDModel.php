<?php

namespace Admin\DModel;

use Admin\Entity\Company;
use Admin\Entity\Staff;
use phpex\DModel\DModel;

class WelfareBonusDModel extends DModel {

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
        return new \Admin\Entity\WelfareBonus();
    }


    public function added($userId, $sid, $bonus, $memo) {
        if (!$userId || !$sid || !$bonus) {
            $this->error = "无效参数";
            return false;
        }

        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */
        $company = $companyDM->find($sid);

        if (!$company || $company->getBonus() <= 0) {
            $this->error = "请先对企业总股数进行设置";
            return false;
        }

        $staffDM = StaffDModel::getInstance();


        /** @var Staff $staff */

        $staff = $staffDM->findOneBy(array("userId" => $userId, "sid" => $sid));

        if (!$staff) {
            $this->error = "员工信息获取失败";
            return false;
        }


        $total = $staffDM->name("s")->where("s.sid=$sid and s.status=1")->sum("s.bonus");

        if (($total + $bonus) > $company->getBonus()) {
            $this->error = "发放的总股数不能超过企业的总股数";
            return false;
        }


        $bonusEN = $this->newEntity();


        $bonusEN->setUserId($userId);
        $bonusEN->setSid($sid);
        $bonusEN->setAddTime(nowTime());
        $bonusEN->setBonus($bonus);
        $bonusEN->setMemo($memo);
        $bonusEN->setStatus(1);

        $this->add($bonusEN)->flush($bonusEN);


        $staff->setBonus($bonus + $staff->getBonus());

        $staffDM->save($staff)->flush($staff);
        return true;

    }

}