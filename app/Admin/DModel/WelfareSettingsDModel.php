<?php

namespace Admin\DModel;

use Admin\Entity\WelfareSettings;
use phpex\DModel\DModel;

class WelfareSettingsDModel extends DModel {

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
        return new \Admin\Entity\WelfareSettings();
    }

    /**
     * @param $sid
     * @return WelfareSettings
     */


    public function getSettings($sid) {

        /** @var WelfareSettings $settings */

        $settings = $this->findOneBy(array("sid" => $sid));

        if ($settings) return $settings;

        $settings = $this->newEntity();

        $settings->setSid($sid);
        $settings->setMaterials(1);
        $settings->setLucky(1);
        $settings->setLucky(1);
        $settings->setLuckyPrize("");
        $settings->setBonus(1);
        $settings->setBonusPool(0);
        $settings->setSnack(1);
        $settings->setSnackUserId(0);

        $this->add($settings)->flush($settings);

        return $settings;

    }


}