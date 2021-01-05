<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/8/17
 * Time: 11:30
 */

namespace MobileConsoles\Controller;


use Admin\DModel\AcornAuditDModel;
use Admin\DModel\AcornDModel;
use Admin\DModel\AnythingDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\ExternalRelationsDModel;
use Admin\DModel\SettingsDModel;
use Admin\DModel\SmsDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffGroupDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\WorkTypeDModel;

class UserController extends CommonController {

    public function me() {
        $userDM = UserDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $acornDM = AcornDModel::getInstance();
        $departmentDM = DepartmentDModel::getInstance();

        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $user = $userDM->name("u")->where("u.id=$userId")->setMax(1)->getOneArray();

        $staff = $staffDM->getStaff($user, $sid);

        $company = $companyDM->name("c")->where("c.id=$sid")->setMax(1)->getOneArray();

        $dateW = date("w") - 1;
        $times1 = date('Y-m-d 00:00:00', strtotime('- ' . 0 . ' Day'));
        $times2 = date('Y-m-d 00:00:00', strtotime('- ' . 1 . ' Day'));

        $acornALL = $acornDM->name('a')->where("a.sid = {$this->getUser('sid')} and a.userId = {$this->getUser('id')}")->sum("a.acorn") ?: 0;
        $acornYES = $acornDM->name('a')->where("a.sid = {$this->getUser('sid')} and a.userId = {$this->getUser('id')} and a.addTime < '{$times1}' and a.addTime >= '{$times2}'")->sum("a.acorn") ?: 0;

        if (preg_match("/^[a-zA-Z\s]+$/", $user['fullName'])) {
            $fullName = strtoupper(substr($user['fullName'], 0, 2));
        } else {
            $fullName = mb_substr($user['fullName'], -2, 2, "utf-8");
        }

        $staff['department'] = $staff['department'] ? $staff['department'] : 0;
        $staff['sid'] = $staff['sid'] ? $staff['sid'] : 0;
        $departmentEN = $departmentDM->name("d")->where("d.sid={$staff['sid']} and d.id={$staff['department']}")->getOneArray();

        $isManager = $company['superid'] == $user['id'] ? "(管理员)" : "";

        //计算今天的备忘总条数
        $anythingDM = AnythingDModel::getInstance();
        $todayStart = date("Y-m-d 00:00:01");
        $todayEnd = date("Y-m-d 23:59:59");
        $todayCount = $anythingDM->name('a1')
            ->where("a1.certainTime>='{$todayStart}' and a1.certainTime<='{$todayEnd}' and a1.status<>1 and a1.userId={$this->getUser('id')}")
            ->count();
        $todayCount = $todayCount ?: 0;

        $this->assign("prefix", $this->cdnThumbBase);
        $this->assign("isManager", $isManager);
        $this->assign("department", $departmentEN['names']);
        $this->assign("fullName", $fullName);
        $this->assign("staff", $staff);
        $this->assign("user", $user);
        $this->assign("company", $company);
        $this->assign("acornALL", $acornALL);
        $this->assign("acornYES", $acornYES);
        $this->assign("photo", $staff["photo"] ?: $user["photo"]);
        $this->assign("todayCount", $todayCount);

        return $this->display();
    }


    public function setting() {
        $userDM = UserDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();

        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $user = $userDM->name("u")->where("u.id=$userId")->setMax(1)->getOneArray();

        $staff = $staffDM->getStaff($user, $sid);

        $company = $companyDM->name("c")->where("c.id=$sid")->setMax(1)->getOneArray();

        $this->assign("staff", $staff);
        $this->assign("user", $user);
        $this->assign("company", $company);
        $this->assign("photo", $staff["photo"] ?: $user["photo"]);
        return $this->display();
    }

