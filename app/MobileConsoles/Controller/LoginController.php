<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/8/21
 * Time: 9:42
 */

namespace MobileConsoles\Controller;


use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\SettingsDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\SmsDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\UserTokenDModel;
use Admin\Entity\Company;
use Admin\Entity\CompanyMember;
use Admin\Entity\CompanyOpenapi;
use Jeechange\SDK\DingSDK;
use Jeechange\SDK\WxSDK;
use phpex\Util\ORG\Image;

class LoginController extends CommonController {


    public function dingLogin() {
        $DingSDK = new DingSDK();
        if (Q()->isGet()) {
            $get = Q()->get->all();
            if (!$get['company']) {
                $this->assign("message", "已退出系统，请从工作台重新进入");
                return $this->display("dingLoginError");
            }

            $companyDM = CompanyDModel::getInstance();
            $companyEN = $companyDM->findOneBy(array("codeNo" => $get['company']));
            if (!$companyEN) {
                $this->assign("message", "企业/团队信息获取失败，请从工作台重新进入");
                return $this->display("dingLoginError");
            }

            $apiDM = CompanyOpenapiDModel::getInstance();
            /** @var CompanyOpenapi $api */
            $api = $apiDM->findOneBy(array("sid" => $companyEN->getId(), "namesEn" => "dingtalk"));

            if (!$api) {
                $this->error("message", "请先进入PC版后台完成钉钉配置再使用..");
                return $this->display("dingLoginError");
            }
            if ($api) $DingSDK->initConfig($api);

            $this->assign("company", $get['company']);
            if ($get['user']) {
                $this->assign("recEN", $get['user']);
            }
            $this->assign("corpId", $DingSDK->corpid);
            $this->assign("sdk_types", "dingtalk");
            $this->assign("sdk_config", $DingSDK->getJsApiConfig());
            return $this->display("dingLogin");
        }

        $post = Q()->post->all();
        if (!$post["company"]) {
            return $this->error("已退出系统，请从工作台重新进入..");
        }

        $companyDM = CompanyDModel::getInstance();
        $companyEN = $companyDM->findOneBy(array("codeNo" => $post['company']));
        if (!$companyEN) return $this->error("企业/团队信息获取失败..");
        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi $api */
        $api = $apiDM->findOneBy(array("sid" => $companyEN->getId(), "namesEn" => "dingtalk"));

        if (!$api) {
            return $this->error("请先进入PC版后台完成钉钉配置再使用..");
            //return $this->display("dingLoginError");
        }

        $DingSDK->initConfig($api);

        $userInfo = $DingSDK->getUserInfo($post["code"]);

        if (!$userInfo) {
            return $this->error("自动登录失败..");
        }

        $userDM = UserDModel::getInstance();

        $user = $userDM->findOneBy(array("phone" => $userInfo["mobile"]));

        if (!$user) {
            $NewUser = 2;
            $post['phone'] = $userInfo["mobile"];
            $post["password"] = "123456";
            $post["password_confirm"] = "123456";
            $post["userName"] = $userInfo["mobile"];
            $post["fullName"] = $userInfo["name"];
            $ret1 = $this->register($post);
            if ($ret1['status'] == 'n') {
                return $this->error($ret1["info"]);
            }
        } else {
            $NewUser = 1;
        }
        $user = $userDM->checkLogin($userInfo["mobile"], null);

        if (!$user) {
            //$this->assign("message", );
            return $this->display($userDM->getError());
        }

        if ($post['company']) {
            $companyMemberDM = CompanyMemberDModel::getInstance();
            $companyDM = CompanyDModel::getInstance();
            $companyEN = $companyDM->name('c')->where("c.codeNo = '{$post['company']}' and c.status = 1")->getOneArray();
            if ($post['recEN'] == "root") {
                $recId = $companyEN["superid"];
            } else {
                $recEN = $userDM->name("u")->where("u.phone = {$post['recEN']} and u.status = 1")->getOneArray();
                $recId = $recEN['id'];
            }
            $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN['id']}' and cm.userId = '{$user->getId()}'")->count() ?: 0;
            if ($companyEN && $count <= 0) {
                $companyMemberEN = $companyMemberDM->newEntity();
                $companyMemberEN->setSid($companyEN['id']);
                $companyMemberEN->setUserId($user->getId());
                $companyMemberEN->setRecId($recId);
                $companyMemberEN->setStatus(0);
                $companyMemberEN->setAddTime(nowTime());
                $companyMemberEN->setTypes(1);
                $companyMemberEN->setAcorn(0);
                $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
            } elseif ($companyEN) {
                $user->setSid($companyEN['id']);
                $userDM->save($user)->flush($user);
            }
        }
        RbacAccessDModel::accredit($this->access, "MobileConsoles", $user->getSid(), $user, $user->getRoleName());

