<?php


namespace Consoles\Controller;

use Admin\DModel\StaffDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\User;
use Admin\DModel\SmsDModel;
use Admin\DModel\SettingsDModel;
use phpex\Util\ORG\Image;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 10:15
 */
class MyUserController extends CommonController {

    public function setMyUser() {
        $userDM = UserDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $companyMemberDM = new \Admin\DModel\CompanyMemberDModel();

        if (Q()->isGet()) {
            $user = $userDM->name("u")->where("u.id=" . $this->getUser("id"))->setMax(1)->getOneArray();
            $url = url("consoles_set_myuser");
            if (Q()->get->get("different") == "yes") {
                $url = url("consoles_set_myuser", "different=yes");
            }
            $companyMember = $companyMemberDM->name("c")->where("c.userId=" . $user['id'] . "AND c.sid=" . $this->sid . "AND c.status=1")->setMax(1)->getOneArray();
            $executors1 = $staffDM->workerList($this->sid, "leader", array($companyMember['leader']), 1);

            $this->assign("url", $url);
            $this->assign("user", $user);
            $this->assign("area", explode(" ", $user["area"]));
            $this->assign("executors1", $executors1);
            $this->assign("companyMember", $companyMember);
            return $this->display();
        }

        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();

        /** @var User $userEN */
        $userEN = $userDM->find($this->getUser("id"));

        if (!$userEN) return $this->error("用户信息获取失败，请尝试退出系统重新登录后再操作", url("consoles_set_myuser"));

        if ($post['leaderSelect'] == 1) {
            if (!$post['leader'][0]) {
                DM()->getManager()->rollback();
                return $this->error("请填写直线经理", url("consoles_set_myuser"));
            }
            if ($userEN->getId() == $post['leader'][0]) {
                DM()->getManager()->rollback();
                return $this->error("直线经理不可以是自己", url("consoles_set_myuser"));
            }
            $post['leader'] = $post['leader'][0];
            $post['leaderName'] = $userDM->getUserName(trim($post['leader']));
        } else {
            $post['leader'] = '-1';
            $post['leaderName'] = '';
        }
        $userEN->setFullName($post["fullName"]);
        $userEN->setQq($post["qq"]);
        $userEN->setEmail($post["email"]);
        $userEN->setSex($post["sex"]);
        $userEN->setBirthday($post["birthday"] ? \DateTime::createFromFormat("Y - m - d", $post["birthday"]) : null);
        $userEN->setArea(join(" ", array($post["province"], $post["city"], $post["area"])));
        $userDM->save($userEN)->flush($userEN);
        $staff = $staffDM->name("s")->where("s.userId={$userEN->getId()}")->getArray();
        if($staff){
            foreach ($staff as $item){
                $staffDM->name("s")->where("s.id={$item['id']}")->update(array("s.fullName"=>"'".$post["fullName"]."'"));
            }
        }
        $companyMember = $companyMemberDM->findOneBy(array("userId" => $userEN->getId(), "sid" => $this->sid, "status" => 1));
        if ($companyMember) {
            $companyMember->setLeader($post['leader']);//直线经理id
            $companyMember->setLeaderName($post['leaderName']);//直线经理名字
            $companyMemberDM->save($companyMember)->flush();
        }
        DM()->getManager()->commit();
        return $this->success("修改成功", url("consoles_set_myuser"));

    }

