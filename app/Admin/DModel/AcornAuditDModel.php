<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class AcornAuditDModel extends DModel {
//字段解释
//tags   检索字段
//thumbs 附件
    public $statusMemo = array(
        0 => "审核中",
        1 => "已审核",
        2 => "不通过",
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
            $result["statusMemo"] = $this->statusMemo[$result["aa_status"]];
            $result["addTime"] = $result["aa_addTime"] instanceof \DateTime ? $result["aa_addTime"]->format("Y-m-d H:i:s") : "-";
        } else {
            $result["statusMemo"] = $this->statusMemo[$result['status']];
            $result["addTime"] = $result["addTime"] instanceof \DateTime ? $result["addTime"]->format("Y-m-d H:i:s") : "-";
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\AcornAudit();
    }

    //将历史数据更新到tags字段中
    public function updateData() {
        $userDM = new \Admin\DModel\UserDModel();
        $userarr = $userDM->name('u')->select('u.id,u.fullName')->getArray(false, false);
        $userarr = array_column($userarr, 'fullName', 'id');
        $acornAudit = $this->name('aa')
            ->select('aa.id,aa.userId,aa.toUser,aa.auditor')
            ->where('aa.tags is null')
            ->order('aa.id', 'desc')
            ->limit(0, 300)
            ->getArray(false, false);
        $arr = array();
        foreach ($acornAudit as $v) {
            $array = array();
            $array[] = $userarr[$v['userId']];
            $v['toUser'] = explode(',', $v['toUser']);
            foreach ($v['toUser'] as $vv) {
                $array[] = $userarr[$vv];
            }
            $array[] = $userarr[$v['auditor']];
            $array = array_unique($array);
            $arr[$v['id']] = implode(',', $array);
        }
        foreach ($arr as $ka => $va) {
            $acornAuditEN = $this->find($ka);
            if ($acornAuditEN) {
                $acornAuditEN->setTags($va);
                $this->save($acornAuditEN)->flush();
            }
        }
    }

}