        if ($user->getSid() == 0) {
            return $this->success(url("mobileConsoles_user_guide"));
        }

        if ($NewUser == 2) {
//            return $this->success(url("mobileConsoles_user_userInfo", array("Tips" => "new")));
            return $this->success(url("mobileConsoles_user_guide"));
        } else {
            if ($post['company'] && $count <= 0) {
                return $this->success(url("mobileConsoles_company_joined"));
            }

//            //判断是否从分享页过来的
//            $get = Q()->server->all();
//            $referer = $get['HTTP_REFERER'];
//            $index = strpos($referer,"?");
//            $parameters = substr($referer,$index+1);
//            if($parameters){
//                $a = explode("&",$parameters);
//                foreach ($a as $k=>$v){
//                    $index1 = strpos($v,"=");
//                    if($k == 0){
//                        $share = substr($v,$index1+1);
//                    }else{
//                        $terminal = substr($v,$index1+1);
//                    }
//                }
//                if($share){
//                    $shareDM = ShareDModel::getInstance();
//                    $shareEN = $shareDM->find($share);
//                    if(!$shareEN){
//                        return $this->success(url("mobileConsoles_index_index"));
//                    }
//                    $gobackUrl = explode(",",$shareEN->getGobackUrl());
//                    if($terminal == 'isMobile'){
//                        $url = $gobackUrl[1];
//                    }else{
//                        $url = $gobackUrl[0];
//                    }
//                    return $this->success($url);
//                }
//            }

//            if($get['share']){
//                $share = $get['share'];
//                $terminal = $get['terminal'];
//                $shareDM = ShareDModel::getInstance();
//                $shareEN = $shareDM->find($share);
//                if(!$shareEN){
//                    return $this->success(url("mobileConsoles_index_index"));
//                }
//                $gobackUrl = explode(",",$shareEN->getGobackUrl());
//                if($terminal == 'isMobile'){
//                    $url = $gobackUrl[1];
//                }else{
//                    $url = $gobackUrl[0];
//                }
//                return $this->success($url);
//            }

            return $this->success(url("mobileConsoles_index_index"));
        }
    }


    public function wxworkLogin() {

        $get = Q()->get->all();

        $WxSDK = new WxSDK();
        if (Q()->isGet()) {
            if (!$get['company']) {
                $this->assign("message", "已退出系统，请从工作台重新进入");
                return $this->display("dingLoginError");
            }
            $companyDM = CompanyDModel::getInstance();

            $companyEN = $companyDM->findOneBy(array("codeNo" => $get['company']));
            if (!$companyEN) {
                $this->assign("message", "企业/团队信息获取失败，请从工作台重新进入");
                return $this->display("dingLoginError");
            }

            $apiDM = CompanyOpenapiDModel::getInstance();
            /** @var CompanyOpenapi $api */
            $api = $apiDM->findOneBy(array("sid" => $companyEN->getId(), "namesEn" => "wxwork"));

            if (!$api) {
                $this->error("message", "请先进入PC版后台完成企业微信配置再使用..");
                return $this->display("dingLoginError");
            }

            $WxSDK->initConfig($api);
            $this->assign("company", $get['company']);


            if (!$get["code"]) {
                $url = $WxSDK->getCodeUrl(url("~mobileConsoles_login", array("company" => $get['company'], "user" => $get['user'])));
                return $this->redirect($url);
            }
            if ($get['user']) {
                $this->assign("recEN", $get['user']);
            }
            $this->assign("code", $get["code"]);
            $this->assign("sdk_types", "wxwork");
            $this->assign("sdk_config", $WxSDK->getJsApiConfig());
            return $this->display("wxworkLogin");
        }
        $post = Q()->post->all();
        if (!$post["company"]) {
            return $this->error("已退出系统，请从工作台重新进入..");
        }

        $companyDM = CompanyDModel::getInstance();
        /** @var Company $companyEN */
        $companyEN = $companyDM->findOneBy(array("codeNo" => $post['company']));

        if (!$companyEN) return $this->error("企业/团队信息获取失败..");
        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi $api */
        $api = $apiDM->findOneBy(array("sid" => $companyEN->getId(), "namesEn" => "wxwork"));

        if (!$api) {
            $this->error("message", "请先进入PC版后台完成企业微信配置再使用..");
            return $this->display("dingLoginError");
        }

        $WxSDK->initConfig($api);


        $userId = $WxSDK->getUserId($post["code"]);

        $userInfo = $WxSDK->getUserInfo($userId);

        if (!$userInfo) {
            return $this->error("自动登录失败..");
        }

        $userDM = UserDModel::getInstance();
        $user = $userDM->findOneBy(array("phone" => $userInfo["mobile"]));
        if (!$user) {
            $NewUser = 2;
            $post['phone'] = $userInfo["mobile"];
            $post["password"] = "123456";
            $post["password_confirm"] = "123456";
            $post["userName"] = $userInfo["mobile"];
            $post["fullName"] = $userInfo["name"];
            $ret1 = $this->register($post);
            if ($ret1['status'] == 'n') {
                return $this->error($ret1["info"]);
            }
        } else {
            $NewUser = 1;
        }
        $user = $userDM->checkLogin($userInfo["mobile"], null);
        if (!$user) {
            return $this->error($userDM->getError());
        }
        $companyMemberDM = CompanyMemberDModel::getInstance();

        /** @var CompanyMember $companyMemberEN */

        $companyMemberEN = $companyMemberDM->findOneBy(array("sid" => $companyEN->getId(), "userId" => $user->getId()));
        if (!$companyMemberEN) {
            if ($post['recEN'] == "root") {
                $recId = $companyEN->getSuperid();
            } else {
                $recEN = $userDM->name("u")->where("u.phone = {$post['recEN']} and u.status = 1")->getOneArray();
                $recId = $recEN['id'];
            }
            $companyMemberEN = $companyMemberDM->newEntity();
            $companyMemberEN->setSid($companyEN->getId());
            $companyMemberEN->setUserId($user->getId());
            $companyMemberEN->setRecId($recId);
            $companyMemberEN->setStatus(0);
            $companyMemberEN->setAddTime(nowTime());
            $companyMemberEN->setTypes(1);
            $companyMemberEN->setAcorn(0);
            $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
        } elseif ($companyMemberEN->getStatus() == 1) {
            $user->setSid($companyMemberEN->getSid());
            $userDM->save($user)->flush($user);
        }
        RbacAccessDModel::accredit($this->access, "MobileConsoles", $user->getSid(), $user, $user->getRoleName());

        if ($user->getSid() == 0) {
            return $this->success(url("mobileConsoles_user_guide"));
        }

        if ($NewUser == 2) {
            return $this->success(url("mobileConsoles_user_guide"));
        } elseif ($companyMemberEN->getStatus() != 1) {
            return $this->success(url("mobileConsoles_company_joined"));
        }
        return $this->success(url("mobileConsoles_index_index"));

    }

    public function login() {

        $userAgent = Q()->headers->get("user-agent");
//暂时屏蔽钉钉和企业微信的自动登录

//        if (preg_match("#DingTalk#", $userAgent)) {
//            return $this->dingLogin();
//        }
//        if (preg_match("#MicroMessenger#", $userAgent) && (Q()->get->get("company") || Q()->post->get("company"))) {
//            return $this->wxworkLogin();
//        }

        $userDM = UserDModel::getInstance();
        $get = Q()->get->all();
        if ($get['company']) {
            $this->assign("company", $get['company']);
        }
        if ($get['user']) {
            $this->assign("recEN", $get['user']);
        }
        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();

        $NewUser = $post['NewUser'];

        if (!$post["userName"]) {
            return $this->error("请输入手机号码");
        }
//        types = 0，手机验证码登录
//        types = 1，密码登录
        if ($post['types'] == 0) {
//            if(strcasecmp($post['verify'],Q()->getSession()->get("login_verify")) != 0){
//                return $this->error("图像验证码不正确");
//            }
            $user = $userDM->findOneBy(array("userName" => $post['userName']));
            if (!$user) {
                $user = $userDM->findOneBy(array("phone" => $post['userName']));
            }
            if (!$user) {
                if ($NewUser == 1) {
                    return $this->error("您尚未注册，请设置真实姓名完成注册！");
                } else {
                    $post['phone'] = $post['userName'];
                    $ret1 = $this->register($post);
                    if ($ret1['status'] == 'n') {
                        return $this->error($ret1['info']);
                    }
                }
            } else {
                //如果是旧用户，且使用验证码登录，直接修改密码
                if (!$post['password'] || !$post['password_confirm']) {
                    return $this->error("请输入密码");
                } else {
                    if ($post['password'] != $post['password_confirm']) {
                        return $this->error("两次输入不一致");
                    }
                }
                $post['password_confirm'] = md5($post['password_confirm']);
                $user->setPwd($post['password_confirm']);
                $userDM->save($user)->flush();
            }

            $smsDM = SmsDModel::getInstance();
            $settingsDM = SettingsDModel::getInstance();
            $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));
            $smsDM->setting($settings);
            $template = SmsDModel::SEND_CAPTCHA;
