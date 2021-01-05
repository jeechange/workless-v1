<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Consoles\Service;

use Admin\DModel\DepartmentDModel;
use Admin\DModel\WelfareSettingsDModel;
use Node\Service\NodeMenuService;

/**
 * Description of MenuService
 *
 * @author river2liu <river2liu@jeechange.com>
 */
class MenuService extends NodeMenuService {

    public $debug = false;

    private $isLeader = null;

    public $isSuperTypes_1 = false;
    public $isSuperTypes_2 = false;
    public $isSuperTypes_3 = false;


    private function isDepartmentLeader() {
        if (null !== $this->isLeader) return (bool)$this->isLeader;

        $sid = $this->getUser("sid");
        $this->isLeader = false;
        if (!$sid) {
            return false;
        }

        $departmentDM = DepartmentDModel::getInstance();

        $userId = $this->getUser("id");

        $count = $departmentDM->name("d")->where("(d.directorId =$userId or REGEXP(d.directorsId,'(^|\,)$userId(\,|$)')=1) and d.sid = $sid")->count();

        if ($count) {
            $this->isLeader = true;
            return true;
        }
        return false;
    }

    /** 钉钉免登陆 */
    public function dingtalkEnable() {

        if ($this->debug) return true;
        return $this->isSuperTypes_3;
    }

    /** 钉钉机器人 */
    public function dingwebhookEnable() {
        if ($this->debug) return true;
        return $this->isSuperTypes_3;
    }

    /** 企业微信 */
    public function wxworkEnable() {
        if ($this->debug) return true;
        return $this->isSuperTypes_3;
    }

    /** 设置 - 管理员 */
    public function supersEnable() {
        if ($this->debug) return true;
        return $this->isSuperTypes_3;
    }

    /** 物资发放 */
    public function myGrantEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;

        $sid = $this->getUser("sid");
        if (!$sid) return false;

        $settingsDM = WelfareSettingsDModel::getInstance();
        $settings = $settingsDM->getSettings($sid);
        return $settings->getSnackUserId() == $this->getUser("id");
    }

    /** 福利管理-福利设置 */
    public function bonus_settingEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 任务大厅 */
    public function admin_task_listsEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /**  统计分析 */
    public function taskStatisticsEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 积分审核 */
    public function admin_acorn_auditEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }


    /** 统计分析 */
    public function admin_RankingEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 积分管理-积分明细 */

    public function admin_acorn_allListsEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 组织架构 */
    public function DepartmentEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 管理-价值维度 */
    public function AchievementsEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 任务管理 */
    public function taskSettingEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }


    /** 目标管理 */
    public function targetEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 考核管理 */
    public function kaoheguanliEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }

    /** 调查管理 */
    public function diaochaguanliEnable() {
        if ($this->debug) return true;
        if ($this->isSuperTypes_3) return true;
        return $this->isDepartmentLeader();
    }


}
