<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/27
 * Time: 17:43
 */

namespace Consoles\Controller;


use Admin\DModel\CompanyDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\RbacRoleDModel;
use Admin\DModel\SettingsDModel;
use Admin\DModel\SmsDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffGroupDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\SurveyGroupDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WelfareSettingsDModel;
use Admin\Entity\Company;
use Admin\Entity\Department;
use Admin\Entity\RbacRole;
use Admin\Entity\Staff;
use Admin\Entity\StaffStation;
use Admin\DModel\CompanyMemberDModel;

class StaffController extends CommonController {

    protected $moduleName = "Consoles";

    /**
     * 员工列表
     */
    public function lists($sid) {
        if (!$sid) $sid = $this->getUser("sid") ?: 0;

        $company = CompanyDModel::getInstance()->find($sid);

        $departmentDM = DepartmentDModel::getInstance();
        $department = $departmentDM->name("d")->select("d")->where("d.sid=" . $sid)->getArray();
        $departmentSelect = array("" => "全部");
        foreach ($department as $key => $item) {
            $departmentSelect[$item['id']] = $item['names'];
        }

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addSelect("d.id", "部门", $departmentSelect);
        $search->addKeyword("s.fullName,s.phone", "姓名/手机号");
        $search->bindData(Q()->get->all());
        $search->build($where, $searchForm, $params);
        $where = ($where == null) ? '' : " and " . $where;

        $comMemDM = CompanyMemberDModel::getInstance();

        $lists = $comMemDM->name("c")->select("c,s,d.names as d_department,ss.names as ss_station")
            ->leftJoin("Staff", "s", "c.userId = s.userId and s.sid={$sid}")
            ->leftJoin("StaffStation", "ss", "ss.id = s.station and ss.sid={$sid}")
            ->leftJoin("Department", "d", "d.id = s.department and d.sid={$sid}")
            ->where("c.sid = {$sid} $where")
            ->setParameter($params)
            ->order("c.id", "ASC")
            ->setPage()
            ->data_sort()
            ->getArray(true);

        $this->assign("types", "yuangong");
        $this->assign("lists", $lists);
        $this->assign("sid", $sid);
        $this->assign("company", $company);

        return $this->display();
    }

