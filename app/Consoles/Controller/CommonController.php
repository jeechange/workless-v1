<?php

namespace Consoles\Controller;

use Admin\DModel\AcornAuditDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\ExternalRelationsDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\AnythingDModel;
use Admin\DModel\RedDotDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Jeechange\SDK\DingSDK;
use phpex\Access\AccessInterface;
use phpex\Library\Controller;
use Admin\DModel\CompanyDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\StaffDModel;

abstract class CommonController extends Controller implements AccessInterface {
    protected $sid = 0;

    protected $cdnBase = "https://cdn.itmakes.com/uploads";
    protected $cdnThumbBase = "https://cdn.itmakes.com/thumbs";

    protected function _initialize() {
        //红点
        $redDot = $this->redDot();
        $this->assign("redDot", $redDot);

        C("view.error_tpl", "Public:error");
        $session = Q()->getSession();
        if (!$session || !$this->getUser()) {
            return;
        }
        $this->sid = $session->get("sid");

        $this->assign("sid", $this->sid);

        if (in_array(R()->getAction(), array("addTeam", "index"))) return;
        if (in_array(R()->getController(), array("Login"))) {
            if (in_array(R()->getAction(), array("logout", "verify"))) {
                return;
            }
        }

        $this->intoCompany();

        $erDM = ExternalRelationsDModel::getInstance();
        $erDM->UserId($this->getUser('id') ?: 0, $this->getUser('phone'));
        $lists = $erDM->findBy(array("userId" => $this->getUser('id')));//判断是否存在外部联系人
        if ($lists) {
            $this->assign("different", "yes");
        }

        if ($this->sid == 0 && $_GET['different'] != 'yes') {
            $this->guide()->send();
            exit;
        }
    }

    protected function isSuper() {
        return true;
//        $companyDM = CompanyDModel::getInstance();
//        $userId = $this->getUser("id") ?: 0;
//        $sid = $this->getUser("sid") ?: 0;
//        $activeCompany = $companyDM->name("c")
//            ->where("c.id=$sid")
//            ->getOneArray(false, false);
//        $isSuper = $activeCompany && $activeCompany["superid"] == $userId;
//        return $isSuper;
    }

    /**
     *
     * 检查是否管理员
     * @param int $types 1主管理员，2子管理员，3主管理员或子管理员
     * @return bool
     */

    public function isSuperTypes($types = 3) {
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

    /**
     * @param string $var
     * @param null $currentPage
     * @param null $pSize
     * @return \Consoles\Service\PageService
     */
    public function page($var = "p", $currentPage = null, $pSize = null) {
        static $pages = array();
        if (!isset($pages[$var]))
            $pages[$var] = new \Consoles\Service\PageService($this, $var, $currentPage, $pSize);
        return $pages[$var];
    }

    public function success($message = '', $jumpUrl = '', $time = 3, $tpl = "Public:success") {
        $this->assign(array("msg" => $message, 'jumpUrl' => $jumpUrl, 'time' => $time));
        $this->assign("ajaxextramethod", "SideForm");
        return $this->display($tpl);
    }

    public function handleReturn($handle, $parameter = array(), $tpl = 'Public:handleReturn') {
        $assign = array("handle" => $handle, "parameter" => $parameter);
        return $this->display($tpl, $assign, false);
    }

    public function error($message = '', $jumpUrl = '', $time = 5, $tpl = "Public:error") {
        $this->assign(array("msg" => $message, 'jumpUrl' => $jumpUrl, 'time' => $time));
        $this->assign("ajaxextramethod", "SideForm");
        return $this->display($tpl);
    }

    /*
     * 用户引导页
     */
    private function guide() {
        $CTOF = 1;
        $userDM = UserDModel::getInstance();
        $memberDM = CompanyMemberDModel::getInstance();
        if (Q()->isGet()) {
            $this->flushUser();
            $get = Q()->get->all();

            if ($get['company']) {
                $companyDM = CompanyDModel::getInstance();
                $companyEN = $companyDM->name("c")->select("c,cm")
                    ->leftJoin("CompanyMember", "cm", "cm.sid = c.id and cm.userId = {$this->getUser('id')}")
                    ->where("c.codeNo = '{$get['company']}'")
                    ->getOneArray(true, false);
                if ($companyEN) {
                    $this->assign("companyNames", $companyEN['c_names']);
                    $this->assign("cCodeNo", $companyEN['cm_id']);
                    $CTOF = 2;
                } else {
                    $CTOF = 0;
                }
            } else {
                $userId = $this->getUser('id') ?: 0;
                $mlists = $memberDM->name("m")->select("m,c.names as c_names")
                    ->leftJoin("Company", "c", "m.sid = c.id")
                    ->where("m.userId = {$userId}")
                    ->getArray(true, false);
                $this->assign("mlists", $mlists);
                if (!$mlists) {
                    $CTOF = 0;
                }
            }

            if ($get['company'] && $get['user']) {
                $recUser = $userDM->findOneBy(array("phone" => $get['user']));
                if (!$recUser) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "推荐人不存在"));
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
                    $data['leader'] = 0;
                    $memberDM->create($data, $memberEN = $memberDM->newEntity());
                    if (!$memberDM->check($data, $memberEN)) {
                        return $this->ajaxReturn(array("status" => "n", "info" => $memberDM->getError()));
                    }
                    $memberDM->add($memberEN)->flush();
                }
            }

