<?php

namespace Admin\DModel;

use Admin\Entity\Company;
use Admin\Entity\User;
use phpex\DModel\DModel;

class CompanyMemberDModel extends DModel {

    public $statusMemo = array(
        0 => "企业已邀请",
        1 => "已加入",
        2 => "已忽略",
    );

    public $statusStaffMemo = array(
        1 => "正式员工",
        2 => "试用期员工",
        3 => "离职员工",
    );

    //left_time  员工离司时间 ,rec_id推荐人的用户id
    //leader  上级id (leader>0：有上级，leader=-1：无上级，leader=null：未设置)
    //leader_name  上级名字
    //survey_acorn  调查积分
    //target_acorn  目标积分
    //status  0企业已邀请，等待员工确认，1已加入，2已忽略

    /**
     * 自动填充规则
     */
    public function _fill() {
        $this->addFill("surveyAcorn", "0", self::FILL_STRING, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例
    }

    protected function resolveArray(&$result) {
        if ($this->scalar) {
            $result["c_statusMemo"] = CompanyDModel::getInstance()->memos["status"][$result["c_status"]];
            $result["c_levelsMemo"] = CompanyDModel::getInstance()->memos["levels"][$result["c_levels"]];


            if ($result["c_status"]==0 || $result["c_status"]==2){
                $span_left_c = "<span style='color:red'>";
                $span_right_c = "</span>";
            }
            $result["c_statusMemoList"] =  $span_left_c.$this->statusMemo[$result["c_status"]].$span_right_c;

            if ($result["s_status"]==3){
                $span_left = "<span style='color:red'>";
                $span_right = "</span>";
            }
            $result["s_statusStaffMemo"] = $span_left.$this->statusStaffMemo[$result["s_status"]].$span_right;

        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\CompanyMember();
    }

    /**
     * 更新创建团队未存入companyMember
     */
    public function updateComperny($userId) {
        $companyDM = CompanyDModel::getInstance();
        $companyMemberDM = CompanyMemberDModel::getInstance();
        $lists = $companyDM->name("c")->select("c")
            ->where("c.superid=" . $userId)
            ->groupBy("c.id")
            ->getArray(true);
        foreach ($lists as $key => $item) {
            $count = $companyMemberDM->name('cm')->where("cm.sid = '{$item['c_id']}' and cm.userId = '{$userId}'")->count() ?: 0;
            if ($count <= 0) {//创建人添加自己进入该团队
                $companyMemberEN = $companyMemberDM->newEntity();
                $cMob['sid'] = $item['c_id'];
                $cMob['userId'] = $userId;
                $cMob['status'] = 1;
                $cMob['addTime'] = nowTime();
                $cMob['types'] = 1;
                $companyMemberDM->create($cMob, $companyMemberEN);
                $companyMemberDM->add($companyMemberEN)->flush();
            }
        }
    }


    public function getInviteInfo(User $user) {

        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */

        $company = $companyDM->find($user->getSid() ?: 0);


        $inviteInfo = sprintf('%s邀请您登录Workless加入"%s"，%s',
            $user->getFullName(),
            $company ? $company->getNames() : "",
            $companyUrl = url("~consoles_login_login", array("recEN" => $user->getPhone(), "company" => $company->getCodeNo()))
        );

        return $inviteInfo;

    }


}
