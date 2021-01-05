<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/8/21
 * Time: 16:45
 */

namespace MobileConsoles\Controller;


use Admin\DModel\CompanyAuthDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\ExternalRelationsDModel;
use Admin\DModel\IndustryDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\RbacRoleDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffGroupDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WorkTypeDModel;
use Admin\Entity\Company;
use Admin\Entity\CompanyAuth;
use Admin\Entity\CompanyMember;
use Admin\Entity\User;
use Admin\Service\CompanyTemplate;

class CompanyController extends CommonController {


    public function index() {

        $sid = $this->getUser("sid");

        $industryDM = IndustryDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();

        $authDM = CompanyAuthDModel::getInstance();
        /** @var CompanyAuth $auth */

        $auth = $authDM->findOneBy(array("sid" => $sid));

        $this->assign("active", "index");
        if (!$auth) {
            return $this->display("noAuth");
        }

        $this->assign("industry", $industryDM->industryMemo($auth->getIndustry()));
        $this->assign("scales", $companyDM->memos["scales"]);
        $this->assign("auth", $auth);

        return $this->display();
    }

    public function lists() {
        $companyDM = CompanyDModel::getInstance();

        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $activeCompany = $companyDM->name("c")->select("u,c")
            ->leftJoin("User", "u", "u.id = c.superid")
            ->where("c.id=$sid")
            ->getOneArray(true);

        $companys = $companyDM->name("c")->select("u,c")
            ->leftJoin("User", "u", "u.id = c.superid")
            ->where("c.superid=$userId and c.id<>$sid")
            ->getArray(true);

        $staffs = $companyDM->name("c")
            ->select("u,c")
            ->innerJoin("Staff", "s", "s.sid=c.id")
            ->leftJoin("User", "u", "u.id = c.superid")
            ->groupBy("c.id")
            ->where("s.userId=$userId and c.id<>$sid and c.superid<>$userId")
            ->getArray(true);

        $this->assign("activeCompany", $activeCompany);
        $this->assign("companys", $companys);
        $this->assign("staffs", $staffs);

        $count = $activeCompany ? 1 : 0;

        $this->assign("count", $count + count($companys) + count($staffs));

        $this->assign("active", "cut");

        return $this->display();
    }

    public function toggle($id) {
        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */

        $company = $companyDM->find($id);

        if (!$company) return $this->error("企业不存在");

        $userId = $this->getUser("id") ?: 0;

        /** @var User $user */

        $user = UserDModel::getInstance()->find($userId);

        if (!$user) return $this->error("您的登录已超时，请重新登录");

        $roleName = "system";

        if ($userId != $company->getSuperid()) {
            $staffDM = StaffDModel::getInstance();
            $staff = $staffDM->findOneBy(array("sid" => $id, "userId" => $userId));
            $roleName = $staff->getRoleName();
            if (!$staff) {
                return $this->error("您还没加入该企业");
            }
        }

        $user->setSid($company->getId());

        $user->setRoleName($roleName);

        UserDModel::getInstance()->save($user)->flush($user);

        RbacAccessDModel::accredit($this->access, "MobileConsoles", $user->getSid(), $user, $user->getRoleName());

        return $this->success(url('mobileConsoles_company_lists'));

    }

    public function NewJoin() {
        $staffDM = StaffDModel::getInstance();

        $options = DepartmentDModel::getInstance()->getOptions($this->sid, 0);
        $this->assign("options", $options);

        $stationDM = StaffStationDModel::getInstance();
        $where = "s.sid =" . $this->sid;
        $stations = $stationDM->name("s")->select("s.id,s.names,s.department")
            ->where($where)
            ->getArray();
        $this->assign("stations", $stations);

        $roleDM = RbacRoleDModel::getInstance();
        $roles = $roleDM->name("r")->where("r.sid={$this->sid} and r.module = 'Consoles'")->order("r.sort", "ASC")->getArray();
        $this->assign("roles", $roles);

        $this->assign("searchUrl", url("~mobileConsoles_company_findUser"));

        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        $post['userId'] = $post['ChoiceId'];

        $station = $stationDM->find($post["station"] ?: 0);

        if (!$station) return $this->error("职位信息获取失败");

        if ($station->getNum() > 0) {
            $count = $staffDM->name("s")->where("s.sid=" . $this->sid . " and s.station=" . $station->getId())->count();
            if ($count >= $station->getNum()) {
                return $this->error(sprintf("【%s】职位人数为%d人，如需要设置更多人员，请到职位管理更改", $station->getNames(), $station->getNum()));
            }
        }

        $staff = $staffDM->findOneBy(array("sid" => $this->sid, "userId" => $post["userId"]));

        if ($staff) return $this->error("您已添加此员工");

        $staffDM->addStaff($this->sid, $post["userId"], "Consoles", $post["roleId"], $post);

        return $this->ajaxReturn(array("status" => 'y', "url" => url('mobileConsoles_user_company')));
    }

    public function findUser() {
        $userDM = UserDModel::getInstance();

        $post = Q()->post->all();

        if (!$post['search']) {
            return $this->ajaxReturn(array("status" => 'n', "info" => "搜索不能为空！"));
        }

        $lists = $userDM->name('u')->where("(u.userName like '%{$post['search']}%' or u.phone like '%{$post['search']}%' or u.fullName like '%{$post['search']}%' or u.userName like '%{$post['search']}%' or u.nickName like '%{$post['search']}%') and u.status = 1")->getArray();
        if (!$lists) {
            return $this->ajaxReturn(array("status" => 'n', "info" => "搜索失败！"));
        }
//        <input class="dian" type="radio" name="FindId" id="FindUser{$v['id']}" value="{$v['id']}" style="display:none;"/>
        $html = '';
        foreach ($lists as $k => $v) {
            $html .= <<<work
<div class="findUser" id="FindUserBox{$v['id']}">
    <div class="wztx">{$v['fullName']}</div>
    <div class="wzxx">{$v['phone']} — {$v['fullName']}</div>
    <div class="dianlabel" onclick="ChoiceId({$v['id']}, '{$v['fullName']}', '{$v['phone']}')">选择</div>
</div>
work;
        }

        $html .= "<div style='clear:both;'></div>";

        return $this->ajaxReturn(array("status" => 'y', "data" => $html));
    }

