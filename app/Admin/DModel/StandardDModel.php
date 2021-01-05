<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class StandardDModel extends DModel {
    private $methods = array(
        "0" => "系统发放",
        "1" => "审核发放",
        "2" => "手动发放",
        "3" => "其他",
    );

    private $cycle = array(
        "-1" => "每次",
        "0" => "每天",
        "1" => "每周",
        "2" => "每月",
        "3" => "每年",
    );

    //维度状态
    private $statusMemo = array(
        0 => "关闭",
        1 => "开启",
        2 => "申请",
    );
    //维度类型
    private $typeMemo = array(
        0 => "显示",
        1 => "隐藏",
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
        if (!$this->scalar) {
            $result["statusMemo"] = $this->getStatusMemo($result["status"]);
            $result["cycleMemo"] = $this->getCycle($result["cycle"]);
            $result["methodsMemo"] = $this->getMethods($result["methods"]);
        } else {
            $result["statusMemo"] = $this->getStatusMemo($result["s_status"]);
            $result["cycleMemo"] = $this->getCycle($result["s_cycle"]);
            $result["methodsMemo"] = $this->getMethods($result["s_methods"]);
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Standard();
    }

    /**发放类型
     * @param null $key
     * @return array|mixed
     */
    public function getMethods($key = null) {
        return null !== $key ? $this->methods[$key] : $this->methods;
    }

    /**发放周期
     * @param null $key
     * @return array|mixed
     */
    public function getCycle($key = null) {
        return null !== $key ? $this->cycle[$key] : $this->cycle;
    }

    public function getStatusMemo($status) {
        return $this->statusMemo[$status] ?: "--";
    }


    public function addHot($id, $sid) {
        if ($id) {
            $parameter = array("sid" => $sid, "id" => $id);
            $stat = $this->name("s")->where("s.id = :id AND s.sid = :sid")
                ->setParameter($parameter)
                ->setInc("s.hot", 1);
            if (!$stat) {
                $this->error = sprintf("标准项不存在：%d", $id);
                return false;
            }
        } else {
            $this->error = sprintf("标准项不存在：%d", $id);
            return false;
        }
        try {
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function getStandard($names, $sid, $classifyId) {
        $standard = $this->findOneBy(array("names" => $names, "sid" => $sid, "classify" => $classifyId));

        if ($standard) return $standard;

        $standard = $this->newEntity();

        $standard->setNames($names);

        $standard->setClassify($classifyId);
        $standard->setSubClassify(0);
        $standard->setAcorn(0);
        $standard->setMethods(2);
        $standard->setCycle(-1);
        $standard->setCycle(-1);

        $standard->setStatus(1);
        $standard->setMemo("");
        $standard->setAddTime(nowTime());
        $standard->setSid($sid);
        $standard->setSNo("");
        $standard->setHot(0);
        $standard->setWorkload("");

        $this->add($standard)->flush($standard);

        return $standard;
    }

    public function getAddedLists($sid, $types, $acorn = 0) {
        if (!$types) {
            return $this->name("s")->where("s.acorn>={$acorn} and s.sid={$sid}")->getArray();
        }

        $ids = array();
        $standardClassifyDM = StandardClassifyDModel::getInstance();
        foreach ($types as $type) {
            if (!$type) continue;
            $class = $standardClassifyDM->findOneBy(array("namesEn" => $type));

            if ($class) $ids[] = $class->getId();
        }
        if (!$ids) return array();

        return $this->name("s")->where("s.acorn>={$acorn} and s.sid={$sid} and s.classify in (:classify)")->setParameter(array("classify" => $ids))->getArray();
    }

    /**
     * 获取标准的名称
     * @param $id
     */
    public function getStandName($id, $sid) {
        $standard = $this->findOneBy(array("id" => $id, "sid" => $sid));
        if ($standard) {
            $name = $standard->getNames();
        } else {
            $name = "未知";
        }
        return $name;

    }

}