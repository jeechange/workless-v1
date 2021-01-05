<?php

namespace Admin\DModel;

use Admin\Entity\Settings;
use phpex\DModel\DModel;

class SmsDModel extends DModel {

    /** @var  Settings */

    protected $settings;

    const SEND_CAPTCHA = 'CAPTCHA';
    const MODIFY_SETTING = 'MODIFY_SETTING';
    const SMS_REG = 'SMS_REG';//注册验证
    const SMS_FIND = 'SMS_FIND';//密码找回验证
    const CHANGE_PASSWORD = 'CHANGE_PASSWORD';//修改登录密码
    const SEND_RECOMMEND = 'SEND_RECOMMEND';//发送推荐网址
    const CUI_KUAN = 'CUI_KUAN';
    private $templates = array(
        'CAPTCHA' => array('{#SIGNATURE#}您的手机验证码是：{#CAPTCHA#}，有效时间10分钟，请勿泄漏。如非本人操作，请勿理会。', 600, 0, 60),
        'SMS_REG' => array('{#SIGNATURE#}您的手机验证码是：{#CAPTCHA#}，有效时间10分钟，请勿泄漏。如非本人操作，请勿理会。', 600, 0, 60),

        'SEND_RECOMMEND' => array('{#SIGNATURE#}您的好友欢迎您加入团队：{#CAPTCHA#}', 600, 0, 60),

        'SMS_FIND' => array('您申请手机找回密码，验证码是{#CAPTCHA#}。请保密并确认本人操作！{#SIGNATURE#}', 600, 0, 60),
        'CUSTOM1' => array('您申请手机找回密码，验证码是{#CAPTCHA#}。请保密并确认本人操作！{#SIGNATURE#}', 600, 1, 0),
        'MODIFY_SETTING' => array('{#SIGNATURE#}您的手机验证码是：{#CAPTCHA#}，有效时间10分钟，请勿泄漏。如非本人操作，请勿理会。', 600, 0, 60),
        'TASK_ADD_SUCCESS' => array('{#SIGNATURE#}任务#{#CODE_NO#}发布成功', 600, 0, 60),
        'TASK_EXECUTORS' => array('{#SIGNATURE#}您有一个任务待执行#{#CODE_NO#}', 600, 0, 60),
    );

    private $templateVar = array(
        'SIGNATURE' => '【WorkLess】',
        'CAPTCHA' => '',
    );

    private $statusMemo = array(
        0 => "未使用",
        1 => "已使用",
        2 => "已失效",
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

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Sms();
    }


    public function templateReplace($match) {
        return isset($this->templateVar[$match[1]]) ? $this->templateVar[$match[1]] : "";
    }

    public function setting($settings) {
        $this->settings = $settings;
        return $this;
    }

    function send($template, $tel, $userId = 0, $vars = array()) {
        if (!$this->settings || !($this->settings instanceof Settings)) {
            $this->error = "短信未设置";
            return false;
        }

        if ($this->settings->getStatus() != 1 || $this->settings->getNames() != "sms") {
            $this->error = "短信未启用";
            return false;
        }

        if (!isset($this->templates[$template])) {
            $this->error = "短信模板不可用";
            return false;
        }

        //将已过期的记录设置成已失效
        $params = array(
            'tel' => $tel,
            'template' => $template,
            'sendTime' => addTime('- ' . $this->templates[$template][0])->format("Y-m-d H:i:s"),
            'sid' => $this->settings->getSid(),
        );

        $this->name("s")
            ->where("s.tel =:tel and s.template=:template and s.status=0 and s.sendTime<=:sendTime and s.sid = :sid")
            ->setParameter($params)
            ->update(array('s.status' => 2));

        $params["tel"] = $tel;

        /* @var $smsEN \Admin\Entity\Sms */
        $smsEN = $this->name("s")
            ->where("s.tel=:tel AND s.stat=100 and s.template=:template and s.status=0 and s.sendTime<=:sendTime and s.sid = :sid")
            ->setParameter($params)
            ->order('s.sendTime', "DESC")->setMax(1)->getOneObject();

        $tempInfo = $this->templates[$template];

        if ($tempInfo[3] && $smsEN && (time() - $tempInfo[3]) > ($smsEN->getSendTime()->getTimestamp())) {
            $this->error = "两次发送间隔为{$tempInfo[3]}秒";
            return false;
        }
        if (!isset($vars['CAPTCHA']))
            $code = $smsEN && $smsEN->getCode() ? $smsEN->getCode() : rand(100000, 999999);
        else
            $code = $vars['CAPTCHA'];


        $config = json_decode($this->settings->getSettings(), true);
        //设置模板变量
        $this->templateVar['CAPTCHA'] = $code;
       // $this->templateVar['SIGNATURE'] = $config["SIGNATURE"] ?: $this->templateVar['SIGNATURE'];
        $this->templateVar = array_merge($this->templateVar, $vars);
        $content = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->templates[$template][0]);

