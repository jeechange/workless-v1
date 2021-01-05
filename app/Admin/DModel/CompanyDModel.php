<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class CompanyDModel extends DModel {
    //superid创始人/管理人的用户id

    public $apps = array(
        "target" => "绩效管理",
        "team" => "团队协作",
//        "client" => "客户关系",
    );

    public $memos = array(
        "scales" => array(
            0 => "10人以下",
            1 => "10-19人",
            2 => "20-49人",
            3 => "51-99人",
            4 => "100-149人",
            5 => "150-299人",
            6 => "300-499人",
            7 => "500人以上",
        ),
        "levels" => array(
            1 => "A",
            2 => "B",
            3 => "C",
            4 => "D",
            5 => "E",
            99 => "其他",
        ),
        "source" => array(
            1 => "搜索引擎",
            2 => "客户介绍",
            3 => "会议",
            4 => "广告",
            5 => "电话",
            6 => "自媒体",
            7 => "朋友圈",
            8 => "培训",
            9 => "机构",
            99 => "其他",
        ),
        "status" => array(
            1 => "启用",
            0 => "禁用",
        ),
    );


    /**
     * 自动填充规则
     */
    public function _fill() {
        //$this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);  //自动填充示例
        $this->addFill("platform", "Workless," . $this->get_device_type(), self::FILL_STRING, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {
        if ($this->scalar) {
            $result["levelsMemo"] = $this->memos["levels"][$result["c_levels"]];
            $result["statusMemo"] = $this->memos["status"][$result["c_status"]];
            $result["scalesMemo"] = $this->memos["scales"][$result["c_scales"]];
        } else {
            $result["levelsMemo"] = $this->memos["levels"][$result["levels"]];
            $result["statusMemo"] = $this->memos["status"][$result["status"]];
            $result["scalesMemo"] = $this->memos["scales"][$result["scales"]];
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Company();
    }


    public function getScales($key = 0) {
        $result = $this->memos["scales"][$key] ?: "未知";
        return $result;
    }


    public function addRoles($sid, $moduleName) {
        $lists = array(
            array("sid" => $sid, "names" => "系统管理员", "module" => $moduleName, "roleName" => "system", "status" => 1, "weight" => "1", "sort" => 0),
            array("sid" => $sid, "names" => "部门管理员", "module" => $moduleName, "roleName" => "branch", "status" => 1, "weight" => "2", "sort" => 0),
            array("sid" => $sid, "names" => "员工", "module" => $moduleName, "roleName" => "staff", "status" => 1, "weight" => "3", "sort" => 0),
        );
        $roleDM = RbacRoleDModel::getInstance();
        foreach ($lists as $item) {
            $where = array("module" => $moduleName, "roleName" => $item["roleName"], "sid" => $sid);
            $has = $roleDM->findOneBy($where);
            if ($has) continue;
            $role = $roleDM->newEntity();
            $roleDM->create($item, $role);
            $roleDM->add($role)->flush($role);
        }
    }

    public function getCompanyNames($sid) {
        $lists = $this->name("c")->select("c.names,c.id")->where("c.id=" . $sid)->getOneArray(false, false);

        return $lists['names'] ?: "";
    }


    /*
 获取设备型号
 */
    function get_device_type() {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type = 'other';
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 'ios';
        }
        if (strpos($agent, 'android')) {
            $type = 'android';
        }
        return $type;
    }


}