    /**
     * 员工列表（已关闭）
     * @return mixed
     */
    public function lists_close() {
        $search = $this->search();
        $search->labelType("placeholder");
        $search->addKeyword("s.fullName,s.phone", "姓名/手机号");
        $search->bindData(Q()->get->all());
        $search->build($where, $searchForm, $params);

        $this->assign("active", "StaffLists");
        $this->assign("types", "yuangong");
        $this->assign("isSuper", $this->isSuper());
        $staffDM = StaffDModel::getInstance();

//        if ($where) {
        $where = ($where == null) ? '' : " and " . $where;
        $lists = $staffDM->name("s")->where("s.sid={$this->sid} $where")->setParameter($params)->getArray();
        $listStr = "";
        $stationDM = StaffStationDModel::getInstance();
        $comMemberDM = CompanyMemberDModel::getInstance();
        $userDM = UserDModel::getInstance();
        foreach ($lists as $item) {

            //直线经理
            $comMember = $comMemberDM->name("c")->select("c")->where("c.userId=" . $item['userId'] . " AND c.sid=" . $this->sid . "AND c.status=1")->setMax(1)->getOneArray();
            $leaderName = "";
            if ($comMember) {
                $url1 = url("consoles_mod", array("con" => "MyUser", "id" => $comMember["id"]));
                if ($comMember['leader'] > 0) {
                    $leaderName = "<a  href='{$url1}' data-side-form='900px'>{$userDM->getUserName($comMember['leader'])}</a>";
                } elseif ($comMember['leader'] == -1) {
                    $leaderName = "<a  href='{$url1}' data-side-form='900px'>无</a>";
                } else {
                    $leaderName = "<a  href='{$url1}' data-side-form='900px' style='color: red;'>未设置</a>";
                }
            }

            //职位
            $station = $stationDM->name("s")->select("s")->where("s.id=" . $item['station'] . " AND s.sid=" . $this->sid . "AND s.status=1")->setMax(1)->getOneArray();
            $stationMemo = "-";
            if ($station) {
                $stationMemo = $station['names'];
            }

            //部门
            $departmentDM = DepartmentDModel::getInstance();
            $department = $departmentDM->name("d")->select("d")->where("d.id=" . $item['department'] . " AND d.sid=" . $this->sid . "AND d.status=1")->setMax(1)->getOneArray();
            $departmentMemo = "-";
            if ($station) {
                $departmentMemo = $department['names'];
            }

            $listStr .= "<tr>";
            $listStr .= "<td>{$item["fullName"]}</td>";
            $listStr .= "<td>$departmentMemo</td>";
            $listStr .= "<td>{$item["phone"]}</td>";
            $listStr .= "<td>{$leaderName}</td>";
            $listStr .= "<td>{$item["wx"]}</td>";
            $listStr .= "<td>{$item["qq"]}</td>";
            $listStr .= "<td>{$item["email"]}</td>";
            $listStr .= "<td>{$stationMemo}</td>";
            $listStr .= "<td>{$item["memo"]}</td>";
            $statusMemo = $staffDM->statusMemo[$item["status"]];
            $listStr .= "<td>{$statusMemo}</td>";
            $listStr .= "<td>";
            $url = url("consoles_mod", array("con" => "Staff", "id" => $item["id"]));
            $listStr .= "<a href='{$url}' data-side-form='900px'>修改</a>";
            $url01 = url("consoles_delete", array("con" => "Staff", "id" => $item["id"]));
            if ($item["status"] == 3) {
                $listStr .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$url01}' data-confirm='一旦删除，不可恢复，确定吗？'>删除</a>";
            }
            $listStr .= "</td>";
            $listStr .= "</tr>";
        }

        $this->assign("lists", $listStr);
//        } else {
//            $lists = $staffDM->getList($this->sid);
//            $this->assign("lists", $lists);
//        }

        $this->assign("searchForm", $searchForm);


