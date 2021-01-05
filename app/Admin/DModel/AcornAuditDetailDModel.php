<?php

namespace Admin\DModel;

use Admin\Entity\AcornAudit;
use Admin\Entity\AcornAuditDetail;
use phpex\DModel\DModel;

class AcornAuditDetailDModel extends DModel {
//sid:公司Id
//auditId:审核id
//userId:执行人id
//fromUser:申请人名称
//executor:执行人名字
//sNames:标准维度事项（如：发布一篇文章）
//scNames:标准维度（如：行为）
//acorn:积分
//addTime:申请时间
//auditTime:执行时间
//status:状态
//types:类型

    protected $statusMemo = array(
        0 => "审核中/未阅读",
        1 => "已审核/已阅读",
        2 => "不通过",
    );

    protected $typesMemo = array(
        "0" => "审核",
        "1" => "抄送",

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
            $result["statusMemo"] = $this->statusMemo[$result["ad_status"]];
            $result["typeMemo"] = $this->typesMemo[$result["ad_types"]];

        } else {
            $result["statusMemo"] = $this->statusMemo[$result['status']];
            $result["typeMemo"] = $this->typesMemo[$result['types']];
        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\AcornAuditDetail();
    }

    public function adds(AcornAudit $acornAudit, $userId) {
        $userDM = new \Admin\DModel\UserDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $standardClassifyDM = new \Admin\DModel\StandardClassifyDModel();

        //审核人
        $ids = $acornAudit->getAuditor();
        //抄送人
        if ($acornAudit->getCPerson() > 0) {
            if ($ids) {
                $ids .= "," . $acornAudit->getCPerson();
            } else {
                $ids = $acornAudit->getCPerson();
            }
        }
        //提交上级
        if ($acornAudit->getSuperior() > 0) {
            $ids .= "," . $acornAudit->getSuperior();

        }
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $entity = $this->name("aa")->where("aa.userId=" . $id . " AND aa.auditId=" . $acornAudit->getId())->getOneArray();
            if ($entity) {
                continue;
            }
            $data['auditId'] = $acornAudit->getId();
            $data['userId'] = $id;
            $data['fromUser'] = $userDM->getUserName($acornAudit->getFromUser());
            $data['executor'] = $userDM->getUserName($id);
            $data['sNames'] = $standardDM->getStandName($acornAudit->getNames(), $acornAudit->getSid());
            $lists = $standardClassifyDM->getlists($acornAudit->getScId());
            $data['scNames'] = $lists['names'];
            $data['acorn'] = $acornAudit->getAcorn();
            $data['sid'] = $acornAudit->getSid();
            $data['addTime'] = new \DateTime(date("Y-m-d H:i:s", strtotime(totime($acornAudit->getAddTime()))));
            $data['status'] = 0;

            if (in_array($id, explode(',', $acornAudit->getCPerson()))) {
                $types = 1;
                $content = "{$userDM->getUserName($acornAudit->getFromUser())}申请积分【{$data['sNames']}】，已抄送给您";
            } else {
                $types = 0;
                $content = "{$userDM->getUserName($acornAudit->getFromUser())}申请积分【{$data['sNames']}】，待您审批";
            }
            $data['types'] = $types;
            $this->create($data, $entity = $this->newEntity());
            $this->add($entity)->flush();

            //在all_todo表中添加审批人验收记录
            $AllTodoDM = AllTodoDModel::getInstance();
            $AllTodoDM->addAllTodo($acornAudit->getSid(), $id, 3, $entity->getId(), $content);

            //添加到todo
//            $todoDM = new \Admin\DModel\TodoDModel();
//            $todoDM->createTodoAcorn($entity);
        }

    }


}