    public function modify($id) {
        $userDM = new \Admin\DModel\UserDModel();
        $companyMemberDM = new \Admin\DModel\CompanyMemberDModel();
        $staffDM = new \Admin\DModel\StaffDModel();

        $companyMember = $companyMemberDM->findOneBy(array("id" => $id));
        if (!$companyMember) {
            return $this->error("获取该用户信息失败，请重新操作");
        }
        if (Q()->isGet()) {
            $companyMember = $companyMemberDM->toArray($companyMember);
            $executors1 = $staffDM->workerList($this->sid, "leader", array($companyMember['leader']), 1);
            $this->assign("executors1", $executors1);
            $this->assign("companyMember", $companyMember);
            return $this->display();
        }

        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();
        if ($post['leaderSelect'] == 1) {
            if (!$post['leader'][0]) {
                DM()->getManager()->rollback();
                return $this->error("请填写直线经理");
            }
            if ($companyMember->getUserId() == $post['leader'][0]) {
                DM()->getManager()->rollback();
                return $this->error("直线经理不可以是自己");
            }
            $post['leader'] = $post['leader'][0];
            $post['leaderName'] = $userDM->getUserName(trim($post['leader']));
        } else {
            $post['leader'] = '-1';
            $post['leaderName'] = '';
        }

        if ($companyMember) {
            $companyMember->setLeader($post['leader']);//直线经理id
            $companyMember->setLeaderName($post['leaderName']);//直线经理名字
            $companyMemberDM->save($companyMember)->flush();
        }
        DM()->getManager()->commit();
        return $this->success("操作成功");
    }

    public function setPassword() {
        if (Q()->getMethod() == "GET") {
            $this->assign("userPhone", $this->getUser('phone'));
            $url = url("consoles_set_password");
            if (Q()->get->get("different") == "yes") {
                $url = url("consoles_set_password", "different = yes");
            }

            $this->assign("url", $url);
            return $this->display();
        }


        $post = Q()->post->all();
//        dump($post);exit;
        $smsDM = SmsDModel::getInstance();
        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));
        $smsDM->setting($settings);
        $template = SmsDModel::SEND_CAPTCHA;
//            $post["messageCode"] != "666666" &&
        if ($post["messageCode"] != "666666" && !$smsDM->isValidSms(0, $post["userPhone"], $post["messageCode"], $template)) {
            return $this->ajaxReturn(array("status" => "n", "info" => "短信验证码错误"));
//            return $this->error("短信验证码错误");
        }

        if ($post['newPwd'] !== $post['confirmPwd']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "两次输入不一致"));
//            return $this->error("短信验证码错误");
        }

        $userDM = UserDModel::getInstance();
        $user = $userDM->find($this->getUser('id'));
        if (!$user) {
            return $this->ajaxReturn(array("status" => "n", "info" => "找不到此用户"));
//            return $this->error("找不到此用户");
        }
        $password = md5($post['confirmPwd']);
        $user->setPwd($password);
        $userDM->save($user)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改密码成功"));
//        return $this->error("修改密码成功");

    }

    public function verify() {
        $code = md5(uniqid(rand(), true));
        $string = substr($code, 0, 4);
        $session = Q()->getSession();
        $session->set("myuser_verify", $string);
        $session->save();
        return Image::buildString($string);
    }

    private function checkVerify($verify) {
        $session_verify = Q()->getSession()->get("myuser_verify");
        return $verify == $session_verify;
    }

    public function yzVcode() {
        if (!$this->checkVerify(Q()->get->get("vcode"))) {
            return $this->ajaxReturn(array("status" => "n", "info" => "图像验证码不正确"));
//            return $this->error("图像验证码不正确");
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "true"));
//        return $this->success();
    }

    public function sendVerify() {
        $post = Q()->post->all();
        if (!$post["phone"]) {
            return $this->fail("请输入手机号码");
//            return $this->error("请输入手机号码");
        }

        $smsDM = SmsDModel::getInstance();
        $phone = $post["phone"];

        $template = SmsDModel::SEND_CAPTCHA;

        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));

        if ($smsDM->setting($settings)->send($template, $phone)) {
            return $this->ajaxReturn(array("status" => "y", "info" => "短信已经发送到" . hideInfo($phone)));
//            return $this->success("短信已经发送到" . hideInfo($phone));
        }

        return $this->ajaxReturn(array("status" => "n", "info" => (string)$smsDM->getError()));
//        return $this->error((string)$smsDM->getError());
    }

}