        $uid = $config["username"];
        $pwd = $config["password"];
        $mobileids = $tel;

        $content_str = urlencode($content);

        $host = Q()->getHost();
        if (0 === strpos($host, "192.168") || $host == "localhost" || $host == "127.0.0.1" || get_client_ip() == "127.0.0.1") {
            $rsarr = array("stat" => 100, "message" => "本地调试发送！");
        } else {
            $url = "http://api.sms.cn/sms/?ac=send&uid=$uid&pwd=$pwd&mobile=$mobileids&content=$content_str";

            $rs = curl($url);
            $rs = iconv('gbk', 'utf-8//TRANSLIT//IGNORE', $rs);
            $rsarr = json_decode($rs, true);
        }
        if (!isset($rsarr['stat']) || !isset($rsarr['message'])) {
            $this->error = '短信平台无响应';
            return false;
        }

        if ($rsarr['stat'] != 100) {
            $this->error = "错误:" . $rsarr['stat'];
            return false;
        }

        if ($smsEN) {
            $smsEN->setSendTime(nowTime());
            $smsEN->setMobileids($mobileids);
            $smsEN->setStatus($tempInfo[2]);

            $this->add($smsEN)->flush($smsEN);
            $this->name("s")->where("s.template=:template and s.userId=:userId and s.tel=:tel and s.id<>:id")
                ->setParameter(array('template' => $template, 'userId' => $smsEN->getUserId(), 'tel' => $smsEN->getTel(), 'id' => $smsEN->getId()))
                ->update(array('s.status' => 2));
        } else {
            $smsEN = $this->newEntity();
            $smsEN->setUserId($userId);
            $smsEN->setSid($this->settings->getSid());
            $smsEN->setTel($tel);
            $smsEN->setContent($content);
            $smsEN->setSendTime(nowTime());
            $smsEN->setMobileids($mobileids);
            $smsEN->setStat($rsarr['stat']);
            $smsEN->setStatus($tempInfo[2]);
            $smsEN->setAddTime(nowTime());
            $smsEN->setCode($code);
            $smsEN->setTemplate($template);
            $this->add($smsEN)->flush();
        }

