<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Service;

/**
 * Description of MenuService
 *
 * @author river2liu <river2liu@jeechange.com>
 */
class MenuService {

    private static $self;
    private $access;

    public function __construct($access) {
        $this->access = $access;
    }

    /**
     * @param type $key
     */
    protected function getUser($key = null) {
        return $this->access->getUser($key);
    }

    /**
     * 转换启用/禁用
     * @param type $status
     * @return integer
     */
    public function convertEnable($status) {
        if (in_array($status, array(0, 1), true)) {
            return $status;
        }
        if (method_exists($this, $status . "Enable")) {
            return $this->{$status . "Enable"}() ? 1 : 0;
        }
        return 0;
    }

    /**
     * 转换标题
     * @param type $names
     */
    public function convertNames($names) {
        return preg_replace_callback("/%([a-zA-Z]+)%/", array(&$this, 'callConvertNames'), $names);
    }

    public function callConvertNames($names) {
        if ($names[1] == "callConvert") {
            return "*callConvert*";
        }
        if (method_exists($this, $names[1] . "Names")) {
            return $this->{$names[1] . "Names"}();
        }
        return "&{$names[1]}&";
    }

    /**
     * 是否显示我的报单中心
     * @return boolean
     */
    public function agentEnable() {
        return $this->getUser("bStatus") == 2;
    }

    /**
     * 是否显示申请报单中心
     * @return type
     */
    public function applyagentEnable() {
        return $this->getUser("bStatus") != 2;
    }

    /**
     * 是否显示双轨菜单
     * @return type
     */
    public function pyramidEnable() {
        return \Admin\DModel\UserDModel::USER_SCHEMA == 2;
    }
    /**
     * 是否显示三轨菜单
     * @return type
     */
    public function pyramid3Enable() {
        return \Admin\DModel\UserDModel::USER_SCHEMA == 3;
    }

}
