<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/22
 * Time: 11:36
 */

namespace Consoles\Controller;


use Admin\DModel\CompanyAuthDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\ExternalRelationsDModel;
use Admin\DModel\IndustryDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\RbacRoleDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\SmsDModel;
use Admin\DModel\SettingsDModel;
use Admin\Entity\Company;
use Admin\Entity\CompanyAuth;
use Admin\Entity\CompanyMember;
use Admin\Entity\User;
use Admin\Service\CompanyTemplate;

class CompanyController extends CommonController {

    public function lists() {
        $companyAuthDM = CompanyAuthDModel::getInstance();
        $industryDM = IndustryDModel::getInstance();
        $companyMemberDM = CompanyMemberDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;
        $companyMemberDM->updateComperny($this->getUser("id"));
        $sort = array();

//        $lists = $companyMemberDM->name("cm")->select("cm.sid as cm_sid,u.fullName as fullName,u.phone as phone,c")
//            ->leftJoin("Company", "c", "cm.sid=c.id")
//            ->leftJoin("User", "u", "u.id = cm.userId")
//            ->innerJoin("Staff", "s", "s.sid = cm.sid and s.status in (1,2)")
//            ->where("cm.userId=" . $userId)
//            ->groupBy("c.id")
//            ->getArray(true);

        $lists = $companyDM->name("c")->select("cm.sid as cm_sid,u.fullName as fullName,u.phone as phone,c,s")
            ->leftJoin("CompanyMember", "cm", "cm.sid=c.id")
            ->leftJoin("User", "u", "u.id = cm.userId")
            ->leftJoin("Staff", "s", "s.sid = cm.sid and s.status in (1,2)")
            ->where("cm.userId=" . $userId . " and s.userId=" . $userId)
            ->groupBy("c.id")
            ->getArray(true);

        foreach ($lists as $key => &$item) {
            $companyAuth = $companyAuthDM->name("a")->select("a")->where("a.sid=" . $item['c_id'])->getOneArray();
            $item['authStatusMemo'] = $companyAuth['statusMemo'];
            $item['a_id'] = $companyAuth['id'];
            $industry = $industryDM->name("i")->select("i")->where("i.id=" . $item['c_industry'])->getOneArray();
            $item['industryMemo'] = $industry['names'];
            $item['superId'] = $companyDM->name('c')->where("c.id = {$item['cm_sid']} and (c.superid = {$userId} or REGEXP(c.subSuperid, '(^|\,)({$userId})(\,|$)') = 1)")->getOneArray(false, false)['id'] ?: 0;
            $item['scalesMemo'] = $companyDM->getScales($item['c_scales']);
            if ($sid == $item['c_id']) {
                $sort[] = 1;
                $item['sort'] = 1;
            } else {
                $sort[] = 0;
                $item['sort'] = 0;
            }
        }
        array_multisort($sort, SORT_DESC, SORT_STRING, $lists);//排序
        $this->assign("lists", $lists);
        $this->assign("active", "toggle");
        $this->assign("topMenuActive", "company");
        return $this->display();
    }

    public function authentication() {
        $this->assign("active", "authentication");

        $authDM = CompanyAuthDModel::getInstance();

        $userId = $this->getUser("id") ?: 0;
        $lists = $authDM->name("a")->select("a,i")
            ->leftJoin("Industry", "i", "i.id=a.industry")
            ->where("a.userId=$userId")
            ->order("a.status", "ASC")->getArray(true);

        $auth = $authDM->findOneBy(array("status" => 0));

        $this->assign("lists", $lists);

        $this->assign("canAdd", $auth ? false : true);

        return $this->display();
    }


    public function addTeam() {
        $industryDM = IndustryDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $authDM = CompanyAuthDModel::getInstance();
        $temDM = new CompanyTemplate();

        if (Q()->isGet()) {
            $list = $temDM->getLists();
            $this->assign("source", $companyDM->memos["source"]);
            $this->assign("industry", $industryDM->getOptions(0));
            $this->assign("scales", $companyDM->memos["scales"]);
            $this->assign("temLists", $list);
            return $this->display();
        }

        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();
        if (in_array("", $post)) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "不能有空"));
        }

        if ($post['industry'] == 0) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择行业"));
        }

        if ($post['scales'] == -1) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择企业规模"));
        }

        if ($post['source'] == 0) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择通过何种途径了解到我们"));
        }

        $auth = $authDM->findOneBy(array("names" => $post["names"]));
        if ($auth) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "企业名称已存在"));
        }

        $com = $companyDM->findOneBy(array("names" => $post["names"]));
        if ($com) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "企业名称已存在"));
        }

        $comData = $authData = array();
        //企业/团队
        $comData['names'] = $post['names'];
        $comData['superid'] = $this->getUser("id");
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
        $comData['userCount'] = 10; //免费版本最多可添加10个员工
        $comData['usingCount'] = 1;
        $comData['apps'] = "team";//预装应用：任务协作