            $this->assign("CTOF", $CTOF); // company , true or false
            return $this->display("Login:guide");
        } else {
            $this->flushUser();
            $get = Q()->get->all();

            $splicing = '';
            if ($get['types'] == 1) {
                $splicing = " and cm.id = {$get['cc']}";
            } elseif ($get['types'] == 2) {
                $splicing = " and cm.id = {$get['cc']}";
            }

            $where1 = "cm.userId={$this->getUser('id')}" . $splicing;
            $comMebEN = $memberDM->name("cm")->where($where1)->setMax(1)->getOneArray();
            if (!$comMebEN) {
                $where2 = "cm.phone={$this->getUser('phone')}" . $splicing;
                $comMebEN = $memberDM->name("cm")->where($where2)->setMax(1)->getOneArray();
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
            $staffEN->setDepartment(0);
            $staffEN->setBonus(0);
            $staffEN->setSnackNum(0);
            $staffEN->setQq($this->getUser('qq'));
            $staffEN->setWx($this->getUser('qq'));
            $staffEN->setEmail($this->getUser('email'));
            $staffDM->add($staffEN)->flush($staffEN);

            $memberDM->name("c")->where("c.id = {$comMebEN['id']}")->update(array('c.status' => 1, 'c.userId' => $this->getUser('id')));

            $userDM->name("u")->where("u.id = {$this->getUser('id')}")->update(array('u.sid' => $comMebEN['sid']));

            $session = Q()->getSession();
            $this->sid = $session->set("sid", $comMebEN['sid']);

            $this->flushUser();

            $url = sprintf("%s#%s", url("~consoles_index_index"), url("consoles_lists", array('con' => "company")));

            return $this->ajaxReturn(array("status" => "y", "info" => "成功加入！", "url" => $url));
        }
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
            $companyMemberEN->setLeader(0);
            $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
        }
        RbacAccessDModel::accredit($this->access, "Consoles", $user->getSid(), $user, $user->getRoleName());
    }


    public function redDot() {
        $this->flushUser();

//        if (R()->getController() !== "Index") {

//            if (R()->getController() === "Task" and R()->getAction() === "details") {
//                $get = R()->getParameter();
//                RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $get['id'], 'Todo');
//            }

        $allotDM = TaskAllotDModel::getInstance();
        $acornAuditDM = AcornAuditDModel::getInstance();
        $taskDM = TaskDModel::getInstance();
        $anythingDM = AnythingDModel::getInstance();

        $userId = $this->getUser("id");
        if (!$userId) {
            return array();
        }
        $sid = $this->getUser('sid');
        if (!$sid) {
            return array();
        }

        //任务
        $todoDM = TodoDModel::getInstance();
        $where4 = "t.status = 0 and t.userId = {$userId} and t.sid = {$sid} and t.types in (1,2,3) and rd.status is null";
        $count4 = $todoDM->name("t")
            ->leftJoin("RedDot", "rd", "t.relateId = rd.formId and rd.formName = 'Todo' and rd.userId = '{$userId}' and rd.sid = '{$sid}'")
            ->where($where4)
            ->count() ?: 0;

        //备忘录
        $todayStart = date("Y-m-d 00:00:00");
        $todayEnd = date("Y-m-d 23:59:59");
        $todayCount = $anythingDM->name('a1')
            ->where("a1.certainTime>='{$todayStart}' and a1.certainTime<='{$todayEnd}' and a1.status<>1 and a1.userId={$userId}")
            ->count() ?: 0;

        $count1 = $count2 = $count3 = $count4 = 0;

        return array(
            "任务" => array(
                "two" => "我的任务",
                "three" => "",
                "four" => "我的TODO",
                "count" => $count4,
            ),
//            "积分" => array(
//                "two" => "我的",
//                "three" => "审核",
//                "count" => $count1,
//                "yanshou" => "验收",
//                "count3" => $count3,
//            ),
            "anything" => array(
                "today" => $todayCount,
            )
        );
//        }

//        return true;
    }

    public function dingSDKinit() {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->findOneBy(array("sid" => $this->sid, "namesEn" => "dingtalk"));
        $dingSDK = new DingSDK();
        if ($api) {
            $dingSDK->initConfig($api);
            $this->assign("corpId", $dingSDK->corpid);
        } else {
            $this->assign("corpId", "");
        }

        $this->assign("dingJsApiConfig", $dingSDK->getPCJsApiConfig());
        $this->assign("dingJsApi", true);
    }


}