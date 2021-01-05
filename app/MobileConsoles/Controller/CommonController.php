<?php

namespace MobileConsoles\Controller;

use Admin\DModel\AcornAuditDModel;
use Admin\DModel\AnythingDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\ExternalRelationsDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\CompanyOpenapi;
use Jeechange\SDK\DingSDK;
use Jeechange\SDK\WxSDK;
use phpex\Access\AccessInterface;
use phpex\Library\Controller;

abstract class CommonController extends Controller implements AccessInterface {
    public $listsSize = 20;

    protected $sid = 0;

    protected $cdnBase = "https://cdn.itmakes.com/uploads";
    protected $cdnThumbBase = "https://cdn.itmakes.com/thumbs";

    /**
     *
     */
    protected function _initialize() {
        $session = Q()->getSession();
//        loginfo('/skdgjl','common',['common'=>$this->getUser('id')]);
        if (!$session || !$this->getUser()) {
            return;
        }
        $this->intoCompany();
        $this->redDot();
//        $this->sid = $session->get("sid");
        $this->flushUser();
        $this->sid = $this->getUser('sid');

        $this->assign("sid", $this->sid);

        $erDM = ExternalRelationsDModel::getInstance();
        $erDM->UserId($this->getUser('id'), $this->getUser('phone'));
        $lists = $erDM->findBy(array("userId" => $this->getUser('id')));//判断是否存在外部联系人
        if ($lists) {
            $this->assign("different", "yes");
        }
//        loginfo('/skdgjl','common',['common'=>'中部']);
        if ($this->sid == 0 && $_GET['different'] != 'yes') {
            $this->guide()->send();
            exit;
        }
//        loginfo('/skdgjl','common',['common'=>'底部']);
    }