    public function joined() {
        $this->flushUser();


        $get = Q()->get->all();
        if ($get['company']) {
            $userDM = UserDModel::getInstance();
            $companyMemberDM = CompanyMemberDModel::getInstance();
            $companyDM = CompanyDModel::getInstance();

            $user = $this->getUser();
            $companyEN = $companyDM->name('c')->where("c.codeNo = '{$get['company']}' and c.status = 1")->getOneArray();
            $recEN = $userDM->name("u")->where("u.phone = {$get['user']} and u.status = 1")->getOneArray();
            $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN['id']}' and cm.userId = '{$user->getId()}'")->count() ?: 0;
            if ($companyEN && $count <= 0) {
                $companyMemberEN = $companyMemberDM->newEntity();
                $companyMemberEN->setSid($companyEN['id']);
                $companyMemberEN->setUserId($user->getId());
                $companyMemberEN->setRecId($recEN['id']);
                $companyMemberEN->setStatus(0);
                $companyMemberEN->setAddTime(nowTime());
                $companyMemberEN->setTypes(1);
                $companyMemberEN->setAcorn(0);
                $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
            }
        }

        $memberDM = CompanyMemberDModel::getInstance();

        $userId = $this->getUser("id");

        $lists = $memberDM->name("m")->select("m,c,u")
            ->where("m.userId=$userId and m.status=0")
            ->leftJoin("Company", "c", "c.id= m.sid")
            ->leftJoin("User", "u", "u.id=c.superid")
            ->getArray(true);

        $this->assign("lists", $lists);
        $this->assign("count", count($lists));
        $this->assign("active", "joined");


        return $this->display();
    }

    public function inviteAgree($id) {
        if (Q()->get->get("reject")) {
            return $this->inviteReject($id);
        }

        $userId = $this->getUser("id");

        $memberDM = CompanyMemberDModel::getInstance();

        /** @var CompanyMember $member */

        $member = $memberDM->find($id);

        if (!$member || $member->getUserId() != $userId) {
            return $this->error("获取邀请信息异常，请刷新列表重试");
        }

        if ($member->getStatus() == 1) {
            return $this->error("您已经是企业中的一员");
        }

        $staffDM = StaffDModel::getInstance();

        $staffDM->addStaff($member->getSid(), $userId);

        $member->setIntoTime(nowTime());
        $member->setStatus(1);
        $memberDM->save($member)->flush();

        return $this->success(url("mobileConsoles_company_lists"));
    }

    public function inviteReject($id) {
        $userId = $this->getUser("id");

        $memberDM = CompanyMemberDModel::getInstance();

        /** @var CompanyMember $member */

        $member = $memberDM->find($id);

        if (!$member || $member->getUserId() != $userId) {
            return $this->error("获取邀请信息异常，请刷新列表重试", url("consoles_company_inviteMe"));
        }

        if ($member->getStatus() == 1) {
            return $this->error("您已经是企业中的一员", url("consoles_company_inviteMe"));
        }
        $memberDM->remove($member)->flush($member);

        return $this->success(url("mobileConsoles_company_joined"));
    }

    public function AddGroup() {
        $staffDM = StaffDModel::getInstance();

        $acceptA = $staffDM->workers($this->sid, array(), 1);
        $acceptAHtml = $staffDM->workHtml("acceptA", "选择组长", $acceptA);

        $acceptB = $staffDM->workers($this->sid, array(), 1);
        $acceptBHtml = $staffDM->workHtml("acceptB", "选择副组长", $acceptB);

        $acceptC = $staffDM->workers($this->sid, array(), 999);
        $acceptCHtml = $staffDM->workHtml("acceptC", "选择组员", $acceptC);

        $acceptHtml = array();
        $acceptHtml[] = $acceptAHtml;
        $acceptHtml[] = $acceptBHtml;
        $acceptHtml[] = $acceptCHtml;

        $this->assign("acceptHtml", $acceptHtml);

        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post["acceptA"]) {
            return $this->error("组长不能为空");
        }
        if ($post["acceptA"] == $post["acceptB"]) {
            return $this->error("组长和副组长不能是同一个人");
        }
        $members = explode(",", $post["acceptC"]);