        return $this->display();
    }

    /**
     * 下级列表
     * @return \phpex\Foundation\Response
     */
    public function branchLists() {
        $comMemDM = CompanyMemberDModel::getInstance();
        $seach = $this->search();
        $seach->labelType("placeholder");
        $seach->addKeyword("u.fullName", "姓名");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        $where = "c.leader=" . $this->getUser("id") . " AND c.sid=" . $this->sid . " AND c.status=1";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $comMemDM->name("c")
            ->select("c,u")
            ->leftJoin("User", "u", "u.id=c.userId")
            ->where($where)
            ->setParameter($params)
            ->setPage($this->page())
            ->getArray(true);
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function add() {
        $sid = Q()->get->all()['sid'];
        if ($sid == null || $sid == '') return $this->error("错误的请求：缺少企业标识");

        $staffDM = StaffDModel::getInstance();
        $stationDM = StaffStationDModel::getInstance();
        $memberDM = CompanyMemberDModel::getInstance();
        $userDM = UserDModel::getInstance();

        //统计目前已经添加的员工数
        $userCountNow = $memberDM->name("cm")->where("cm.sid=" . $sid)->count() ?: 0;

        $company = CompanyDModel::getINstance()->findOneBy(array("id" => $sid));
        //统计还可以添加的员工人数
        $remainUserCount = (int)($company->getUserCount() - $userCountNow);

        //计算离过期的天数
        $thisTime = date("Y-m-d H:i:s");
        $remainDay = floor((strtotime(totime($company->getExpireTime())) - strtotime($thisTime)) / 86400);

        if (Q()->isGet()) {
            $roleDM = RbacRoleDModel::getInstance();
            $roles = $roleDM->name("r")->where("r.sid={$sid} and r.module = '{$this->moduleName}'")
                ->order("r.sort", "ASC")->getArray();
            $options = DepartmentDModel::getInstance()->getOptions($sid, 0);

            $where = "s.sid =" . $sid . " and s.status=1";
            $stations = $stationDM->name("s")->select("s.id,s.names,s.department")
                ->where($where)
                ->getArray();

            $this->assign("roles", $roles);
            $this->assign("options", $options);
            $this->assign("stations", $stations);
            $this->assign("company", $company);
            $this->assign("userCountNow", $userCountNow);
            $this->assign("remainDay", $remainDay);
            $this->assign("statusMemo", $staffDM->statusMemo);
            $this->assign("inviteInfo", $memberDM->getInviteInfo($this->getUser()));

            return $this->display();
        }

        $post = Q()->post->all();
        if (!$post["full_name"]) {
            return $this->error("姓名不能为空");
        }
        if (!$post["department"]) {
            return $this->error("请选择部门");
        }
        if (!$post["station"]) {
            return $this->error("请选择职位，如尚无职位请先前往创建");
        }
//        dump($post);die;
        //检测有效性输入
        if (!preg_match("/^1[345789]{1}\d{9}$/", $post['phone'])) {
            return $this->error("手机号码格式错误");
        }
        //检测收费账户是否到期
        if ($company->getUserCount() > 9 and $remainDay <= 0) {
            return $this->error("您的账户已过期，请联系客服续费！");
        }
        //检测员工数是否超额
        if ($remainUserCount <= 0) {
            return $this->error("您的员工数已达到最多可以添加的数额，请联系客服扩容！");
        }
        $member = $memberDM->findOneBy(array("phone" => $post["phone"], "sid" => $sid));
        if ($member) return $this->error("同事已经是企业的一员或邀请已发送");

        $userDM = UserDModel::getInstance();
        $user = $userDM->findOneBy(array("phone" => $post["phone"]));
        if ($user) {
            //判断是否已经邀请在内注册成功(兼容之前的数据)
            $member1 = $memberDM->findOneBy(array("userId" => $user->getId(), "sid" => $sid));
            if ($member1) return $this->error("该手机号码已经存在该企业或者团队下");
        }

//        dump($post);die;
//        $departmentDM = DepartmentDModel::getInstance();
//        $stations = array();
//        foreach ($post["department"] as $key => $departmentId) {
//            $stations[] = array($departmentId, $post["station"][$key]);
//            /** @var Department $department */
//            $department = $departmentDM->find($departmentId ?: 0);
//            if (!$department || $department->getSid() != $sid) return $this->error("部门信息获取失败");
//            /** @var StaffStation $station */
//            if (!$post["station"][$key]) continue;
//            $station = $stationDM->find($post["station"][$key]);
//            if (!$station || $station->getSid() != $sid) return $this->error("职位信息获取失败");
//
//            if ($station->getNum() > 0) {
//                $count = $staffDM->name("s")->where("s.sid=$sid and s.station=" . $station->getId())->count();
//                if ($count >= $station->getNum()) {
//                    return $this->error(sprintf("【%s】职位人数为%d人，如需要设置更多人员，请到职位管理更改", $station->getNames(), $station->getNum()));
//                }
//            }
//        }

        $member = $memberDM->newEntity();
        $member->setSid($sid);
        $member->setUserId($user ? $user->getId() : 0);
        $member->setFullName($post["full_name"]);
        $member->setPhone($post["phone"]);
        $member->setAddTime(nowTime());
        $member->setTypes(1);
        $member->setStatus(0);
        $member->setSurveyAcorn(0);
        $member->setRecId($this->getUser('id'));
        $member->setDepartment($post['department']);
        $member->setStation($post['station']);
        $member->setInviteInfo(json_cn(array("memo" => $post["InviteInfo"])));
        $memberDM->add($member)->flush();

        if ($post["sendSms"] == 1) {
            $company = CompanyDModel::getInstance()->find($sid);
            $web = $companyUrl = url("~consoles_login_login", array("recEN" => $post["phone"], "company" => $company ? $company->getCodeNo() : ""));
            $this->sendVerify($post['phone'], $web, $company ? $company->getCodeNo() : "");
        }

        return $this->success("邀请已发送,请等待同事的回应", url('consoles_staff_lists', 'sid=' . $sid));
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

    /**
     * 修改员工
     */
    public function modify($id) {
        $comMebDM = CompanyMemberDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $stationDM = StaffStationDModel::getInstance();

        $cm = $comMebDM->name("c")->where("c.id={$id}")->getOneArray();
        if (!$cm) {
            return $this->error("该员工不存在或者存在异常");
        }

        $userId = $cm['userId'];
        $sid = $cm['sid'];
        $c_status = $cm['status'];

        $staff = $staffDM->name("s")->where("s.userId={$userId} and s.sid=$sid")->getOneArray();

        if (Q()->getMethod() == "GET") {
            //部门信息初始化
            $queryDepartment = $this->queryDepartment($sid);
            $depNames = $queryDepartment['depNames'];
            $depIds = $queryDepartment['depIds'];
            $departmentOption = null;
            foreach ($depIds as $kk => $vv) {
                if ($vv == $staff['department']) {
                    $departmentOption .= "<option selected value='$vv'>$depNames[$kk]</option>";
                } else {
                    $departmentOption .= "<option value='$vv' id='$vv'>$depNames[$kk]</option>";
                }
            }

            //职位信息初始化
            $stations = $stationDM->name("s")->select("s.id,s.names,s.department")->where("s.sid=$sid and s.status=1")->getArray();

            $this->assign("staff", $staff);
            $this->assign("departmentOption", $departmentOption);
            $this->assign("stations", $stations);
            $this->assign("statusMemo", $staffDM->statusMemo);
            $this->assign("c_status", $c_status);
            return $this->display();
        }

        $post = Q()->post->all();

        $userDM = UserDModel::getInstance();
        $user = $userDM->find($userId);
        if (!$user) {
            return $this->error("没有此用户");
        }

        //不能设置自己为离职
        if ($post["status"] == 3 && $userId == $this->getUser("id")) {
            return $this->error("不能设置自己为离职");
        }

        $staffEn = $staffDM->findOneBy(array("id" => $staff['id']));

        if (!$staffEn) {
            return $this->error("企业已邀请,等待用户确认");
        }

        $staffEn->setFullName($user->getFullName());
        $staffEn->setUserId($userId);
        $staffEn->setUserName($user->getUserName());
        $staffEn->setRoleName("staff");
        $staffEn->setDepartment($post['department']);
        $staffEn->setStation($post['station']);
        $staffEn->setPhone($post['phone']);
        $staffEn->setQq($post['qq']);
        $staffEn->setWx($post['wx']);
        $staffEn->setEmail($post['email']);
        $staffEn->setMemo($post['memo']);
        $staffEn->setStatus($post['status']);
        $staffDM->save($staffEn)->flush($staffEn);


        //如果设置成离职员工，则清空TODO和任务列表
        if ($post["status"] == 3) {
            TodoDModel::cleanForUserId($userId, $sid);
            TaskAllotDModel::stopAllot($userId, $sid);
            //检验该离职员工主企业
            if ($user->getSid() == $staffEn->getSid()) {
                $cmNextOne = $comMebDM->name("cm")->where("cm.userId={$user->getId()} and cm.sid<>{$user->getSid()}")->limit(1, 1)->getOneArray();
                $user->setSid($cmNextOne["sid"]);
                $userDM->save($user)->flush($user);
            }
        }

        return $this->success("修改成功！");
    }

    /**
     * 修改员工（已关闭）
     * @param $id
     * @return mixed
     */
    public function modify_close($id) {
        $comMebDM = CompanyMemberDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $stationDM = StaffStationDModel::getInstance();
        $roleDM = RbacRoleDModel::getInstance();
        $roles = $roleDM->name("r")->where("r.sid={$this->sid} and r.module = '{$this->moduleName}'")
            ->order("r.sort", "ASC")->getArray();
        $options = $this->getOptions($this->sid, 0);

        $where = "s.sid =" . $this->sid;

        $stations = $stationDM->name("s")->select("s.id,s.names,s.department")
            ->where($where)
            ->getArray();

        $queryDepartment = $this->queryDepartment();
        $depNames = $queryDepartment['depNames'];
        $depIds = $queryDepartment['depIds'];

        $curStaff = $staffDM->name("s")->where("s.id={$id}")->getOneArray();
        $userid = $curStaff['userId'];
        $curSid = $curStaff['sid'];
        $oldStaffInfo = $staffDM->name("si")->where("si.userId={$userid} and si.sid={$curSid}")->getArray();

        foreach ($oldStaffInfo as $k => $v) {
            foreach ($depIds as $kk => $vv) {
                if ($vv == $v['department']) {
                    $oldStaffInfo[$k]['curDepartment'] = $depNames[$kk];
                }
            }
//            $oldStaffInfo[$k]['curDandS'] = $oldStaffInfo[$k]['curDepartment']."->".$oldStaffInfo[$k]['curStation'];
            $oldStaffInfo[$k]['curDandS'] = $oldStaffInfo[$k]['curDepartment'];
        }
        $staffStationDM = StaffStationDModel::getInstance();
        if (!$curStaff['station']) {
            $staffStation = $staffStationDM->name('ss')->where("ss.sid=" . $this->sid)->getArray();
        } else {
            $staffStation = $staffStationDM->name('ss')->where("ss.sid=" . $this->sid . " and ss.id !=" . $curStaff['station'])->getArray();
        }
        $myStation = $staffStationDM->name('ss')->where("ss.sid=" . $this->sid . " and ss.id=" . $curStaff['station'])->getOneArray();
        if (Q()->getMethod() == "GET") {

            $this->assign([
                "curStaff" => $curStaff,
                "oldStaffInfo" => $oldStaffInfo,
                "roles" => $roles,
                "options" => $options,
                "stations" => $stations,
                "statusMemo" => $staffDM->statusMemo,
                "staffStation" => $staffStation,
                "myStation" => $myStation,
            ]);
            return $this->display();
        }

        $post = Q()->post->all();
        if (!in_array($post['oneDepartment'], $post['department']) and $post['oneDepartment'] != 0) {
            $post['department'][] = $post['oneDepartment'];
        }

        if ($post['station']) {
            $station = $staffStationDM->name('ss')->where("ss.id=" . $post['station'])->getOneArray();
            if (!$station) {
                return $this->error("该职位不存在");
            }
            $num = $staffDM->name("s")->where("s.sid = " . $this->sid . " and s.station =" . $post['station'])->count();
            if ($station['num'] <= $num and $station['num'] != 0) {
                return $this->error("所选职位已超出设定人数");
            }
        }


        $userDM = UserDModel::getInstance();
        $user = $userDM->name('u')->where("u.id={$userid}")->getOneArray();
        if (!$user) {
            return $this->error("没有此用户");
        }

        // 检查成员表，如果成员不存在，就添加
        $comMebEN = $comMebDM->findOneBy(array("userId" => $userid, "sid" => $curSid));
        if (!$comMebEN) {
            $comMebEN = $comMebDM->newEntity();
            $comMebEN->setUserId($userid);
            $comMebEN->setSid($curSid);
            $comMebEN->setAddTime(nowTime());
            $comMebEN->setTypes(1);
            $comMebEN->setStatus(1);
            $comMebEN->setAcorn(0);
            $comMebDM->add($comMebEN)->flush();
        }

        if ($post["department"]) { //如果设置了部门
            $ids = array();
            foreach ($post['department'] as $k => $v) {
                /** @var Staff $staff */
                $staff = $staffDM->findOneBy(array("userId" => $userid, "sid" => $curSid, "department" => $v));

                if (!$staff) {
                    $staff = $staffDM->newEntity();
                    $staff->setAddTime(nowTime());
                }
                $staff->setSid($curSid);
                $staff->setFullName($user["fullName"]);
                $staff->setUserId($userid);
                $staff->setUserName($user["userName"]);
                $staff->setRoleName("staff");
                $staff->setDepartment($v);
                $staff->setStation($post['station']);
                $staff->setPhone($post['phone']);
                $staff->setQq($post['qq']);
                $staff->setWx($post['wx']);
                $staff->setEmail($post['email']);
                $staff->setMemo($post['memo']);
                $staff->setStatus($post['status']);
                $staffDM->add($staff)->flush($staff);
                $ids[] = $staff->getId();
            }
            $idsStr = join(",", $ids);
            $staffDM->name("s")->where("s.userId={$userid} and s.sid={$curSid} and s.id not in ($idsStr)")->delete();
        } else {  // 如果未设置部门
            $staff = $staffDM->findOneBy(array("userId" => $userid, "sid" => $curSid, "department" => 0));
            if (!$staff) {
                $staff = $staffDM->newEntity();
                $staff->setAddTime(nowTime());
            }
            $staff->setSid($curSid);
            $staff->setFullName($user["fullName"]);
            $staff->setUserId($userid);
            $staff->setUserName($user["userName"]);
            $staff->setRoleName("staff");
            $staff->setDepartment(0);
            $staff->setStation($post['station']);
            $staff->setPhone($post['phone']);
            $staff->setQq($post['qq']);
            $staff->setWx($post['wx']);
            $staff->setEmail($post['email']);
            $staff->setMemo($post['memo']);
            $staff->setStatus($post['status']);
            $staffDM->add($staff)->flush($staff);

            $staffDM->name("s")->where("s.userId={$userid} and s.sid={$curSid} and s.department <>0")->delete();
        }

        if ($post["status"] == 3) { //如果设置成离职员工，则清空TODO和任务列表
            TodoDModel::cleanForUserId($userid, $curSid);
            TaskAllotDModel::stopAllot($userid, $curSid);
        }

        return $this->success("修改成功！");
    }


    public function findUsers() {
        $maxRow = 5;

        $sid = $this->getUser("sid") ?: 0;

        if (!$sid) return $this->ajaxReturn(array());


        $companyDM = CompanyDModel::getInstance();


        /** @var Company $company */

        $company = $companyDM->find($this->sid);

        $userDM = UserDModel::getInstance();
        $keywords = Q()->post->get("keywords");

        $ids = array($company ? $company->getSuperid() : 0);

        $excepts = explode(",", Q()->post->get("except"));

        $ids = array_merge($ids, $excepts);
        $ids = array_unique($ids);

        $lists = $userDM->name("u")->innerJoin("CompanyMember", "m", "m.userId=u.id")
            ->where("u.phone like :keywords and u.id not in(:ids)  and m.sid=:sid and m.status=1")
            ->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids, "sid" => $sid))
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
            $lists = $userDM->name("u")->innerJoin("CompanyMember", "m", "m.userId=u.id")
                ->where("u.userName like :keywords and u.id not in(:ids) and m.sid=:sid and m.status=1")
                ->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids, "sid" => $sid))
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
            $lists = $userDM->name("u")->innerJoin("CompanyMember", "m", "m.userId=u.id")
                ->where("u.fullName like :keywords and u.id not in(:ids) and m.sid=:sid and m.status=1")
                ->setParameter(array("keywords" => '%' . $keywords . '%', "ids" => $ids, "sid" => $sid))
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


    //将查询出来的部门名称和部门ids字符串转化为数组形式
    public function queryDepartment($sid = null) {
        $sid = !$sid ? $this->sid : $sid;
        $names = $this->getDepartmentOptions($sid, 0);
        $names = explode(",", $names);
        $ids = $this->getDepartmentIds($this->sid, 0);
        $ids = explode(",", $ids);
        return array("depNames" => $names, "depIds" => $ids);
    }

    //查询所有部门，递归分清层级
    public function getDepartmentOptions($sid, $selectId, $pid = 0, $depth = 0) {
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
            $depthstr = $this->departmentDepth($i, $count, $depth);
            $options .= $depthstr . $sec["names"] . ",";
            $options .= $this->getDepartmentOptions($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

    //查询所有部门对应的id，递归分清层级
    public function getDepartmentIds($sid, $selectId, $pid = 0, $depth = 0) {
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
            $idArr .= $this->getDepartmentIds($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $idArr;
    }

    //拼接的字符串
    private function departmentDepth($i, $count, $depth) {
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

    //原始递归
    public function getOptions($sid, $selectId, $pid = 0, $depth = 0) {
        $departmentDM = new DepartmentDModel();
        $where = "d.parentid = $pid and d.sid=$sid";
        $section = $departmentDM->name('d')->where($where)->getArray(false, false);
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= "<option value=\"{$sec["id"]}\"{$selected}>{$depthstr}{$sec["names"]}</option>";
            $options .= $this->getOptions($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

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

    /**
     * 删除邀请记录
     * @param $id
     */
    public function deleteCompanyMember($id) {
        $companyMemberDM = CompanyMemberDModel::getInstance();
        $sid = Q()->get->all()["sid"];
        if (!$sid) return $this->error("删除失败，企业标识缺失！" . $companyMemberDM->getError());
        //删除员工记录
        $result = $companyMemberDM->name("cm")->where("cm.id = {$id}")->delete();
        if ($result == 0) {
            return $this->error("删除失败！" . $companyMemberDM->getError());
        }

        return $this->success("删除成功", url('consoles_staff_lists', 'sid=' . $sid));
    }


    /**
     * 删除员工
     * @param $id
     * @return mixed
     */
    public function delete($id) {
        $userDM = UserDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $companyDM = CompanyDModel::getInstance();
        $staffGroupDM = StaffGroupDModel::getInstance();
        $departmentDM = DepartmentDModel::getInstance();
        $companyMemberDM = CompanyMemberDModel::getInstance();

        $companyMemberEn = $companyMemberDM->find($id);

        $sid = $companyMemberEn->getSid() ?: 0;
        $userId = $companyMemberEn->getUserId();
        //查询员工ID
        $oneEN = $staffDM->name("s")->where("s.userId = {$userId} and s.sid = {$sid}")->getOneArray(false, false);
        if (!$oneEN) return $this->error("数据查询失败(101)！", url('consoles_staff_lists', 'sid=' . $sid));

        //员工ID
        if ($userId == $this->getUser("id")) {
            return $this->error("删除失败，不能删除自己！", url('consoles_staff_lists', 'sid=' . $sid));
        }


        $companyEN = $companyDM->name("c")->where("c.id = {$sid} and (c.superid = {$userId} or REGEXP(c.subSuperid,'(^|\,)(" . $userId . ")(\,|$)')=1)")->getOneArray(false, false);
        if ($companyEN) return $this->error("删除失败，此人是管理员！", url('consoles_staff_lists', 'sid=' . $sid));
        //查询员工信息
        $userEN = $userDM->name("u")->where("u.id = {$userId}")->getOneArray(false, false);
        if (!$userEN) return $this->error("数据查询失败(102)！", url('consoles_staff_lists', 'sid=' . $sid));
        //查询员工记录数据
        $staffEN = $staffDM->name("s")->where("s.sid = {$sid} and s.userId = {$userId}")->getArray(false, false);
        if (!$staffEN) return $this->error("数据查询失败(103)！", url('consoles_staff_lists', 'sid=' . $sid));
        //是否是小吃柜负责人
        $welfareSettingDM = WelfareSettingsDModel::getInstance();
        $wsEN = $welfareSettingDM->name("ws")->where("ws.sid = {$sid} and ws.snackUserId = {$userId}")->getArray(false, false);
        if ($wsEN) return $this->error("删除失败，此人是：小吃柜掌柜。", url('consoles_staff_lists', 'sid=' . $sid));
        //查询员工是否管理部门
        $departmentEN = $departmentDM->name("d")->where("d.sid = {$sid} and d.phone = '{$userEN['phone']}'")->getArray(false, false);
        if ($departmentEN) {
            $one = "";
            foreach ($departmentEN as $k => $v) {
                if ($k != 0) $one .= "，";
                $one .= $v['names'];
            }
            return $this->error("删除失败，此人是：{$one}。部门负责人。", url('consoles_staff_lists', 'sid=' . $sid));
        }
        //是否管理调查组
        $surveyGroupDM = SurveyGroupDModel::getInstance();
        $surveyGroupEN = $surveyGroupDM->name("sg")->where("sg.sid = {$sid} and (sg.leader = {$userId} and sg.helper = {$userId})")->getArray();
        if ($surveyGroupEN) {
            $two = "";
            foreach ($surveyGroupEN as $kk => $vv) {
                if ($kk != 0) $two .= "，";
                $two .= $vv['names'];
            }
            return $this->error("删除失败，此人是：{$two}。调查组负责人。", url('consoles_staff_lists', 'sid=' . $sid));
        }
        //查询员工是否管理小组
        $sgEN = $staffGroupDM->name("sg")->where("sg.sid = {$sid} and sg.leader = '{$userId}' and sg.helper = '{$userId}'")->getArray(false, false);
        if ($sgEN) {
            $three = "";
            foreach ($sgEN as $kkk => $vvv) {
                if ($kkk != 0) $three .= "，";
                $three .= $vvv['subject'];
            }
            return $this->error("删除失败，此人是：{$three}。小组负责人。", url('consoles_staff_lists', 'sid=' . $sid));
        }

        TodoDModel::cleanForUserId($userId, $sid);  //清空todo
        TaskAllotDModel::stopAllot($userId, $sid);  //停止正在执行的任务


        //从小组踢除
        $SGlists = $staffGroupDM->name("sg")->where("sg.sid = {$sid} and REGEXP(sg.members,'(^|\,)(" . $userId . ")(\,|$)')=1")->getArray(false, false);
        foreach ($SGlists as $kkkk => $vvvv) {
            $members = explode(",", $vvvv['members']);
            foreach ($members as $kkkkk => $vvvvv) {
                if ($vvvvv == $userId) unset($members[$kkkkk]);
            }
            $newMembers = implode(",", $members);
            $SGEN4 = $staffGroupDM->find($vvvv['id']);
            $SGEN4->setMembers($newMembers);
            $staffGroupDM->save($SGEN4)->flush($SGEN4);
        }
        //从调查小组踢除
        $SurceyLists = $surveyGroupDM->name("sg")->where("sg.sid = {$sid} and REGEXP(sg.members,'(^|\,)(" . $userId . ")(\,|$)')=1")->getArray(false, false);
        foreach ($SurceyLists as $k6 => $v6) {
            $members = explode(",", $v6['members']);
            foreach ($members as $k61 => $v61) {
                if ($v61 == $userId) unset($members[$k61]);
            }
            $newMembers = implode(",", $members);
            $SGEN6 = $surveyGroupDM->find($v6['id']);
            $SGEN6->setMembers($newMembers);
            $surveyGroupDM->save($SGEN6)->flush($SGEN6);
        }

        //删除员工记录
        $companyMemberDM->name("cm")->where("cm.sid = {$sid} and cm.userId = {$userId}")->delete();

        $staffDM->name("s")->where("s.sid = {$sid} and s.userId = {$userId}")->delete();

        $user = $userDM->findOneBy(array("id" => $userId, "sid" => $sid));
        if ($user) {
            $user->setSid(0);
            $userDM->save($user)->flush();
        }

        return $this->success("删除成功", url('consoles_staff_lists', 'sid=' . $sid));
    }

}