//        $comData['expireTime'] = date("Y-m-d H:i:s", strtotime("+30000day"));//十人以内永久免费使用workless
        $comData['expireTime'] = date("Y-m-d H:i:s", strtotime("+10day"));//十人以内永久免费使用workless
        $companyDM->create($comData, $companyEN = $companyDM->newEntity());
        if (!$companyDM->check($comData, $companyEN)) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => $companyDM->getError()));
        }
        $companyDM->add($companyEN)->flush();
        $temDM->import($companyEN->getId(), $post['template']);//企业模板
        //企业/团队认证详情
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
            DM()->getManager()->rollback();
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
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "成功创建团队/企业", "url" => url("consoles_index_load_menu", "menu=company")));

    }

    /**
     * 添加下一个企业/团队
     * @return \phpex\Foundation\Response
     */
    public function addTeamSideForm() {
        $industryDM = IndustryDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $authDM = CompanyAuthDModel::getInstance();
        $temDM = new CompanyTemplate();
        $companyMemberDM = new \Admin\DModel\CompanyMemberDModel();

        if (Q()->isGet()) {
            $list = $temDM->getLists();
            $this->assign("source", $companyDM->memos["source"]);
            $this->assign("industry", $industryDM->getOptions(0));
            $this->assign("scales", $companyDM->memos["scales"]);
            $this->assign("temLists", $list);
            return $this->display();
        }

        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();
        if (in_array("", $post)) {
            DM()->getManager()->rollback();
            return $this->error("不能有空");
        }

        if ($post['industry'] == 0) {
            DM()->getManager()->rollback();
            return $this->error("请选择行业");
        }

        if ($post['scales'] == -1) {
            DM()->getManager()->rollback();
            return $this->error("请选择企业规模");
        }

        if ($post['source'] == 0) {
            DM()->getManager()->rollback();
            return $this->error("请选择通过何种途径了解到我们");
        }

        $auth = $authDM->findOneBy(array("names" => $post["names"]));
        if ($auth) {
            DM()->getManager()->rollback();
            return $this->error("企业名称已存在");
        }

        $com = $companyDM->findOneBy(array("names" => $post["names"]));
        if ($com) {
            DM()->getManager()->rollback();
            return $this->error("企业名称已存在");
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
        $comData['userCount'] = 10; //免费版本最多可添加10个员工
        $comData['usingCount'] = 1;
        $comData['apps'] = "team";//预装应用：任务协作
//        $comData['expireTime'] = date("Y-m-d H:i:s", strtotime("+30000day"));//十人以内永久免费使用workless
        $comData['expireTime'] = date("Y-m-d H:i:s", strtotime("+10day"));//十人以内永久免费使用workless

        $companyDM->create($comData, $companyEN = $companyDM->newEntity());
        if (!$companyDM->check($comData, $companyEN)) {
            DM()->getManager()->rollback();
            return $this->error($companyDM->getError());
        }
        $companyDM->add($companyEN)->flush();
        $temDM->import($companyEN->getId(), $post['template']);//企业模板

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
            DM()->getManager()->rollback();
            return $this->error($authDM->getError());
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
        $stationEN->setNames('员工');
        $stationEN->setNum(0);
        $stationEN->setStatus(1);
        $stationDM->add($stationEN)->flush($stationEN);

        $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN->getId()}' and cm.userId = '{$this->getUser("id")}'")->count() ?: 0;
        if ($companyEN && $count <= 0) {//创建人添加自己进入该团队
            $companyMemberEN = $companyMemberDM->newEntity();
            $cMob['sid'] = $companyEN->getId();
            $cMob['userId'] = $this->getUser("id");
            $cMob['status'] = 1;
            $cMob['addTime'] = nowTime();
            $cMob['types'] = 1;
            $companyMemberDM->create($cMob, $companyMemberEN);
            $companyMemberDM->add($companyMemberEN)->flush();
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
        DM()->getManager()->commit();
        return $this->success("成功创建团队/企业");


    }

    public function modify($id) {
        $authDM = CompanyAuthDModel::getInstance();
        $comDM = CompanyDModel::getInstance();
        $temDM = new CompanyTemplate();

        /** @var CompanyAuth $auth */

        $auth = $authDM->findOneBy(array("sid" => $id));

        if (!$auth) return $this->error("认证信息不存在", url("consoles_lists", array("con" => "Company")));

        $com = $comDM->find($auth->getSid());

        if (!$com) {
            return $this->error("公司信息不存在", url("consoles_lists", array("con" => "Company")));
        }

        if (Q()->isGet()) {
            $industryDM = IndustryDModel::getInstance();
            $companyDM = CompanyDModel::getInstance();

            $curTem = $temDM->getLists();
            $this->assign("industry", $industryDM->getOptions($auth->getIndustry()));
            $this->assign("source", $companyDM->memos["source"]);
            $this->assign("scales", $companyDM->memos["scales"]);
            $this->assign("curTem", $curTem);

            $this->assign("auth", $auth);
            $this->assign("com", $com);
            return $this->display();
        }

        $post = Q()->post->all();

        if ($auth->getStatus() == 1) return $this->error("您的企业已经通过认证，不能更改信息", url("consoles_lists", array("con" => "Company")));

        $authDM = CompanyAuthDModel::getInstance();

        $oldAuth = $authDM->findOneBy(array("names" => $post["names"]));
        $userId = $this->getUser("id") ?: 0;


        $post["licenseRegisterDate"] = $post["licenseRegisterDate"] ? new \DateTime($post["licenseRegisterDate"]) : null;
        $post["licenseRegisterExpiry"] = $post["licenseRegisterExpiry"] ? new \DateTime($post["licenseRegisterExpiry"]) : null;
        $post['status'] = 0;

//        dump($auth);exit;
        $comEN = $comDM->find($auth->getSid());
        if (!$comEN) {
            return $this->error("公司信息不存在", url("consoles_lists", array("con" => "Company")));
        }
        $comData = array();
        $comData['names'] = $post['names'];
        $comData['shortNames'] = $post['shortNames'];
        $comData['industry'] = $post['industry'];
        $comData['source'] = $post['source'];
        $comData['keywords'] = $post['keywords'];
        $comData['scales'] = $post['scales'];
        $comData['legal'] = $post['legal'];
        $comData['address'] = $post['address'];
        $comData['province'] = $post['province'];
        $comData['city'] = $post['city'];
        $comData['area'] = $post['area'];
        $comData['bonus'] = $post['bonus'];

        $comDM->create($comData, $comEN);
        $comDM->save($comEN)->flush();

        unset($post['shortNames']);
        unset($post['keywords']);
        unset($post['source']);
        unset($post['bonus']);

        $authDM->create($post, $auth);

        $auth->setUserId($userId);

        $auth->setStatus(0);

        $authDM->add($auth)->flush($auth);

        return $this->success("信息已修改");

    }

    public function inviteMe() {
        $this->assign("active", "inviteMe");

        $userId = $this->getUser("id");
        $phone = $this->getUser("phone");
        $memberDM = CompanyMemberDModel::getInstance();
        $lists = $memberDM->name("m")
            ->select("m.id as m_id,c.names as c_names,c.shortNames as c_shortNames,c.levels as c_levels,c.legal as c_legal,c.status as c_status,u.phone as u_phone,u.userName as u_userName")
            ->where("(m.userId=$userId or m.phone=$phone) and m.status=0")
            ->leftJoin("Company", "c", "c.id= m.sid")
            ->leftJoin("User", "u", "u.id=c.superid")
            ->data_sort()
            ->getArray(true);

        $this->assign("lists", $lists);

        return $this->display();
    }

    public function inviteAgree($id) {
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


        $comMebEN = $memberDM->toArray($member);

        $staffDM = StaffDModel::getInstance();

        $inviteInfo = json_decode($comMebEN["inviteInfo"], true);

        $stations = $inviteInfo && $inviteInfo["stations"] ? $inviteInfo["stations"] : array();

        if (!$stations) {
            $staffEN = $staffDM->name("s")
                ->where("s.sid={$comMebEN['sid']} and s.userId ={$this->getUser('id')} and s.department=0 and s.station = 0")
                ->setMax(1)->getOneObject();

            if (!$staffEN) $staffEN = $staffDM->newEntity();
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
            $staffEN->setDepartment(0);
            $staffEN->setBonus(0);
            $staffEN->setSnackNum(0);
            $staffDM->add($staffEN)->flush($staffEN);
        } else {
            foreach ($stations as $station) {
                $departmentId = $station[0] ?: 0;
                $stationId = $station[1] ?: 0;
                $staffEN = $staffDM->name("s")
                    ->where("s.sid={$comMebEN['sid']} and s.userId ={$this->getUser('id')} and s.department={$departmentId} and s.station = {$stationId}")
                    ->setMax(1)->getOneObject();
                if (!$staffEN) $staffEN = $staffDM->newEntity();
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
                $staffEN->setStation($stationId);
                $staffEN->setQq($this->getUser('qq'));
                $staffEN->setWx($this->getUser('qq'));
                $staffEN->setEmail($this->getUser('email'));
                $staffEN->setDepartment($departmentId);
                $staffEN->setBonus(0);
                $staffEN->setSnackNum(0);
                $staffDM->add($staffEN)->flush($staffEN);
            }
        }


        // $staffDM->addStaff($member->getSid(), $userId);

        $member->setIntoTime(nowTime());
        $member->setStatus(1);
        $memberDM->save($member)->flush();
        return $this->success("加入企业成功", url("consoles_company_inviteMe"));

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
        return $this->success("拒绝成功", url("consoles_company_inviteMe"));
    }


    public function toggle($id) {
        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */

        $company = $companyDM->find($id);

        if (!$company) return $this->error("企业不存在", url("consoles_index_dashboard"));

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
                return $this->error("您还没加入该企业", url("consoles_index_dashboard"));
            }
        }

        $user->setSid($company->getId());

        $user->setRoleName($roleName);

        UserDModel::getInstance()->save($user)->flush($user);

        RbacAccessDModel::accredit($this->access, "Consoles", $user->getSid(), $user, $user->getRoleName());

        return $this->success("企业切换成功，页面将重新加载，请稍候", url("consoles_lists", array("con" => "Company")), 3, "Public:successReload");
    }

    public function memberLists() {
        $companyDM = CompanyDModel::getInstance();
        $sid = $this->getUser("sid") ?: 0;
        $companyEN = $companyDM->find($sid);
        $companyUrl = url("~consoles_login_login", array("recEN" => $this->getUser("phone"), "company" => $companyEN->getCodeNo()));
        $this->assign("companyUrl", $companyUrl);

        if (Q()->getMethod() == "GET") {

            $this->assign("active", "memberLists");
            $this->assign("isSuper", $this->isSuper());

            $company = $companyDM->find($sid);

            $this->assign("codeNo", $company ? $company->getCodeNo() : "");

            $memberDM = CompanyMemberDModel::getInstance();

            $lists = $memberDM->name("m")->where("m.status")->select("m,u")
                ->leftJoin("User", "u", "u.id=m.userId")
                ->where("m.sid=$sid")
                ->setPage()
                ->getArray(true);

            $this->assign("lists", $lists);

            return $this->display();
        }
        $post = Q()->post->all();

        if (!$post['vcode']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请输入图像验证码"));
        }
        if (!$post['phone']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请输入手机号"));
        }
        if ($this->getUser("phone") == $post['phone']) {
            return $this->ajaxReturn(array("status" => 'n', "info" => "不能发给自己"));
        }

        if ($post['vcode'] != Q()->getSession()->get("myuser_verify")) {
            return $this->ajaxReturn(array("status" => 'n', "info" => "图像验证码不正确"));
        }

        $sid = $this->getUser('sid');
        $companyDM = CompanyDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $companyEN = $companyDM->find($sid);

        $web = url("~consoles_login_login", array("company" => $companyEN->getCodeNo(), "user" => $post['phone']));

        $ret = $this->sendVerify($post['phone'], $web, $companyEN->getCodeNo());
        if ($ret != '发送成功') {
            return $this->ajaxReturn(array("status" => 'n', "info" => $ret));
        }

        $companyMemberDM = CompanyMemberDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->name('c')->where("c.id = '{$sid}' and c.status = 1")->getOneArray();
        $recEN = $userDM->name("u")->where("u.phone = {$this->getUser('phone')} and u.status = 1")->getOneArray();

        $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN['id']}' and cm.userId = '{$this->getUser('id')}'")->count() ?: 0;


        if ($companyEN && $count <= 0) {
            $companyMemberEN = $companyMemberDM->newEntity();
            $companyMemberEN->setSid($companyEN['id']);
            $companyMemberEN->setUserId($this->getUser('id'));
            $companyMemberEN->setRecId($recEN['id']);
            $companyMemberEN->setStatus(0);
            $companyMemberEN->setAddTime(nowTime());
            $companyMemberEN->setTypes(1);
            $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
        }

        return $this->ajaxReturn(array("status" => 'y', "info" => "短信发送成功"));
    }

    public function sendVerify($phone, $web, $codeNo = 000000) {
        $smsDM = SmsDModel::getInstance();
        $template = SmsDModel::SEND_RECOMMEND;
        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));
        $ret = $smsDM->setting($settings)->invitingSend($template, $phone, 0, array("web" => $web, "codeNo" => $codeNo));
        if ($ret) {
            return "已经发送，请等待";
        }
        return (string)$smsDM->getError();
    }

    public function inviteAdd() {
        if (Q()->isGet()) {

            return $this->display();
        }

        $post = Q()->post->all();

        $sid = $this->getUser("sid") ?: 0;

        if (!$sid) return $this->error("企业信息获取失败");

        $memberDM = CompanyMemberDModel::getInstance();

        $member = $memberDM->findOneBy(array("sid" => $sid, "userId" => $post["userId"] ?: 0));

        if ($member) return $this->error("同事已经是企业的一员或邀请已发送");

        $userDM = UserDModel::getInstance();

        $user = $userDM->find($post["userId"] ?: 0);

        if (!$user) return $this->error("用户不存在");

        $member = $memberDM->newEntity();

        $member->setSid($sid);
        $member->setUserId($post["userId"]);
        $member->setAddTime(nowTime());
        $member->setTypes(1);
        $member->setStatus(0);
        $member->setSurveyAcorn(0);
        $memberDM->add($member)->flush();
        return $this->success("邀请已发送,请等待同事的回应");
    }

    public function inviteDel($id) {

        $sid = $this->getUser("sid") ?: 0;

        if (!$sid) return $this->error("企业信息获取失败");

        $memberDM = CompanyMemberDModel::getInstance();

        /** @var CompanyMember $member */

        $member = $memberDM->findOneBy(array("sid" => $sid, "id" => $id ?: 0));

        if (!$member) return $this->error("记录不存在");

        if ($member->getStatus() != 0) return $this->error("同事已经是企业的一员！");

        $memberDM->remove($member)->flush($member);
        return $this->success("删除成功", url("consoles_company_memberLists"));
    }


    public function findUsers() {
        $maxRow = 5;


        $companyDM = CompanyDModel::getInstance();


        /** @var Company $company */

        $company = $companyDM->find($this->sid);

        $userDM = UserDModel::getInstance();
        $keywords = Q()->post->get("keywords");

        $ids = array($company ? $company->getSuperid() : 0);
        $lists = $userDM->name("u")
            ->where("u.phone like :keywords and u.id not in(:ids)")->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids))
            ->setMax($maxRow)->getArray();
        $users = array();
        foreach ($lists as $user) {
            $ids[] = $user["id"];
            $maxRow--;
            $users[] = array(
                $user["id"],
                sprintf("%s:%s", $user["userName"], $user['fullName']),
                sprintf("%s:%s:%s", highlight($user["phone"], $keywords, 1), $user["userName"], $user['fullName']),
            );
        }
        if ($maxRow > 0) {
            $lists = $userDM->name("u")
                ->where("u.userName like :keywords and u.id not in(:ids)")
                ->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids))
                ->setMax($maxRow)->getArray();
            foreach ($lists as $user) {
                $ids[] = $user["id"];
                $maxRow--;
                $users[] = array(
                    $user["id"],
                    sprintf("%s:%s", $user["userName"], $user['fullName']),
                    sprintf("%s:%s:%s", $user["phone"], highlight($user["userName"], $keywords, 1), $user['fullName']),
                );
            }
        }
        if ($maxRow > 0) {
            $lists = $userDM->name("u")
                ->where("u.fullName like :keywords and u.id not in(:ids)")
                ->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids))
                ->setMax($maxRow)->getArray();
            foreach ($lists as $user) {
                $ids[] = $user["id"];
                $maxRow--;
                $users[] = array(
                    $user["id"],
                    sprintf("%s:%s", $user["userName"], $user['fullName']),
                    sprintf("%s:%s:%s", $user["phone"], $user["userName"], highlight($user["fullName"], $keywords, 1)),
                );
            }
        }
        return $this->ajaxReturn($users);
    }


    public function superAdd() {
        $sid = $this->getUser("sid") ?: 0;
        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->find($sid);

        if (!$companyEN) return $this->error("企业信息获取失败！");

        if (Q()->isGet()) {
            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workerList($this->sid, "executors", array(), 20);
            $this->assign("executors", $executors);
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post['executors']) {
            return $this->error("人员不能为空！");
        }
        if (!$post['maxScore']) {
            $post['maxScore'] = 0;
//            return $this->error("设置最高分不能为空！");
        }
        if (!preg_match("/^([0-9]|[1-9][0-9]*)$/", $post['maxScore'])) return $this->error("设置的分数必须为整数");

        $executors = is_array($post["executors"]) ? $post["executors"] : explode(",", $post["executors"]);

        $superid = $companyEN->getSuperid();

        $executors = array_filter($executors, function ($val) use ($superid) {
            return $val && $val != $superid;
        });
        $subUserIds = explode(",", $companyEN->getSubSuperid());

        $executors = array_merge($subUserIds, $executors);

        $executors = array_unique($executors);

        $executorsStr = preg_replace("/\,{2,}/", ",", join(",", $executors));

        $maxScore = json_decode($companyEN->getMaxScore(), true);
        foreach ($post['executors'] as $v) {
            $maxScore[$v] = array((int)$post['maxScore'], $post['limitAcorn']);
        }

        $maxScore = json_encode($maxScore);

        $companyEN->setMaxScore($maxScore);
        $companyEN->setSubSuperid(trim($executorsStr, ","));
        $companyDM->save($companyEN)->flush($companyEN);

        return $this->success("添加成功", url('consoles_company_supers'));
    }

    public function supers() {
        $this->assign("formUrl", url("~consoles_company_supers"));

        $companyDM = CompanyDModel::getInstance();
        $sid = $this->getUser("sid") ?: 0;
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

        $company["maxScore"] = json_decode($company["maxScore"], true);

        $this->assign("company", $company);

        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();

        $arr = array();
        foreach ($post['maxScore'] as $k => $v) {
            $on = isset($post['limitAcorn'][$k]) ? 1 : 0;
            $arr[$k] = array(trim($v), $on);
        }

        $companyEN = $companyDM->find($sid);
        $companyEN->setMaxScore(json_encode($arr));
        $companyDM->save($companyEN)->flush();

        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功！"));
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

        $maxScore = json_decode($companyEN->getMaxScore(), true);
        unset($maxScore[$id]);
        $maxScore = json_encode($maxScore);

        $companyEN->setMaxScore($maxScore);
        $companyEN->setSubSuperid(join(",", $newsubUserIds));
        $companyDM->save($companyEN)->flush($companyEN);
        return $this->success("删除成功", url('consoles_company_supers'));
    }


    /**
     * 外部联系人
     */

    public function listsWBLXR() {
        $this->assign("types", "waibu");

        $exDM = ExternalRelationsDModel::getInstance();

        $lists = $exDM->name("e")->select("e,u")
            ->leftJoin("User", "u", "u.id = e.userId")
            ->where("e.sid = {$this->getUser('sid')}")
            ->setPage()
            ->getArray(true);

        $this->assign("lists", $lists);

        return $this->display();
    }

    public function addWBLXR() {
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
            return $this->error("手机号不能为空");
        } else {
            $exGA = $exDM->name("ex")->where("ex.phone = '{$post['phone']}'")->getArray();
            if ($exGA) {
                return $this->error("该手机号已添加");
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

        return $this->success("添加成功");
    }

    public function modifyWBLXR($id) {
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
            return $this->error("手机号不能为空");
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
            return $this->error("数据查询失败");
        }

        $exNew->setSid($this->sid);
        $exNew->setPhone($post['phone']);
        $exNew->setAddTime(nowTime());
        $exNew->setStatus($post['status']);
        $exNew->setMemo($post['memo']);
        $exNew->setTgId($tgId);
        $exNew->setUserId($userId);
        $exDM->save($exNew)->flush();

        return $this->success("编辑成功");
    }

    public function deleteWBLXR($id) {
        $exDM = ExternalRelationsDModel::getInstance();

        $exEN = $exDM->find($id);
        if (!$exEN) {
            return $this->error("查询失败", url("consoles_company_listsWBLXR"));
        }

        $exDM->remove($exEN)->flush();

        return $this->success("删除成功", url("consoles_company_listsWBLXR"));
    }


}