        return true;
    }

    public function isValidSms($userId, $tel, $code, $template) {
        /* @var $smsEN \Admin\Entity\Sms */
        $smsEN = $this->name("s")
            ->where("s.tel=:tel AND s.userId=:userId AND s.stat=100 and s.template=:template and s.status=0 and s.code=:code")
            ->setParameter(array("tel" => $tel, "userId" => $userId, 'template' => $template, "code" => $code))
            ->order('s.sendTime', "DESC")->setMax(1)->getOneObject();
        if (!$smsEN) {
            $this->error = "短信验证码无效！";
            return false;
        }
        if ((time() - $this->templates[$template][1]) > $smsEN->getSendTime()->getTimestamp()) {
            $this->error = "操作超时！";
            return false;
        }

        $this->name("s")->where("s.id = {$smsEN->getId()}")->update(array('s.status' => 2));

        return $smsEN;
    }

    function invitingSend($template, $tel, $userId = 0, $vars = array()) {
        $code = $vars['codeNo'];

        if (!$this->settings || !($this->settings instanceof Settings)) {
            $this->error = "短信未设置";
            return false;
        }
        if ($this->settings->getStatus() != 1 || $this->settings->getNames() != "workLessSms") {
            $this->error = "短信未启用";
            return false;
        }
        if (!isset($this->templates[$template])) {
            $this->error = "短信模板不可用";
            return false;
        }

        //将已过期的记录设置成已失效
        $params = array(
            'tel' => $tel,
            'template' => $template,
            'sendTime' => addTime('- ' . $this->templates[$template][0])->format("Y-m-d H:i:s"),
            'sid' => $this->settings->getSid(),
        );

        $smsEN = $this->name("s")
            ->where("s.tel=:tel AND s.stat=100 and s.template=:template and s.status=0 and s.sendTime<=:sendTime and s.sid = :sid")
            ->setParameter($params)
            ->order('s.sendTime', "DESC")->setMax(1)->getOneObject();
        $tempInfo = $this->templates[$template];
        if ($tempInfo[3] && $smsEN && (time() - $tempInfo[3]) < ($smsEN->getSendTime()->getTimestamp())) {
            $this->error = "两次发送间隔为{$tempInfo[3]}秒";
            return false;
        }
        if ($vars['web']) {
            $web = $vars['web'];
        } else {
            $this->error = "数据获取失败";
            return false;
        }
        $config = json_decode($this->settings->getSettings(), true);
        //设置模板变量
        $this->templateVar['CAPTCHA'] = $web;
        $this->templateVar['SIGNATURE'] = $config["SIGNATURE"] ?: $this->templateVar['SIGNATURE'];
        $this->templateVar = array_merge($this->templateVar, $vars);
        $content = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->templates[$template][0]);

        $uid = $config["username"];
        $pwd = $config["password"];
        $mobileids = $tel;

        $content_str = urlencode($content);
        $host = Q()->getHost();
        if (0 === strpos($host, "192.168") || $host == "localhost" || $host == "127.0.0.1" || get_client_ip() == "127.0.0.1") {
            $rsarr = array("stat" => 100, "message" => "本地调试发送！");
        } else {
            $url = "http://api.sms.cn/sms/?ac=send&uid=$uid&pwd=$pwd&mobile=$mobileids&content=$content_str";

            $rs = curl($url);
            $rs = iconv('gbk', 'utf-8//TRANSLIT//IGNORE', $rs);
            $rsarr = json_decode($rs, true);
        }

        if (!isset($rsarr['stat']) || !isset($rsarr['message'])) {
            $this->error = '短信平台无响应';
            return false;
        }
        if ($rsarr['stat'] != 100) {
            $this->error = "错误:" . $rsarr['stat'];
            return false;
        }
        if ($smsEN) {
            $smsEN->setSendTime(nowTime());
            $smsEN->setMobileids($mobileids);
            $smsEN->setStatus($tempInfo[2]);

            $this->add($smsEN)->flush($smsEN);
            $this->name("s")->where("s.template=:template and s.userId=:userId and s.tel=:tel and s.id<>:id")
                ->setParameter(array('template' => $template, 'userId' => $smsEN->getUserId(), 'tel' => $smsEN->getTel(), 'id' => $smsEN->getId()))
                ->update(array('s.status' => 2));
        } else {
            $smsEN = $this->newEntity();
            $smsEN->setUserId($userId);
            $smsEN->setSid($this->settings->getSid());
            $smsEN->setTel($tel);
            $smsEN->setContent($content);
            $smsEN->setSendTime(nowTime());
            $smsEN->setMobileids($mobileids);
            $smsEN->setStat($rsarr['stat']);
            $smsEN->setStatus($tempInfo[2]);
            $smsEN->setAddTime(nowTime());
            $smsEN->setCode($code);
            $smsEN->setTemplate($template);
            $this->add($smsEN)->flush();
        }

        return true;
    }

}