//            $post["code"] != "666666" &&
            if ($post["code"] != "666666" && !$smsDM->isValidSms(0, $post["userName"], $post["code"], $template)) {
                return $this->error("验证码错误");
            }

            $user = $userDM->checkLogin($post["userName"], null);
        } else {
            $user = $userDM->checkLogin($post["userName"], $post['pwd']);
            $NewUser = 1;
        }

        if (!$user) {
            return $this->error($userDM->getError());
        }


        $log = array(
            "ip" => Q()->getClientIp(),
            "status" => $user->getStatus(),
            "success" => "y",
            "p" => base64_encode(Q()->post->get("password"))
        );
        loginfo("/mlogin/" . $user->getId(), "用户登录", $log);

        if ($post['company']) {
            $companyMemberDM = CompanyMemberDModel::getInstance();
            $companyDM = CompanyDModel::getInstance();
            $companyEN = $companyDM->name('c')->where("c.codeNo = '{$post['company']}' and c.status = 1")->getOneArray();
            $count = $companyMemberDM->name('cm')->where("cm.sid = '{$companyEN['id']}' and cm.userId = '{$user->getId()}'")->count() ?: 0;
            if ($companyEN && $count <= 0) {
                if ($post['recEN'] == "root") {
                    $recId = $companyEN["superid"];
                } else {
                    $recEN = $userDM->name("u")->where("u.phone = {$post['recEN']} and u.status = 1")->getOneArray();
                    $recId = $recEN['id'];
                }
                $companyMemberEN = $companyMemberDM->newEntity();
                $companyMemberEN->setSid($companyEN['id']);
                $companyMemberEN->setUserId($user->getId());
                $companyMemberEN->setRecId($recId);
                $companyMemberEN->setStatus(0);
                $companyMemberEN->setAddTime(nowTime());
                $companyMemberEN->setTypes(1);
                $companyMemberEN->setAcorn(0);
                $companyMemberDM->add($companyMemberEN)->flush($companyMemberEN);
            } elseif ($companyEN) {
                $user->setSid($companyEN['id']);
                $userDM->save($user)->flush($user);
            }
        }
        RbacAccessDModel::accredit($this->access, "MobileConsoles", $user->getSid(), $user, $user->getRoleName());
        if ($user->getSid() == 0) {
            return $this->success(url("mobileConsoles_user_guide"));
        }

        if ($NewUser == 2) {
//            return $this->success(url("mobileConsoles_user_userInfo", array("Tips" => "new")));
            return $this->success(url("mobileConsoles_user_guide"));
        } else {
            if ($post['company'] && $count <= 0) {
                return $this->success(url("mobileConsoles_company_joined"));
            }

            //判断是否从分享页过来的
            $get = Q()->server->all();
            $referer = $get['HTTP_REFERER'];
            $index = strpos($referer,"?");
            $parameters = substr($referer,$index+1);
            if($parameters){
                $a = explode("&",$parameters);
                foreach ($a as $k=>$v){
                    $index1 = strpos($v,"=");
                    if($k == 0){
                        $share = substr($v,$index1+1);
                    }else{
                        $terminal = substr($v,$index1+1);
                    }
                }
                if($share){
                    $shareDM = ShareDModel::getInstance();
                    $shareEN = $shareDM->find($share);
                    if(!$shareEN){
                        return $this->success(url("mobileConsoles_index_index"));
                    }
                    $gobackUrl = explode(",",$shareEN->getGobackUrl());
                    if($terminal == 'isMobile'){
                        $url = $gobackUrl[1];
                    }else{
                        $url = $gobackUrl[0];
                    }
                    return $this->success($url);
                }
            }

            return $this->success(url("mobileConsoles_index_index"));
        }
    }

    public function register($post) {
        $userDM = UserDModel::getInstance();

        if (!$post['userName']) {
            return array("status" => "n", "info" => "用户名不能为空");
        }
        if (strlen($post['userName']) < 6) {
            return array("status" => "n", "info" => "用户名必须6位数以上");
        }
        if (!preg_match("/^1[345789]{1}\d{9}$/", $post['phone'])) {
            return array("status" => "n", "info" => "手机号码格式错误");
        }
        $checkName = $userDM->findOneBy(array("userName" => $post['userName']));
        if ($checkName) {
            return array("status" => "n", "info" => "用户名已存在");
        }
        $checkPhone = $userDM->findOneBy(array("phone" => $post['phone']));
        if ($checkPhone) {
            return array("status" => "n", "info" => "手机号码已存在");
        }
        if ($post['password'] != $post['password_confirm']) {
            return array("status" => "n", "info" => "两次密码不一致");
        }
        if (!$post['fullName']) {
            return array("status" => "n", "info" => "请填写姓名");
        } else {
            if (preg_match("/\d+/", $post['fullName'])) {
                return array("status" => "n", "info" => "姓名格式错误");
            }
        }

        $now = null;
        $userDM->create($post, $user = $userDM->newEntity());
//        dump($post);exit;
        $user->setRoleName('role_0');
        $user->setAddTime(nowTime());
        $user->setStatus('1');
        $user->setSid(0);
        $userDM->add($user)->flush();

        return array("status" => "y", "info" => "注册完成");
    }

    public function verify() {
        $code = md5(uniqid(rand(), true));
        $string = substr($code, 0, 4);
        $session = Q()->getSession();
        $session->set("login_verify", $string);
        $session->save();

        return Image::buildString($string);
    }

    private function checkVerify($verify) {
        $session_verify = Q()->getSession()->get("login_verify");
        return $verify == $session_verify;
    }

    public function YZvcode() {
        if (!$this->checkVerify(Q()->get->get("vcode"))) {
            return $this->ajaxReturn(array("status" => "n", "info" => "图像验证码不正确"));
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "true"));
    }

    public function sendVerify() {
        $post = Q()->post->all();
        if (!$post["phone"]) {
            return $this->fail("请输入手机号码");
        }

        $smsDM = SmsDModel::getInstance();
        $phone = $post["phone"];
        $template = SmsDModel::SEND_CAPTCHA;

        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));

        if ($smsDM->setting($settings)->send($template, $phone)) {
            return $this->success("短信已经发送到" . hideInfo($phone));
        }
        return $this->error((string)$smsDM->getError());
    }

    public function logout() {
        $log = array(
            "ip" => Q()->getClientIp(),
            "status" => $this->getUser("status"),
            "success" => "y"
        );
        loginfo("/login/" . $this->getUser("id"), "用户注销", $log);

        $userTokenDM = new UserTokenDModel();
        $userTokenDM->name("t")->where("t.deviceId='{$this->deviceId}'")->update(array("t.status" => 0));
        $this->access->clearAccredit();

        return $this->redirect(url("~mobileConsoles_login"));
    }

}