    public function intoCompany() {

        if ($this->access->isIgnore()) return;

        $get = Q()->get->all();
        if (!$get["company"]) return;
        $userDM = UserDModel::getInstance();

        $user = $userDM->find($this->getUser("id") ?: 0);

        if (!$user) return;

        $companyMemberDM = CompanyMemberDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->name('c')->where("c.codeNo = '{$get['company']}' and c.status = 1")->setMax(1)->getOneArray();

        if (!$companyEN) return;

        $user->setSid($companyEN['id']);
        $userDM->save($user)->flush($user);

        if (!isset($get['recEN']) || !$get['recEN'] || $get['recEN'] == "root") {
            $recId = $companyEN["superid"];
        } else {
            $recEN = $userDM->name("u")->where("u.phone = {$get['recEN']} and u.status = 1")->getOneArray();
            $recId = $recEN['id'];
        }
        $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN['id']}' and cm.userId = '{$user->getId()}'")->count() ?: 0;
        if ($count <= 0) {
            $companyMemberEN = $companyMemberDM->newEntity();
            $companyMemberEN->setSid($companyEN['id']);
            $companyMemberEN->setUserId($user->getId());
            $companyMemberEN->setRecId($recId);
            $companyMemberEN->setStatus(1);
            $companyMemberEN->setAddTime(nowTime());
            $companyMemberEN->setTypes(1);
            $companyMemberEN->setAcorn(0);
            $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
        }
        RbacAccessDModel::accredit($this->access, "MobileConsoles", $user->getSid(), $user, $user->getRoleName());
    }

    /**
     *
     * 检查是否管理员
     * @param int $types 1主管理员，2子管理员，3主管理员或子管理员
     * @return bool
     */

    public function isSuper($types = 3) {
        $companyDM = CompanyDModel::getInstance();
        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $activeCompany = $companyDM->name("c")
            ->where("c.id=$sid")
            ->getOneArray(false, false);
        if (!$activeCompany) return false;


        $superids = array();
        if (in_array($types, array(1, 3))) {
            $superids[] = $activeCompany["superid"];
        }

        if (in_array($types, array(2, 3))) {
            $superids = array_merge($superids, explode(",", $activeCompany["subSuperid"]));
        }
        return in_array($userId, $superids);
    }

    /*
     * 用户引导页
     */
    private function guide() {
        $userDM = UserDModel::getInstance();
        $memberDM = CompanyMemberDModel::getInstance();
        if (Q()->isGet()) {
            $this->flushUser();
            $get = Q()->get->all();
            if ($get['company'] && $get['user']) {

                $recUser = $userDM->findOneBy(array("phone" => $get['user']));
                if (!$recUser) {
                    return $this->error("推荐人不存在");
                }
                $check = $memberDM->findOneBy(array("userId" => $this->getUser("id"), "sid" => $recUser->getSid()));

                if (!$check) {
                    $data = array();
                    $data['userId'] = $this->getUser('id');
                    $data['sid'] = $recUser->getSid();
                    $data['addTime'] = nowTime();
                    $data['intoTime'] = nowTime();
                    $data['types'] = 1;
                    $data['status'] = 1;
                    $memberDM->create($data, $memberEN = $memberDM->newEntity());
                    if (!$memberDM->check($data, $memberEN)) {
                        return $this->error($memberDM->getError());
                    }
                    $memberDM->add($memberEN)->flush();
                }
            }
            return $this->display("User:guide");
        } else {
            $this->flushUser();
            $comMebEN = $memberDM->name("cm")->where("cm.userId={$this->getUser('id')}")->setMax(1)->getOneArray();
            if (!$comMebEN) {
                $comMebEN = $memberDM->name("cm")->where("cm.phone={$this->getUser('phone')}")->setMax(1)->getOneArray();
            }
            if (!$comMebEN) {
                return $this->ajaxReturn(array("status" => "n", "info" => "尚未收到邀请"));
            }

            $staffDM = StaffDModel::getInstance();
            $staffEN = $staffDM->newEntity();
            $staffEN->setSid($comMebEN['sid']);
            $staffEN->setFullName($this->getUser('fullName'));
            $staffEN->setUserId($this->getUser('id'));
            $staffEN->setUserName($this->getUser('userName'));
            $staffEN->setRoleName("staff");
            $staffEN->setPhone($this->getUser('phone'));
            $staffEN->setAddTime(nowTime());
            $staffEN->setStatus(1);
            $staffEN->setEffect(0);
            $staffEN->setPoint(0);
            $staffEN->setStation(0);
            $staffEN->setQq($this->getUser('qq'));
            $staffEN->setWx($this->getUser('qq'));
            $staffEN->setEmail($this->getUser('email'));
            $staffDM->add($staffEN)->flush($staffEN);

            $memberDM->name("c")->where("c.id = {$comMebEN['id']}")->update(array('c.status' => 1, 'c.userId' => $this->getUser('id')));

            $userDM->name("u")->where("u.id = {$this->getUser('id')}")->update(array('u.sid' => $comMebEN['sid']));

            $session = Q()->getSession();
            $this->sid = $session->set("sid", $comMebEN['sid']);

            $this->flushUser();

            return $this->ajaxReturn(array("status" => "y", "info" => "成功加入！", "url" => url("~mobileConsoles_company_lists")));
        }
    }

    public function redDot() {
        $allotDM = TaskAllotDModel::getInstance();
        $acornAuditDM = AcornAuditDModel::getInstance();
        $taskDM = TaskDModel::getInstance();
        $anythingDM = AnythingDModel::getInstance();

        $userId = $this->getUser("id");
        $sid = $this->getUser('sid');

        //审核
        $where1 = "aa.auditor = '{$userId}' and aa.sid = '{$sid}' and aa.status = 0 and rd.status is null";
        $count1 = $acornAuditDM->name("aa")
            ->leftJoin("RedDot", "rd", "aa.id = rd.formId and rd.formName = 'AcornAudit' and rd.userId = '{$userId}' and rd.sid = '{$sid}'")
            ->where($where1)
            ->count() ?: 0;

        //任务
        $where2 = " a.userId = '{$userId}' and t.sid = '{$sid}' and rd.status is null";
        $count2 = $allotDM->name("a")
            ->leftJoin("Task", "t", "t.id=a.tid")
            ->leftJoin("RedDot", "rd", "t.id = rd.formId and rd.formName = 'Task' and rd.userId = '{$userId}' and rd.sid = '{$sid}'")
            ->where($where2)->count() ?: 0;

        $where3 = "t.acceptId= '{$userId}' and t.sid = '{$sid}' and rd.status is null";
        $count3 = $taskDM->name("t")->select("t,rd")
            ->leftJoin("RedDot", "rd", "t.id = rd.formId and rd.formName = 'TaskCBA' and rd.userId = '{$userId}' and rd.sid = '{$sid}'")
            ->where($where3)
            ->count() ?: 0;

        //备忘录
        $todayStart = date("Y-m-d 00:00:00");
        $todayEnd = date("Y-m-d 23:59:59");
        $todayCount = $anythingDM->name('a1')
            ->where("a1.certainTime>='{$todayStart}' and a1.certainTime<='{$todayEnd}' and a1.status<>1 and a1.userId={$userId}")
            ->count() ?: 0;

//        消除红点：用户ID，公司ID，来源ID，来源表名
//        $redDotDM = new \Admin\DModel\RedDotDModel();
//        $redDotDM->NewAdd($userId, $sid, 5, 'TaskCBA');

        $count1 = $count2 = $count3 = $todayCount = 0;

        $this->assign(
            "redDot", array(
                "home" => array(
                    "count" => $count1,
                    "acorn" => array(
                        "count" => $count1,
                        "audit" => $count1
                    ),
                ),
                "task" => array(
                    "count" => $count2 + $count3,
                    "todo" => $count2,
                    "accept" => $count3
                ),
                "me" => array(
                    "count" => $todayCount,
                    "anything" => $todayCount
                ),
            )
        );

        return true;
    }


    public function initSDK() {
        $get = Q()->get->all();
        $session = Q()->getSession();
        if ((!$session || !$session->get("sid")) && !$get["company"]) return;

        $userAgent = Q()->headers->get("user-agent");
        if (preg_match("#DingTalk#", $userAgent)) {
            $sdk_types = "dingtalk";
            $sdk = new DingSDK();
        } elseif (preg_match("#MicroMessenger#", $userAgent)) {
            $sdk_types = "wxwork";
            $sdk = new WxSDK();
        } else {
            return;
        }
        $apiDM = CompanyOpenapiDModel::getInstance();

        if ($get["company"]) {
            $companyEN = CompanyDModel::getInstance()->findOneBy(array("codeNo" => $get["company"]));
            if ($companyEN) {
                /** @var CompanyOpenapi $api */
                $api = $apiDM->findOneBy(array("sid" => $companyEN->getId(), "namesEn" => $sdk_types));
                if ($api) $sdk->initConfig($api);
            }
        } elseif ($session && $session->get("sid")) {
            /** @var CompanyOpenapi $api */
            $api = $apiDM->findOneBy(array("sid" => $session->get("sid"), "namesEn" => $sdk_types));
            if ($api) $sdk->initConfig($api);
        } else {
            return;
        }
        $this->assign("sdk_types", $sdk_types);
        $this->assign("corpId", $sdk->corpid);
        $this->assign("sdk_config", $sdk->getJsApiConfig());
    }

}