//        if (in_array($post["acceptA"], $members)) {
//            return $this->error("组长不能是同时是组员");
//        }
//        if ($post["acceptB"] && in_array($post["acceptB"], $members)) {
//            return $this->error("副组长不能是同时是组员");
//        }

        if (!$post["names"]) return $this->error("小组名称不能为空");

        $groupDM = StaffGroupDModel::getInstance();
        $hasGroup = $groupDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($hasGroup) return $this->error("存在重复的小组名称");

        $group = $groupDM->newEntity();
        $post["acceptC"] = join(",", array_unique($members));

        $data['names'] = $post['names'];
        $data['subject'] = $post['subject'];
        $data['leader'] = $post['acceptA'];
        $data['helper'] = $post['acceptB'];
        $data['members'] = $post['acceptC'];

        $groupDM->create($data, $group);
        $group->setAddTime(nowTime());
        $group->setSid($this->sid);
        $group->setStatus(1);

        $groupDM->add($group)->flush($group);

        return $this->success(url("mobileConsoles_company_groupLists"));
    }

    //小组列表
    public function groupLists() {
        $groupDM = StaffGroupDModel::getInstance();
        if (Q()->getMethod() == "GET") {
            $groupLists = $groupDM->name('g')->where("g.status = 1 and g.sid={$this->sid}")->getArray();
            $this->assign([
                "groupLists" => $groupLists,
            ]);
            return $this->display();
        }
    }

    //编辑小组
    public function modifyGroup() {
        $groupDM = StaffGroupDModel::getInstance();
        $userDM = UserDModel::getInstance();


        if (Q()->getMethod() == "GET") {
            $get = Q()->get->all();
            $curGroup = $groupDM->name("g")->where("g.id={$get['id']}")->getOneArray();
            if (!$curGroup) {
                return $this->ajaxReturn(array("status" => "n", "info" => "找不到这此小组"));
            }
//            dump($curGroup);exit;
            $staffDM = StaffDModel::getInstance();

            $acceptA = $staffDM->workers($this->sid, $curGroup['leader'], 1);
            $acceptAHtml = $staffDM->workHtml("acceptA", "选择组长", $acceptA);

            $acceptB = $staffDM->workers($this->sid, $curGroup['helper'], 1);
            $acceptBHtml = $staffDM->workHtml("acceptB", "选择副组长", $acceptB);

            $acceptC = $staffDM->workers($this->sid, $curGroup['members'], 999);
            $acceptCHtml = $staffDM->workHtml("acceptC", "选择组员", $acceptC);

            $acceptHtml = array();
            $acceptHtml[] = $acceptAHtml;
            $acceptHtml[] = $acceptBHtml;
            $acceptHtml[] = $acceptCHtml;

            if ($curGroup['leader']) {
                $curGroup['leaders'] = $userDM->name("u")
                    ->select("u.id,u.fullName")
                    ->where("u.id={$curGroup['leader']}")
                    ->getOneArray();
            }
            if ($curGroup['helper']) {
                $curGroup['helpers'] = $userDM->name("u")
                    ->select("u.id,u.fullName")
                    ->where("u.id={$curGroup['helper']}")
                    ->getOneArray();
            }
            if ($curGroup['members']) {
                $members = explode(",", $curGroup['members']);
                $membersArr = array();
                foreach ($members as $k => $v) {
                    $membersArr[] = $userDM->name("u")->select("u.id,u.fullName")->where("u.id={$v}")->getOneArray();
                }
                $curGroup['membersArr'] = $membersArr;
            }

            $this->assign("acceptHtml", $acceptHtml);
            $this->assign("curGroup", $curGroup);
            return $this->display();
        }

        $post = Q()->post->all();
        dump($post);
        exit;

    }

    //删除小组
    public function delGroup() {
        $groupDM = StaffGroupDModel::getInstance();
        $post = Q()->post->all();
        $curGroup = $groupDM->find($post['id']);
        if (!$curGroup) {
            return $this->ajaxReturn(array("status" => "n", "info" => "查无此小组"));
        }
        $groupDM->remove($curGroup)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_company_groupLists")));
    }


    //选择部门
    public function chooseDepartment() {
        $comMebDM = CompanyMemberDModel::getInstance();

        $arr = $this->queryDepartment();
        $department = $arr['departmentArr'];
        $ids = $arr['idsArr'];
        $staffDM = StaffDModel::getInstance();

        if (Q()->getMethod() == "GET") {
            $get = Q()->get->all();
            $oldArr = array();
            $oldStaff = $staffDM->name("s")->where("s.userId={$get['userid']} and s.sid={$get['sid']} and s.status=1")->getArray();
            foreach ($oldStaff as $k => $v) {
                foreach ($ids as $kk => $vv) {
                    if ($v['department'] == $vv) {
                        $oldArr[] = $department[$kk];
                    }
                }
            }
            $this->assign([
                "allDepartment" => $department,
                "oldArr" => $oldArr,
                "userid" => $get['userid'],
                "sid" => $get['sid'],
            ]);
            return $this->display();
        }

        $post = Q()->post->all();
        //$brr->用户选中的部门(中文)
        //$crr->用户选中的部门(转换后的部门id)
        //$drr->旧的部门和新的部门一样(相当于不用改变id在这个数组中的数据)
        //$err->旧的部门和新的部门不一样的(相当于要删除的数据)
        //$frr->用户选中的部门，去除不用改变的数据(相当于需要新增的数据)
        $brr = $crr = $drr = $err = $frr = array();
        $count = 0;
        foreach ($post as $k => $v) {
            if ($v == "on") {
                $count++;
                $brr[] = $k;
            }
        }
        //没有on代表一个部门都没有
        if ($count == 0) {
            $oldUserDep = $staffDM->name("s")->where("s.userId={$post['userid']} and s.sid={$post['sid']} and s.status=1")->order("s.id", "desc")->getArray();
            foreach ($oldUserDep as $k => $v) {
                if ($k == 0) {
                    $modifyData = $staffDM->find($v['id']);
                    $modifyData->setDepartment(0);
                    $staffDM->save($modifyData)->flush();

                    $comMebEN = $comMebDM->name("c")->where("c.userId={$post['userid']} and c.sid={$post['sid']} and c.status=1")->setMax(1)->getOneArray();
                    if (!$comMebEN) {
                        $data2 = array();
                        $data2['userId'] = $post['userid'];
                        $data2['sid'] = $post['sid'];
                        $data2['addTime'] = nowTime();
                        $data2['types'] = 1;
                        $data2['status'] = 1;
                        $data2['acron'] = 0;
                        $comMebDM->create($data2, $comMebEN = $comMebDM->newEntity());
                        $comMebDM->add($comMebEN)->flush();
                    }
                } else {
                    $delData = $staffDM->find($v['id']);
                    $staffDM->remove($delData)->flush();
                }
            }
            return $this->ajaxReturn(array("status" => "y", "info" => "修改成功", "url" => url("mobileConsoles_user_company")));
        }


        foreach ($brr as $kk => $vv) {
            foreach ($department as $k => $v) {
                if ($v == $vv) {
                    $crr[] = $ids[$k];
                }
            }
        }

        $oldStaff = $staffDM->name("s")->where("s.userId={$post['userid']} and s.sid={$post['sid']} and s.status=1")->getArray();
        foreach ($oldStaff as $k => $v) {
            if (in_array($v['department'], $crr)) {
                $drr[] = $v['department'];

            } else {
                $err[] = $v['department'];
            }
        }

        foreach ($crr as $k => $v) {
            if (!in_array($v, $drr)) {
                $frr[] = $v;
            }
        }

        if ($err) {
            foreach ($err as $v) {
                $del = $staffDM->findOneBy(array("userId" => $post['userid'], "sid" => $post['sid'], "status" => 1, "department" => $v));
                if (!del) {
                    return $this->ajaxReturn(array("status" => "n", "info" => "查无此记录"));
                }
                $staffDM->remove($del)->flush();
            }
        }


        if ($frr) {
            $userDM = UserDModel::getInstance();
            $user = $userDM->name('u')->where("u.id={$post['userid']}")->getOneArray();
            if (!$user) {
                return $this->ajaxReturn(array("status" => "n", "info" => "没有此用户"));
            }

            $data = array();
            $data['sid'] = $post['sid'];
            $data['fullName'] = $user['fullName'];
            $data['userId'] = $user['id'];
            $data['userName'] = $user['nickName'];
            $data['roleName'] = "staff";
            $data['phone'] = $user['phone'];
            $data['status'] = 1;

            foreach ($frr as $v) {
                $data['department'] = $v;
                $data['addTime'] = nowTime();
                $staffDM->create($data, $staffEN = $staffDM->newEntity());
                if (!$staffDM->check($data, $staffEN)) {
                    return $this->error($staffDM->getError());
                }
                $staffDM->add($staffEN)->flush();

                $comMebEN = $comMebDM->name("c")->where("c.userId={$post['userid']} and c.sid={$post['sid']} and c.status=1")->setMax(1)->getOneArray();
                if (!$comMebEN) {
                    $data2 = array();
                    $data2['userId'] = $post['userid'];
                    $data2['sid'] = $post['sid'];
                    $data2['addTime'] = nowTime();
                    $data2['types'] = 1;
                    $data2['status'] = 1;
                    $data2['acron'] = 0;
                    $comMebDM->create($data2, $comMebEN = $comMebDM->newEntity());
                    $comMebDM->add($comMebEN)->flush();
                }
            }
        }

        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功", "url" => url("mobileConsoles_user_company")));
    }


    //添加职位
    public function addStaffstation() {
        if (Q()->getMethod() == "GET") return $this->display();
        $staffStationDM = StaffStationDModel::getInstance();
        $post = Q()->post->all();
        if ($post['department'] == "请选择部门") {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择部门！"));
        } else {
            $arr = $this->queryDepartment();
            $department = $arr['departmentArr'];
            $ids = $arr['idsArr'];
            //获得所选部门id
            foreach ($department as $k => $v) {
                if ($v == $post['department']) {
                    $post['department'] = $ids[$k];
                }
            }
        }

        $same = $staffStationDM->name("s")->where("s.names='{$post['names']}' and s.sid={$this->sid} and s.department={$post['department']}")->getArray();
        if ($same) {
            return $this->ajaxReturn(array("status" => "n", "info" => "此部门下存在相同职位名"));
        }
        $post['limitAcorn'] = ($post['limitAcorn'] == "on") ? 1 : 0;
        if ($post['limitAcorn'] == 1) {
            $post['riseAcorn'] = $post['riseAcorn'] ? $post['riseAcorn'] : 1;
        }
        $post['status'] = ($post['status'] == "on") ? 1 : 0;
        $post['sid'] = $this->sid;

        $staffStationDM->create($post, $staffStationEN = $staffStationDM->newEntity());
        if (!$staffStationDM->check($post, $staffStationEN)) {
            return $this->error($staffStationDM->getError());
        }
        $staffStationDM->add($staffStationEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功！", "url" => url("mobileConsoles_user_company")));
    }


    //修改职位
    public function modifyStaffstation() {
        $staffStationDM = StaffStationDModel::getInstance();
        if (Q()->getMethod() == "GET") {
            $get = Q()->get->all();
            $staffStationEN = $staffStationDM->name("wt")->where("wt.id={$get['id']}")->getOneArray();
            $arr = $this->queryDepartment();
            $department = $arr['departmentArr'];
            $ids = $arr['idsArr'];
            //获得所选部门id
            foreach ($ids as $k => $v) {
                if ($v == $staffStationEN['department']) {
                    $curDeparment = $department[$k];
                }
            }
            $this->assign([
                "staffStation" => $staffStationEN,
                "curDeparment" => $curDeparment,
                "isSuper" => $this->isSuper(),
            ]);
            return $this->display();
        }
        $post = Q()->post->all();
        $arr = $this->queryDepartment();
        $department = $arr['departmentArr'];
        $ids = $arr['idsArr'];
        //获得所选部门id
        foreach ($department as $k => $v) {
            if ($v == $post['department']) {
                $post['department'] = $ids[$k];
            }
        }
        $same = $staffStationDM->name("s")->where("s.names='{$post['names']}' and s.sid={$this->sid} and s.department={$post['department']} and s.id!={$post['id']}")->getArray();
        if ($same) {
            return $this->ajaxReturn(array("status" => "n", "info" => "此部门下存在相同职位名"));
        }

        $post['limitAcorn'] = ($post['limitAcorn'] == "on") ? 1 : 0;
        if ($post['limitAcorn'] == 1) {
            $post['riseAcorn'] = $post['riseAcorn'] ? $post['riseAcorn'] : 1;
        }
        $post['status'] = ($post['status'] == "on") ? 1 : 0;
        $id = $post['id'];
        unset($post['id']);
        $staffStationEN = $staffStationDM->find($id);
        if (!$staffStationEN) {
            return $this->ajaxReturn(array("status" => "n", "info" => "没有此条记录"));
        }
        $staffStationDM->create($post, $staffStationEN);
        if (!$staffStationDM->check($post, $staffStationEN)) {
            return $this->error($staffStationDM->getError());
        }
        $staffStationDM->save($staffStationEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功！", "url" => url("mobileConsoles_user_company", array("active" => "ZHIWEI"))));
    }

    //删除职位
    public function delStaffstation() {
        $post = Q()->post->all();
        $stationDM = StaffStationDModel::getInstance();
        $curStation = $stationDM->find($post['id']);
        if (!$curStation) {
            return $this->ajaxReturn(array("status" => "n", "info" => "没有此职位"));
        }
        $staffDM = StaffDModel::getInstance();
        $checkStation = $staffDM->name("s")->where("s.sid={$this->sid} and s.station={$curStation->getId()}")->getArray();
        if ($checkStation) {

            foreach ($checkStation as $k => $v) {
                $modify = $staffDM->find($v['id']);
//                dump($modify);exit;
                $modify->setStation(0);
                $staffDM->save($modify)->flush();
            }
        }
        $stationDM->remove($curStation)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_user_company")));

    }

    //添加工种
    public function addWorktype() {
        if (Q()->getMethod() == "GET") return $this->display();
        $worktypeDM = WorkTypeDModel::getInstance();
        $post = Q()->post->all();
        $post['status'] = ($post['status'] == "on") ? 1 : 0;
        $post['sid'] = $this->sid;
        $worktypeDM->create($post, $worktypeEN = $worktypeDM->newEntity());
        if (!$worktypeDM->check($post, $worktypeEN)) {
            return $this->error($worktypeDM->getError());
        }
        $worktypeDM->add($worktypeEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功！", "url" => url("mobileConsoles_user_company")));
    }


    //修改工种
    public function modifyWorktype() {
        $worktypeDM = WorkTypeDModel::getInstance();
        if (Q()->getMethod() == "GET") {
            $get = Q()->get->all();
            $worktypeEN = $worktypeDM->name("wt")->where("wt.id={$get['id']}")->getOneArray();
            $this->assign([
                "worktype" => $worktypeEN,
                "isSuper" => $this->isSuper(),
            ]);

            return $this->display();
        }
        $post = Q()->post->all();
        $post['status'] = ($post['status'] == "on") ? 1 : 0;
        $id = $post['id'];
        unset($post['id']);
        $worktypeEN = $worktypeDM->find($id);
        if (!$worktypeEN) {
            return $this->ajaxReturn(array("status" => "n", "info" => "没有此条记录"));
        }
        $worktypeDM->create($post, $worktypeEN);
        if (!$worktypeDM->check($post, $worktypeEN)) {
            return $this->error($worktypeDM->getError());
        }
        $worktypeDM->save($worktypeEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功！", "url" => url("mobileConsoles_user_company")));
    }

    //删除工种
    public function delWorktype() {
        $post = Q()->post->all();
        $worktypeDM = WorkTypeDModel::getInstance();
        $curWorktype = $worktypeDM->find($post['id']);
        if (!$curWorktype) {
            return $this->ajaxReturn(array("status" => "n", "info" => "没有此工种"));
        }
        $worktypeDM->remove($curWorktype)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_user_company")));
    }


    //查找已有职位
    public function queryStation($param) {

        $arr = $this->queryDepartment();
        $department = $arr['departmentArr'];
        $ids = $arr['idsArr'];
        //获得所选部门id
        foreach ($department as $k => $v) {
            if ($v == $param) {
                $param = $ids[$k];
            }
        }

        $stationDM = StaffStationDModel::getInstance();
        $stationEN = $stationDM->name('s')->where("s.status=1 and s.department={$param}")->getArray();
        $idsArr = array();
        $namesArr = array();
        foreach ($stationEN as $k => $v) {
            $idsArr[] = $v['id'];
            $namesArr[] = $v['names'];
        }
        return array("ids" => $idsArr, "names" => $namesArr);
    }

    //ajax返回已有职位
    public function ajaxStation() {
        $post = Q()->post->all();
        if ($post['department'] == "请选择部门") {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择部门"));
        }
        $param = $post['department'];
        $arr = $this->queryStation($param);
        return $this->ajaxReturn($arr);
    }


    //查找已有部门名称和id，因为AUI的值只允许有一个，不能像HTML的select一样，所以要分开
    public function queryDepartment() {
        $department = $this->getOptions($this->sid, 0);
        $department = explode(",", rtrim($department, ","));
        $ids = $this->getIds($this->sid, 0);
        $ids = explode(",", rtrim($ids, ","));
        return array("departmentArr" => $department, "idsArr" => $ids);
    }

    //ajax返回已有部门
    public function ajaxDepartment() {
        $arr = $this->queryDepartment();
        return $this->ajaxReturn($arr);
    }

    //修改部门(列表)
    public function departmentLists() {
        $arr = $this->queryDepartment();
        $department = $arr['departmentArr'];
        $ids = $arr['idsArr'];

        $allDepartment = array_combine($ids, $department);
        if (in_array("", $allDepartment)) {
            $allDepartment = null;
        }

        if (Q()->getMethod() == "GET") {
            $this->assign([
                "allDepartment" => $allDepartment,
            ]);
            return $this->display();
        }
    }

    //删除部门
    public function delDepartment() {
//        dump(Q()->post->all());exit;
        $post = Q()->post->all();
        $id = $post['id'];
        $departmentDM = DepartmentDModel::getInstance();
//        $curDepartment = $departmentDM->find($id);
        $findlower = $departmentDM->findOneBy(array("parentid" => $id));
        if ($findlower) {
            return $this->ajaxReturn(array("status" => "n", "info" => "此部门含有下级部门"));
        }
        $staffDM = StaffDModel::getInstance();
        $staffEN = $staffDM->name("s")->where("s.department={$id} and s.sid={$this->sid} and s.status=1")->getArray();
//        dump($staffEN);exit;
        foreach ($staffEN as $k => $v) {
            $isSingle = $staffDM->name("s")
                ->where("s.sid={$this->sid} and s.userId={$v['userId']} and s.id!={$v['id']} and s.department!=0")
                ->getArray();
//            dump($isSingle);exit;
            if ($isSingle) {
                $del = $staffDM->find($v['id']);
                $staffDM->remove($del)->flush();
            } else {
                $modify = $staffDM->find($v['id']);
                $data = array("department" => 0);
                $staffDM->create($data, $modify);
                $staffDM->save($modify)->flush();
            }
        }

        $stationDM = StaffStationDModel::getInstance();
        $stationEN = $stationDM->name("st")->where("st.department={$id}")->getArray();
        if ($stationEN) {
            foreach ($stationEN as $kk => $vv) {
                $removeStation = $stationDM->find($vv['id']);
                $stationDM->remove($removeStation)->flush();
            }
        }

        $departmentEN = $departmentDM->find($id);
        $departmentDM->remove($departmentEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_company_departmentLists")));

    }


    //查找部门下的同事
    public function queryColleague($depId = 0) {
        $staffDM = StaffDModel::getInstance();
        $staffEN = $staffDM->name('s')->where("s.department={$depId} and s.sid={$this->sid} and s.status=1")->getArray();
//        dump($staffEN);exit;
        $ids = $names = array();
        foreach ($staffEN as $k => $v) {
            $ids[] = $v['id'];
            $names[] = $v['fullName'];
        }
        return array("ids" => $ids, "names" => $names);
    }

    //ajax返回部门下的同事
    public function ajaxColleague() {
        $post = Q()->post->all();

        $arr = $this->queryColleague($post['depid']);
//        dump($arr);exit;
        return $this->ajaxReturn(array("namesArr" => $arr['names']));
    }

    //修改部门
    public function modifyDepartment() {
        $departmentDM = DepartmentDModel::getInstance();
        if (Q()->getMethod() == "GET") {
            $get = Q()->get->all();

            $arr = $this->queryDepartment();
            $department = $arr['departmentArr'];
            $ids = $arr['idsArr'];

            $id = $get['id'];
            $curDepartment = $departmentDM->find($id);
            if ($curDepartment->getParentid() == 0) {
                $upper = "作为顶级部门";
            } else {
                $upper = $departmentDM->name("d")->where("d.id={$curDepartment->getParentid()}")->getOneArray();
                $upperId = $upper['id'];
                foreach ($ids as $k => $v) {
                    if ($v == $upperId) {
                        $upper = $department[$k];
                    }
                }
            }
            $this->assign([
                "id" => $id,
                "upper" => $upper,
                "curNames" => $curDepartment->getNames(),
                "curDescription" => $curDepartment->getDescription(),
                "curDirector" => $curDepartment->getDirector()
            ]);
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post['names']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写部门名称"));
        }

        $duplicate = $departmentDM->name("d")->where("d.sid={$this->sid} and d.names='{$post['names']}' and d.id!={$post['id']}")->getArray();
        if ($duplicate) {
            return $this->ajaxReturn(array("status" => "n", "info" => "存在相同的部门名称"));
        }

        if ($post['parentid'] == "作为顶级部门") {
            $post['parentid'] = 0;
        } else {
            $arr = $this->queryDepartment();
            $department = $arr['departmentArr'];
            $ids = $arr['idsArr'];
            //获得所选部门id
            foreach ($department as $k => $v) {
                if ($v == $post['parentid']) {
                    $post['parentid'] = $ids[$k];
                }
            }
        }

        if ($post['director'] == "请选择负责人(可为空)") {
            $post['director'] = null;
        }

        if ($post['parentid'] == $post['id']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "不能选择自身"));
        }


        $modify = $departmentDM->find($post['id']);
        if (!$modify) {
            return $this->ajaxReturn(array("status" => "n", "info" => "找不到此部门"));
        }
        unset($post['id']);
//        dump($post);exit;
        $departmentDM->create($post, $modify);
        if (!$departmentDM->check($post, $modify)) {
            return $this->error($departmentDM->getError());
        }
        $departmentDM->save($modify)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功", "url" => url("mobileConsoles_company_departmentLists")));
    }

    //添加部门
    public function addDepartment() {
        $departmentDM = DepartmentDModel::getInstance();
        if (Q()->getMethod() == "GET") return $this->display();
        $post = Q()->post->all();

        if (!$post['names']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写部门名称"));
        }

        $same = $departmentDM->name("d")->where("d.sid={$this->sid} and d.names='{$post['names']}'")->getArray();
        if ($same) {
            return $this->ajaxReturn(array("status" => "n", "info" => "存在相同的部门名称"));
        }

        if ($post['parentid'] == "作为顶级部门") {
            $post['parentid'] = 0;
        } else {
            $arr = $this->queryDepartment();
            $department = $arr['departmentArr'];
            $ids = $arr['idsArr'];
            //获得所选部门id
            foreach ($department as $k => $v) {
                if ($v == $post['parentid']) {
                    $post['parentid'] = $ids[$k];
                }
            }
        }
        $post['sid'] = $this->sid;
        $post['status'] = 1;

        $departmentDM->create($post, $departmentEN = $departmentDM->newEntity());
        if (!$departmentDM->check($post, $departmentEN)) {
            return $this->error($departmentDM->getError());
        }
        $departmentDM->add($departmentEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => url("mobileConsoles_company_departmentLists")));
    }

    //查询所有部门，递归分清层级
    public function getOptions($sid, $selectId, $pid = 0, $depth = 0) {
        $departmentDM = new DepartmentDModel();
        $where = "d.parentid = $pid and d.sid=$sid";
        $section = $departmentDM->name('d')->where($where)->getArray(false, false);
        if (!$section) {
            return;
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
//            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= $depthstr . $sec["names"] . ",";
            $options .= $this->getOptions($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

    //查询所有部门对应的id，递归分清层级
    public function getIds($sid, $selectId, $pid = 0, $depth = 0) {
        $departmentDM = new DepartmentDModel();
        $where = "d.parentid = $pid and d.sid=$sid";
        $section = $departmentDM->name('d')->where($where)->getArray(false, false);
        if (!$section) {
            return;
        }
        $idArr = "";
        $i = 0;
        foreach ($section as $sec) {
//            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $idArr .= $sec["id"] . ",";
            $idArr .= $this->getIds($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $idArr;
    }

    //拼接的字符串
    private function depth($i, $count, $depth) {
        if ($depth == 0) {
            return "";
        }
        $nbsp = '';
        $return = "";
        for ($j = 0; $j < $depth; $j++) {
            $return .= $nbsp;
        }
        return ($i + 1) < $count ? $return . "├" : $return . "└";
    }

    public function supers() {

        $companyDM = CompanyDModel::getInstance();
        $sid = $this->getUser("sid") ?: 0;
        if (Q()->isGet()) {
            $userDM = UserDModel::getInstance();

            $company = $companyDM->name("c")->where("c.id=$sid")->setMax(1)->getOneArray();

            $companyUser = $userDM->find($company["superid"] ?: 0);
            $subSuperid = preg_replace("/\,{2,}/", ",", $company["subSuperid"]);
            $subSuperid = trim($subSuperid, ",");
            if ($subSuperid) {
                $subUsers = $userDM->name("u")->where("u.id in ({$subSuperid})")->getArray();
                $this->assign("subUsers", $subUsers);
            } else {
                $this->assign("subUsers", array());
            }
            $this->assign("companyUser", $companyUser);
            $this->assign("company", $company);
            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workers($this->sid);
            $this->assign("executors", $executors);

            return $this->display();
        }

        /** @var Company $companyEN */

        $companyEN = $companyDM->find($sid);

        if (!$companyEN) return $this->error("企业信息获取失败！");


        $post = Q()->post->all();

        $executors = explode(",", $post["executors"]);

        $superid = $companyEN->getSuperid();

        $executors = array_filter($executors, function ($val) use ($superid) {
            return $val && $val != $superid;
        });
        $subUserIds = explode(",", $companyEN->getSubSuperid());

        $executors = array_merge($subUserIds, $executors);

        $executors = array_unique($executors);
        $executorsStr = preg_replace("/\,{2,}/", ",", join(",", $executors));

        $companyEN->setSubSuperid(trim($executorsStr, ","));
        $companyDM->save($companyEN)->flush($companyEN);
        return $this->success("添加成功");
    }

    public function superDel($id) {
        $companyDM = CompanyDModel::getInstance();
        $sid = $this->getUser("sid") ?: 0;
        /** @var Company $companyEN */
        $companyEN = $companyDM->find($sid);

        if (!$companyEN) return $this->error("企业信息获取失败！");

        $subUserIds = explode(",", $companyEN->getSubSuperid());

        $newsubUserIds = array();

        foreach ($subUserIds as $subUserId) {
            if ($id == $subUserId || !$subUserId) continue;
            $newsubUserIds[] = $subUserId;
        }
        $companyEN->setSubSuperid(join(",", $newsubUserIds));
        $companyDM->save($companyEN)->flush($companyEN);
        return $this->success("删除成功");
    }

    //创建团队/企业
    public function addTeam() {
        $industryDM = IndustryDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $authDM = CompanyAuthDModel::getInstance();
        $temDM = new CompanyTemplate();

        $industryArr = $this->queryIndustry();

        $sourceArr = $companyDM->memos["source"];
        $sourceNames = $sourceIds = array();
        foreach ($sourceArr as $k => $v) {
            $sourceIds[] = $k;
            $sourceNames[] = $v;
        }
        $scalesArr = $companyDM->memos["scales"];
        $scalesNames = $scalesIds = array();
        foreach ($scalesArr as $k => $v) {
            $scalesIds[] = $k;
            $scalesNames[] = $v;
        }

        $templateArr = $temDM->getLists();
        $templateNames = $templateIds = array();
        foreach ($templateArr as $k => $v) {
            foreach ($v as $kk => $vv) {
                if ($kk == 0) {
                    $templateIds[] = $vv;
                } else {
                    $templateNames[] = $vv;
                }
            }
        }

        if (Q()->isGet()) {

            $this->assign("sourceNames", $sourceNames);
            $this->assign("sourceNames", $sourceNames);
            $this->assign("industryNames", $industryArr['industryArr']);
            $this->assign("scalesNames", $scalesNames);
            $this->assign("templateNames", $templateNames);

            return $this->display();
        }

        $post = Q()->post->all();
        if ($post['industry'] == "选择行业" || !$post['industry']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择行业"));
        }
        if ($post['scale'] == "选择规模" || !$post['scale']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择规模"));
        }
        if ($post['source'] == "选择途径" || !$post['source']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择途径"));
        }
        if ($post['template'] == "选择模板" || !$post['template']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择模板"));
        }
        if (!$post['names']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写公司名称"));
        }
        if (!$post['contact']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写联系人姓名"));
        }
        if (!$post['contactPhone']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写联系人手机"));
        }
        if (!$post['email']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写邮箱"));
        }
        if (!$post['address']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写详细地址"));
        }
        if (!preg_match("/^1[3456789]{1}\d{9}$/", $post['contactPhone'])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "手机号码格式错误"));
        }
        if (!preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/", $post['email'])) {
            return $this->ajaxReturn(array("status" => "n", "info" => "邮箱格式错误"));
        }

        foreach ($industryArr['industryArr'] as $k => $v) {
            if ($post['industry'] == $v) {
                $post['industry'] = $industryArr['idsArr'][$k];
            }
        }
        foreach ($scalesNames as $k => $v) {
            if ($post['scale'] == $v) {
                $post['scale'] = $scalesIds[$k];
            }
        }
        foreach ($sourceNames as $k => $v) {
            if ($post['source'] == $v) {
                $post['source'] = $sourceIds[$k];
            }
        }
        foreach ($templateNames as $k => $v) {
            if ($post['template'] == $v) {
                $post['template'] = $templateIds[$k];
            }
        }
        $adds = explode(" ", $post['adds']);
        foreach ($adds as $k => $v) {
            if ($k == 0) {
                $post['province'] = $v;
            }
            if ($k == 1) {
                $post['city'] = $v . "市";
            }
            if ($k == 2) {
                $post['area'] = $v;
            }
        }
        $template = $post['template'];
        unset($post['template']);
        unset($post['adds']);

        $auth = $authDM->findOneBy(array("names" => $post["names"]));
        if ($auth) {
//            return $this->error("企业名称已存在");
            return $this->ajaxReturn(array("status" => "n", "info" => "企业名称已存在"));
        }

        $com = $companyDM->findOneBy(array("names" => $post["names"]));
        if ($com) {
//            return $this->error("企业名称已存在");
            return $this->ajaxReturn(array("status" => "n", "info" => "企业名称已存在"));
        }

        $comData = $authData = array();

        $comData['names'] = $post['names'];
        $comData['superid'] = $this->getUser("id");
//        $comData['legal'] = $this->getUser("fullName");
        $comData['industry'] = $post['industry'];
        $comData['scales'] = $post['scales'];
        $comData['province'] = $post['province'];
        $comData['city'] = $post['city'];
        $comData['area'] = $post['area'];
        $comData['address'] = $post['address'];
        $comData['status'] = 1;
        $comData['source'] = $post['source'];
        $comData['codeNo'] = rand_string();
        $comData['addTime'] = nowTime();
        $comData['expireTime'] = date("Y-m-d H:i:s", strtotime("+1year"));

        $companyDM->create($comData, $companyEN = $companyDM->newEntity());
        if (!$companyDM->check($comData, $companyEN)) {
//            return $this->error($companyDM->getError());
            return $this->ajaxReturn(array("status" => "n", "info" => $companyDM->getError()));
        }
        $companyDM->add($companyEN)->flush();

        $temDM->import($companyEN->getId(), $template);//企业模板

        $authData['names'] = $post['names'];
        $authData['userId'] = $this->getUser("id");
//        $authData['legal'] = $this->getUser("fullName");
        $authData['industry'] = $post['industry'];
        $authData['scales'] = $post['scales'];
        $authData['province'] = $post['province'];
        $authData['city'] = $post['city'];
        $authData['area'] = $post['area'];
        $authData['address'] = $post['address'];
        $authData['contact'] = $post['contact'];
        $authData['contactPhone'] = $post['contactPhone'];
        $authData['email'] = $post['email'];
        $authData['status'] = 3;
        $authData['sid'] = $companyEN->getId();
        $authDM->create($authData, $authEN = $authDM->newEntity());
        if (!$authDM->check($authData, $authEN)) {
//            return $this->error($authDM->getError());
            return $this->ajaxReturn(array("status" => "n", "info" => $authDM->getError()));
        }

        $authDM->add($authEN)->flush();
        //自动添加一个部门
        $departmentDM = DepartmentDModel::getInstance();
        $departEN = $departmentDM->newEntity();
        $departEN->setSid($companyEN->getId());
        $departEN->setParentid(0);
        $departEN->setNames('公司');
        $departEN->setStatus(1);
        $departmentDM->add($departEN)->flush($departEN);

        //自动添加一个职位
        $stationDM = StaffStationDModel::getInstance();
        $stationEN = $stationDM->newEntity();
        $stationEN->setSid($companyEN->getId());
        $stationEN->setDepartment(0);
        $stationEN->setNames('-');
        $stationEN->setNum(0);
        $stationEN->setStatus(1);
        $stationDM->add($stationEN)->flush($stationEN);


        $comMebDM = CompanyMemberDModel::getInstance();
        $count = $comMebDM->name('cm')->where("cm.sid = '{$companyEN->getId()}' and cm.userId = '{$this->getUser("id")}'")->count() ?: 0;
        if ($companyEN && $count <= 0) {//创建人添加自己进入该团队
            $data2 = array();
            $data2['userId'] = $this->getUser('id');
            $data2['sid'] = $companyEN->getId();
            $data2['addTime'] = nowTime();
            $data2['types'] = 1;
            $data2['status'] = 1;
            $data2['department'] = $departEN->getId();
            $data2['station'] = $stationEN->getId();
            $data2['phone'] = $this->getUser('phone');
            $comMebDM->create($data2, $comMebEN = $comMebDM->newEntity());
            $comMebDM->add($comMebEN)->flush();
        }
        $staffDM = StaffDModel::getInstance();

        $count = $staffDM->name('s')->where("s.sid = '{$companyEN->getId()}' and s.userId = '{$this->getUser("id")}'")->count() ?: 0;

        if ($companyEN && $count <= 0) {//创建人添加自己进入该团队
            $data = array();
            $data['sid'] = $companyEN->getId();
            $data['fullName'] = $this->getUser('fullName');
            $data['userId'] = $this->getUser('id');
            $data['userName'] = $this->getUser('userName');
            $data['roleName'] = "system";
            $data['department'] = $departEN->getId();
            $data['station'] = $stationEN->getId();
            $data['phone'] = $this->getUser('phone');
            $data['addTime'] = nowTime();
            $data['status'] = 1;
            $staffDM->create($data, $staffEN = $staffDM->newEntity());
            $staffDM->add($staffEN)->flush();
        }

        if ($this->getUser("sid") == 0) {
            $this->toggle($companyEN->getId());
        }
        $temDM->import($companyEN->getId(), 'general.yml');//导入通用模板
        return $this->ajaxReturn(array("status" => "y", "info" => "成功创建团队/企业，主企业已更改", "url" => url("mobileConsoles_company_lists")));

    }

    //查找已有行业名称和id，因为AUI的值只允许有一个，不能像HTML的select一样，所以要分开
    public function queryIndustry() {
        $industry = $this->getIndustryOptions($this->sid, 0);
        $industry = explode(",", rtrim($industry, ","));
        $ids = $this->getIndustryIds($this->sid, 0);
        $ids = explode(",", rtrim($ids, ","));
        return array("industryArr" => $industry, "idsArr" => $ids);
    }

    //ajax返回已有行业
    public function ajaxIndustry() {
        $arr = $this->queryIndustry();
        return $this->ajaxReturn($arr);
    }

    //查询所有行业，递归分清层级
    public function getIndustryOptions($selectId, $pid = 0, $depth = 0) {
        $insudtryDM = IndustryDModel::getInstance();
        $where = "i.parentid = $pid";
        $section = $insudtryDM->name('i')->where($where)->order("i.sort", "desc")->getArray(false, false);
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $depthstr = $this->depth($i, $count, $depth);
            $options .= $depthstr . $sec["names"] . ",";
            $options .= $this->getIndustryOptions($selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

    //查询所有部门对应的id，递归分清层级
    public function getIndustryIds($sid, $selectId, $pid = 0, $depth = 0) {
        $insudtryDM = IndustryDModel::getInstance();
        $where = "i.parentid = $pid";
        $section = $insudtryDM->name('i')->where($where)->getArray(false, false);
        if (!$section) {
            return;
        }
        $idArr = "";
        $i = 0;
        foreach ($section as $sec) {
            $idArr .= $sec["id"] . ",";
            $idArr .= $this->getIndustryIds($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $idArr;
    }

    /**
     * 外部联系人
     */
    public function addWBLXR() {
        $this->assign("isSuper", $this->isSuper());
        $exDM = ExternalRelationsDModel::getInstance();
//        获取任务项目
        $tgDM = TaskGroupDModel::getInstance();
        $TGLists = $tgDM->name("tg")->where("tg.sid = {$this->getUser('sid')} and tg.status = 1")->getArray();
        $this->assign("TGLists", $TGLists);

        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['phone']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "手机号不能为空"));
        } else {
            $exGA = $exDM->name("ex")->where("ex.phone = '{$post['phone']}'")->getArray();
            if ($exGA) {
                return $this->ajaxReturn(array("status" => "n", "info" => "该手机号已添加"));
            }
        }

        $arrTGID = array();
        foreach ($post['tgId'] as $k => $v) {
            $arrTGID[] = $k;
        }

        $tgId = implode(",", $arrTGID) ?: -1;

        $userDM = UserDModel::getInstance();
        $userEN = $userDM->name("u")->where("u.phone = '{$post['phone']}'")->getArray();
        $userId = $userEN[0]['id'] ?: 0;

        $exEN = $exDM->newEntity();
        $exEN->setSid($this->sid);
        $exEN->setPhone($post['phone']);
        $exEN->setAddTime(nowTime());
        $exEN->setStatus(1);
        $exEN->setMemo($post['memo']);
        $exEN->setTgId($tgId);
        $exEN->setUserId($userId);
        $exDM->add($exEN)->flush();

        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => url("mobileConsoles_user_company", array("active" => "WBLXR"))));
    }

    public function modifyWBLXR($id) {
        $this->assign("isSuper", $this->isSuper());
        $exDM = ExternalRelationsDModel::getInstance();
        $userDM = UserDModel::getInstance();

        $exEN = $exDM->name("ex")->where("ex.id = {$id}")->getOneArray();
        $arrTGID = array();
        if ($exEN['tgId'] != -1) {
            $exEN['tgId'] = explode(",", $exEN['tgId']);
            foreach ($exEN['tgId'] as $v) {
                $arrTGID[$v] = $v;
            }
            $exEN['tgId'] = $arrTGID;
            $count = count($exEN['tgId']);
        } else {
            $count = 0;
            $exEN['tgId'] = $arrTGID;
        }
        $this->assign("exEN", $exEN);
        $this->assign("TGCount", $count);

        if ($exEN['userId']) {
            $userEN = $userDM->name("u")->where("u.id = {$exEN['userId']}")->getOneArray();
            $this->assign("userEN", $userEN);
        }

//        获取任务项目
        $tgDM = TaskGroupDModel::getInstance();
        $TGLists = $tgDM->name("tg")->where("tg.sid = {$this->getUser('sid')} and tg.status = 1")->getArray();
        $this->assign("TGLists", $TGLists);

        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post['phone']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "手机号不能为空"));
        }

        $arrTGID = array();
        foreach ($post['tgId'] as $k => $v) {
            $arrTGID[] = $k;
        }

        $tgId = implode(",", $arrTGID) ?: -1;

        $userDM = UserDModel::getInstance();
        $userEN = $userDM->name("u")->where("u.phone = '{$post['phone']}'")->getArray();
        $userId = $userEN[0]['id'] ?: 0;

        $exNew = $exDM->find($exEN['id']);
        if (!$exNew) {
            return $this->ajaxReturn(array("status" => "n", "info" => "数据查询失败！"));
        }
        $exNew->setSid($this->sid);
        $exNew->setPhone($post['phone']);
        $exNew->setAddTime(nowTime());
        $exNew->setStatus($post['status']);
        $exNew->setMemo($post['memo']);
        $exNew->setTgId($tgId);
        $exNew->setUserId($userId);
        $exDM->save($exNew)->flush();

        return $this->ajaxReturn(array("status" => "y", "info" => "编辑成功", "url" => url("mobileConsoles_user_company", array("active" => "WBLXR"))));
    }

    public function deleteWBLXR($id) {
        $exDM = ExternalRelationsDModel::getInstance();

        $exEN = $exDM->find($id);
        if (!$exEN) {
            return $this->error("查询失败", url("mobileConsoles_user_company", array("active" => "WBLXR")));
        }

        $exDM->remove($exEN)->flush();

        return $this->success("删除成功", url("mobileConsoles_user_company", array("active" => "WBLXR")));
    }

}