    /**
     * 用户基本信息修改
     */
    public function userInfo() {
//        $get = Q()->get->all();
//        if ($get['Tips'] == 'new') {
//            $this->assign("tips", "请您先完善您的个人信息，以便他人更好的找到您！");
//        }
//        return $this->display();

        $this->assign("prefix", $this->cdnThumbBase);
        if (Q()->getMethod() == 'GET') {

            if ($this->sid == 0) {
                $url = url('mobileConsoles_user_userInfo', array("different" => "yes"));
                $this->assign("url", $url);
            }

            $this->flushUser();
            if ($this->getUser("birthday")) {
                $birthday = totime($this->getUser("birthday"), "Y-m-d");
            } else {
                $birthday = date("Y-m-d", time());
            }
            if ($this->getUser('sex') == 1) {
                $sex = "男";
            } else if ($this->getUser('sex') == 2) {
                $sex = "女";
            } else {
                $sex = "请选择";
            }
            $area = $this->getUser("area") ? $this->getUser("area") : "北京 东城区";
            $fullName = $this->getUser("fullName");
            if (preg_match("/^[a-zA-Z\s]+$/", $fullName)) {
                $fullName = strtoupper(substr($fullName, 0, 2));
            } else {
                $fullName = mb_substr($fullName, -2, 2, "utf-8");
            }
            $this->assign([
                "fullName" => $fullName,
                "photo" => $this->getUser("photo"),
                "nickName" => $this->getUser("nickName"),
                "qq" => $this->getUser("qq"),
                "email" => $this->getUser("email"),
                "sex" => $sex,
                "birthday" => $birthday,
                "area" => $area
            ]);
            return $this->display();
        }
        $post = Q()->post->all();
        if ($post['qq']) {
            if (!preg_match("/^[1-9][0-9]{4,11}$/", $post['qq'])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "QQ格式错误"));
            }
        }
        if ($post['email']) {
            if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $post['email'])) {
                return $this->ajaxReturn(array("status" => "n", "info" => "邮箱格式错误"));
            }
        }
        if ($post['sex'] == "男") {
            $post['sex'] = 1;
        } else if ($post['sex'] == "女") {
            $post['sex'] = 2;
        } else {
            $post['sex'] = 0;
        }
        $post['birthday'] = date("Y-m-d H:i:s", strtotime($post['birthday']));
        $userDM = new UserDModel();
        $userEN = $userDM->find($this->getUser("id"));
        $userDM->create($post, $userEN);
        $userDM->save($userEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "url" => url("mobileConsoles_user_me")));
    }

    /**
     * 修改密码
     */
    public function modifyPWD() {
        if (Q()->isGet()) {
            if ($this->sid == 0) {
                $url = url("mobileConsoles_user_modifyPWD", array("different" => "yes"));
                $this->assign("url", $url);
            }
            return $this->display();
        }
        $post = Q()->post->all();

        if ($post['pwd'] != $post['pwd2']) {
            return $this->error("新密码不一致");
        }

        $userDM = new UserDModel();
        $userEN = $userDM->find($this->getUser("id"));

        if (md5($post['oldpwd']) != $userEN->getPwd()) {
            return $this->error("旧密码错误");
        }

        $userEN->setPwd(md5($post['pwd']));
        $userDM->save($userEN)->flush($userEN);
        return $this->success(url("mobileConsoles_user_setting"));
    }

    /**
     * 个人学历
     */
    public function education() {
        $arr = array();

        $arr['1']['names'] = "小学";
        $arr['1']['id'] = "1";
        $arr['2']['names'] = "初中";
        $arr['2']['id'] = "2";
        $arr['3']['names'] = "高中";
        $arr['3']['id'] = "3";
        $arr['4']['names'] = "专科";
        $arr['4']['id'] = "4";
        $arr['5']['names'] = "大学";
        $arr['5']['id'] = "5";

        $this->assign("options", $arr);

        return $this->display();
    }

    /**
     * 工作经验
     */
    public function workExp() {
        return $this->display();
    }


    public function company() {
        $userDM = UserDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $groupDM = StaffGroupDModel::getInstance();
        $worktypeDM = WorkTypeDModel::getInstance();
        $staffStationDM = StaffStationDModel::getInstance();
        $staffDM = StaffDModel::getInstance();

        $get = Q()->get->all();
        if ($get['active']) {
            $this->assign("active", $get['active']);
        } else {
            $this->assign("active", "BUMEN");
        }

        //用户信息
        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $user = $userDM->name("u")->where("u.id=$userId")->setMax(1)->getOneArray();
        //公司
        $company = $companyDM->name("c")->where("c.id={$sid}")->setMax(1)->getOneArray();

        //部门
        $department = $this->department($sid, 0, 0);
        foreach ($department as $k => $v) {
            $department[$k]['count'] = $staffDM->name('s')
                ->where("s.department={$v['id']} and s.sid={$sid} and s.status=1")
                ->count();
        }

        //小组
        $group = $groupDM->name('g')->where("g.status = 1 and g.sid={$sid}")->getArray();
        foreach ($group as &$v) {
            $v['leader'] = $userDM->name("u")->where("u.id = {$v['leader']}")->getOneArray();
        }

        //工种
//        $worktype = $worktypeDM->name("wt")->where("wt.sid={$sid}")->getArray();

        //职位
        $staffStation = $staffStationDM->name("ss")->where("ss.sid={$sid}")->getArray();
        //外部联系人
        $exDM = ExternalRelationsDModel::getInstance();
        $exLists = $exDM->name("e")->select("e,u")
            ->leftJoin("User", "u", "u.id = e.userId")
            ->where("e.sid = {$this->getUser('sid')}")
            ->getArray(true);

        //未加入到任何部门的此公司的同事
        $staff = $staffDM->name("s")->where("s.sid={$sid} and (s.department=0 or s.department is null)")->getArray();

        $companyUser = $userDM->find($company["superid"] ?: 0);

        $this->assign("user", $user);
        $this->assign("companyUser", $companyUser);
        $this->assign("company", $company);
        $this->assign("department", $department);
        $this->assign("exLists", $exLists);//外部联系人
        $this->assign("Dajax", url("~mobileConsoles_user_DAjaxLists"));
        $this->assign("Gajax", url("~mobileConsoles_user_GAjaxLists"));
        $this->assign("UserInfoUrl", url("~mobileConsoles_user_PersonalCard"));
        $this->assign("group", $group);
//        $this->assign("worktype", $worktype);
        $this->assign("staffStation", $staffStation);
        $this->assign("staff", $staff);
        $this->assign("isSuper", $this->isSuper());

        return $this->display();
    }

    /**
     * 部门递归
     */
    public function department($sid, $pid, $layers) {
        $departmentDM = DepartmentDModel::getInstance();
        $department = $departmentDM->name("d")->where("d.sid = {$sid} and d.parentid = {$pid} and d.status = 1")->getArray();

        $lists = array();
        foreach ($department as &$v) {
            $prefix = '';
            $Depart = $this->department($sid, $v['id'], $layers + 1);
            $count = count($Depart);

            foreach ($Depart as $kk => &$vv) {
                if ($kk + 1 < $count) {
                    $prefix = '├ ';
                } else {
                    $prefix = '└ ';
                }
                $vv['names'] = $prefix . $vv['names'];
            }

            $lists[] = $v;
            $lists = array_merge($lists, $Depart);
        }

        return $lists;
    }

    /**
     * 部门ajax数据获取
     * 参数：部门ID
     */
    public function DAjaxLists() {
        $get = Q()->get->all();
        $staffDM = StaffDModel::getInstance();

        $lists = $staffDM->name('s')
            ->where("s.department = {$get['did']} and s.sid = {$this->getUser('sid')}")
            ->getArray(true);

        $htmls = '';
        foreach ($lists as &$v) {
            $html = <<<work
               <a class="item-content item-link open-popup" data-popup=".info-popup" onclick="GetUserData({$v['s_id']})" style="border-bottom:1px solid #eee;">
                    <div style="width:95%;">
                        <div class="item-title" style="padding:0.5rem 0;">
                            <div style="width:15%; float:left;">
                                <div class="wztx">{$v['s_fullName']}</div>
                            </div>
                            <div style="width:75%; float:left;">
                                <p style="margin:0;">{$v['s_fullName']}</p>
                                <p style="margin:0;">{$v['s_phone']}</p>
                            </div>
                            <div style="width:10%; float:left; color:#0078e2; line-height: 40px;">
                                详情
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </a>
work;
            $htmls .= $html;
            $v['html'] = $html;
        }

        return $this->ajaxReturn(array("DAjaxLists" => $htmls));
    }

    /**
     * 小组ajax数据获取
     * 参数：小组ID
     */
    public function GAjaxLists() {
        $get = Q()->get->all();
        $staffDM = StaffDModel::getInstance();
        $StaffGroupDModel = StaffGroupDModel::getInstance();

        $GEN = $StaffGroupDModel->find($get['did']);
        if (!$GEN) {
            return $this->ajaxReturn(array("GAjaxLists" => null));
        }

        $memo = $GEN->getMembers();
        $ids = explode(",", $memo);

        $htmls = '';
        foreach ($ids as &$v) {
            $staffEN = $staffDM->name('s')
                ->where("s.userId = {$v} and s.sid = {$this->sid}")
                ->setMax(1)
                ->getArray(true);
            if (!$staffEN) {
                continue;
            }
            $html = <<<work
               <a class="item-content item-link open-popup" data-popup=".info-popup" onclick="GetUserData({$staffEN[0]['s_id']})" style="border-bottom:1px solid #eee;">
                    <div style="width:95%;">
                        <div class="item-title" style="padding:0.5rem 0;">
                            <div style="width:15%; float:left;">
                                <div class="wztx">{$staffEN[0]['s_fullName']}</div>
                            </div>
                            <div style="width:75%; float:left;">
                                <p style="margin:0;">{$staffEN[0]['s_fullName']}</p>
                                <p style="margin:0;">{$staffEN[0]['s_phone']}</p>
                            </div>
                            <div style="width:10%; float:left; color:#0078e2; line-height: 40px;">
                                详情
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </a>
work;
            $htmls .= $html;
            $v['html'] = $html;
        }

        return $this->ajaxReturn(array("GAjaxLists" => $htmls));
    }


    public function PersonalCard() {
        $get = Q()->get->all();

        if (!$get['id']) {
            return $this->ajaxReturn(array("info" => "获取信息失败", "status" => "n"));
        }

        $staffDM = StaffDModel::getInstance();
        $staffEN = $staffDM->name('s')->where("s.id = {$get['id']}")->getOneArray();
        if (!$staffEN) {
            return $this->ajaxReturn(array("info" => "获取信息失败", "status" => "n"));
        }
//        $departmentDM = DepartmentDModel::getInstance();
//        $departmentEN = $departmentDM->name("d")->where("d.sid={$staffEN['sid']} and d.id={$staffEN['department']}")->getOneArray();

        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->name("c")->where("c.id={$staffEN['sid']}")->getOneArray();

        $isManager = $companyEN['superid'] == $staffEN['userId'] ? "(管理员)" : "";

        $isSuper = $this->isSuper();

        $departmentDM = DepartmentDModel::getInstance();
        $depNames = " ";
        if (!$isSuper) {
            $depLists = $staffDM->name("s")->where("s.userId={$staffEN['userId']} and s.sid={$this->sid} and s.status=1 ")->getArray();
            if ($depLists) {
                foreach ($depLists as $k => $v) {
                    if ($v['department']) {
                        $findDep = $departmentDM->find($v['department']);
                        if ($findDep) {
                            $depNames .= $findDep->getNames() . " ";
                        }
                    }
                }
            }

        }
        if (!$depNames || $depNames === " ") {
            $depNames .= " 暂未分配部门";
        }

        if (empty($staffEN['wx'])) {
            $staffEN['wx'] = "";
        }

        if (empty($staffEN['qq'])) {
            $staffEN['qq'] = "";
        }

        if (empty($staffEN['email'])) {
            $staffEN['email'] = "";
        }


        $headerImg = path('[MobileConsoles]/img/user_header.png');
//        <img src="{path('[MobileConsoles]/img/head.png')}" alt="" class="user-img"> 头像图片
//        <div class="user-info">
//                <div class="headerSZ"></div>
//                <img class="headerImg" src="{$headerImg}">
//                <div class="user-overview">
//                    <div class="user-base">
//                        <div class="user-names">{$staffEN["fullName"]}</div>
//                        <div class="user-acorn">{$departmentEN['names']}</div>
//                    </div>
//                    <div class="user-company">{$staffEN["names"]}<span style="font-size:0.5rem;">$isManager</span></div>
//                </div>
//                <div class="user-head">
//                    <div class="user-img">
//                        <div class="user-img" style="background:#0087e2; text-align:center; line-height:60px; color:white; font-size:18px;">{$staffEN['fullName']}</div>
//                    </div>
//                </div>
//                <div style="clear:both;"></div>
//            </div>
//            <div style="clear:both;"></div>
        $html = "";


        $html .= <<<work
            
    
            <div class="list-block" style="margin: 0; padding: 5px 10px;">
                <ul>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-wode mlrLogo"></i> 姓名：{$staffEN['fullName']}</div>
                        </div>
                    </li>
                    <li>
work;
        if ($isSuper) {
            $html .= <<<work
                        <div class="item-inner" onclick="chooseDepartment({$staffEN['userId']},{$staffEN['sid']})">
                            <div class="item-title"><i class="icon al-icon al-icon-influence mlrLogo"></i>点击选择部门</div>
                        </div>
work;
        } else {
            $html .= <<<work
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-influence mlrLogo"></i>{$depNames}</div>
                        </div>
work;
        }
        $html .= <<<work
                    </li>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-shoujihao mlrLogo"></i> 手机号：{$staffEN['phone']}</div>
                        </div>
                    </li>
                    <li>
                        <div class="item-inner">
                        <div class="item-title"><i class="icon al-icon al-icon-weixin mlrLogo"></i> 微信：{$staffEN['wx']}</div>
                        </div>
                    </li>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-qq mlrLogo"></i> QQ:{$staffEN['qq']}</div>
                        </div>
                    </li>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-youxiang mlrLogo"></i> 邮箱:{$staffEN['email']}</div>
                        </div>
                    </li>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-zhuangtai2 mlrLogo"></i> 状态:{$staffEN['statusMemo']}</div>
                        </div>
                    </li>
                    <li>
                        <div class="item-inner">
                            <div class="item-title"><i class="icon al-icon al-icon-beizhu mlrLogo"></i> 备注:{$staffEN['memo']}</div>
                        </div>
                    </li>
                </ul>
            </div>
work;


        return $this->ajaxReturn(array("custom" => $html));
    }


    public function inviting() {
        $userDM = UserDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->name('s')->where("s.id = {$this->getUser('sid')} and s.status = 1")->getOneArray();
        if (!$companyEN) {
            return $this->error("您还没有加入团体或者企业，不能进行分享！", url("mobileConsoles_user_me"));
        }
        $this->assign("company", $companyEN);
        $this->assign("phone", $this->getUser('phone'));

        if (Q()->isGet()) {
            $pureUrl = url("~mobileConsoles_company_joined", array("company" => $companyEN['codeNo'], "user" => $this->getUser('phone')));
            $url = "您的同事" . $this->getUser("fullName") . "正在邀请您加入团队/企业，点击链接马上加入：" . url("~mobileConsoles_company_joined", array("company" => $companyEN['codeNo'], "user" => $this->getUser('phone')));
            $this->assign([
                "url" => $url,
                "pureUrl" => $pureUrl
            ]);
            return $this->display();
        }
        $post = Q()->post->all();

        if ($this->getUser("phone") == $post['phone']) {
            return $this->ajaxReturn(array("status" => 'n', "info" => "不能发给自己"));
        }

        if ($post['types'] == 1) {
            if ($post['code'] != Q()->getSession()->get("login_verify")) {
                return $this->ajaxReturn(array("status" => 'n', "info" => "图像验证码不正确"));
            }

            $sid = $this->getUser('sid');
            $companyDM = CompanyDModel::getInstance();
            $companyEN = $companyDM->find($sid);
            $web = url("~mobileConsoles_login", array("company" => $companyEN->getCodeNo(), "user" => $post['phone']));

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

        return $this->ajaxReturn(array("status" => 'n', "info" => "短信发送失败"));
    }

    public function sendVerify($phone, $web, $codeNo = 000000) {
        $smsDM = SmsDModel::getInstance();
        $template = SmsDModel::SEND_RECOMMEND;
        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));
        $ret = $smsDM->setting($settings)->invitingSend($template, $phone, 0, array("web" => $web, "codeNo" => $codeNo));
        if ($ret) {
            return "发送成功";
        }
        return (string)$smsDM->getError();
    }


    /**
     * 用户引导页
     */
    public function guide() {
//        dump(123);exit;
        if (Q()->isGet()) return $this->display("User:guide");
        $this->flushUser();
        $userDM = UserDModel::getInstance();
        $comMebDM = CompanyMemberDModel::getInstance();

        $comMebEN = $comMebDM->name("cm")->where("cm.userId={$this->getUser('id')}")->getOneArray();
        if (!$comMebEN) {
            $comMebEN = $comMebDM->name("cm")->where("cm.phone={$this->getUser('phone')}")->getOneArray();
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

        $comMebDM->name("c")->where("c.id = {$comMebEN['id']}")->update(array('c.status' => 1, 'c.userId' => $this->getUser('id')));
        $userDM->name("u")->where("u.id = {$this->getUser('id')}")->update(array('u.sid' => $comMebEN['sid']));

        return $this->ajaxReturn(array("status" => "y", "info" => "成功加入！", "url" => url("mobileConsoles_company_lists")));
//        if ($comMebEN['status'] == 1) {
//            return $this->ajaxReturn(array("status" => "y", "info" => "成功加入！"));
//        } else if ($comMebEN['status'] == 2) {
//            return $this->ajaxReturn(array("status" => "n", "info" => "审核未通过"));
//        } else {
//            return $this->ajaxReturn(array("status" => "n", "info" => "正在审核中..."));
//        }
    }


    public function UserAcorn() {
        $acornDM = AcornDModel::getInstance();
        $lists = $acornDM->name("a")
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("Study", "s", "s.id = a.names")
            ->select("a,u.fullName,s.names as sNames")
            ->where("a.status = 1 and a.sid = {$this->sid} and a.userId = {$this->getUser('id')}")
            ->setMax(12)
            ->order("a.id", "DESC")
            ->getArray(true);
        foreach ($lists as $k => $v) {
            $lists[$k]['addTimes'] = $this->time_tran(totime($v['a_addTime']));
        }
        $this->assign("lists", $lists);

        return $this->display();
    }

    function time_tran($the_time) {
        $now_time = date("Y-m-d H:i:s", time());
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $t = $now_time - $show_time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }

}