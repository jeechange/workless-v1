<?php

namespace Admin\DModel;

use Admin\Entity\Settings;
use phpex\DModel\DModel;
use phpex\Util\ORG\PHPMailer;

class EmailDModel extends DModel {


    protected $templates = array(
        'CAPTCHA' => array('您的手机验证码是：{#CAPTCHA#}，有效时间10分钟，请勿泄漏。如非本人操作，请勿理会。{#SIGNATURE#}', 600, 0, 60),
        'SMS_REG' => array('您的手机验证码是：{#CAPTCHA#}，有效时间10分钟，请勿泄漏。如非本人操作，请勿理会。{#SIGNATURE#}', 600, 0, 60),
        'SMS_FIND' => array('您申请手机找回密码，验证码是{#CAPTCHA#}。请保密并确认本人操作！{#SIGNATURE#}', 600, 0, 60),
        'CUSTOM' => array("", 600, 0, 60),
        'CUSTOM1' => array("", 600, 1, 0),
    );


    private $templateVar = array(
        'CAPTCHA' => '',
    );

    /** @var  Settings */
    protected $apiConfig;

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
        return new \Admin\Entity\Email();
    }

    public function templateReplace($match) {
        return isset($this->templateVar[$match[1]]) ? $this->templateVar[$match[1]] : "";
    }


    public function setting($api) {
        $this->apiConfig = $api;
        return $this;
    }


    public function send($template, $email, $title, $content = "", $vars = array()) {
        if (!$this->apiConfig || !($this->apiConfig instanceof Settings)) {
            $this->error = "邮件未设置";
            return false;
        }
        if ($this->apiConfig->getStatus() != 1 || $this->apiConfig->getNames() != "email") {
            $this->error = "邮件未启用";
            return false;
        }
        if (!isset($this->templates[$template])) {
            $this->error = "邮件模板不可用";
            return false;
        }
        $tempInfo = $this->templates[$template];

        if (!$content) $content = $tempInfo[0];


        //将已过期的记录设置成已失效
        $params = array(
            'template' => $template,
            'sendTime' => addTime('- ' . $this->templates[$template][0])->format("Y-m-d H:i:s"),
            'sid' => $this->apiConfig->getSid(),
        );

        $this->name("m")
            ->where("m.template=:template and m.status=0 and m.sendTime<=:sendTime and m.sid = :sid")
            ->setParameter($params)
            ->update(array('m.status' => 2));


        $params["email"] = $email;

        /* @var $emailEN \Admin\Entity\Email */
        $emailEN = $this->name("m")
            ->where("m.template=:template and m.status=0 and m.sendTime<=:sendTime and m.sid = :sid and m.toemail=:email")
            ->setParameter($params)
            ->order('m.sendTime', "DESC")->setMax(1)->getOneObject();


        if ($tempInfo[3] && $emailEN && (time() - $tempInfo[3]) > ($emailEN->getSendTime()->getTimestamp())) {
            $this->error = "两次发送间隔为{$tempInfo[3]}秒";
            return false;
        }
        if (!isset($vars['CAPTCHA']))
            $code = $emailEN && $emailEN->getSendCode() ? $emailEN->getSendCode() : rand(100000, 999999);
        else
            $code = $vars['CAPTCHA'];

        $config = json_decode($this->apiConfig->getSettings(), true);

        //设置模板变量
        $this->templateVar['CAPTCHA'] = $code;
        $this->templateVar['SIGNATURE'] = $config["signature"] ?: $this->templateVar['SIGNATURE'];
        $this->templateVar = array_merge($this->templateVar, $vars);
        $content = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $content);

        $host = Q()->getHost();
        $host = "1111";
        if (0 !== strpos($host, "192.168") && !in_array($host, array("localhost", "127.0.0.1", "::1"))) {
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->IsHTML();
            $mail->CharSet = 'UTF-8';
            $mail->AddAddress($email);
            $mail->Body = $content;
            $mail->From = $config["address"];
            $mail->FromName = $config["names"];
            $mail->Subject = $title;
            $mail->Host = $config["smtp"];
            $mail->SMTPAuth = true;
            $mail->Username = $config["address"];
            $mail->Password = $config["password"];
            $send = $mail->Send();
            if (!$send) {
                $this->error = $mail->ErrorInfo;
                return false;
            }
        }

        if ($emailEN) {
            $emailEN->setSendTime(nowTime());
            $emailEN->setToemail($email);
            $emailEN->setStatus($tempInfo[2]);

            $this->add($emailEN)->flush($emailEN);
            $params["id"] = $emailEN->getId();
            $this->name("m")
                ->where("m.template=:template and m.status=0 and m.sendTime<=:sendTime and m.sid = :sid and m.toemail=:email and m.id<>:id")
                ->setParameter($params)
                ->update(array('s.status' => 2));

        } else {
            $emailEN = $this->newEntity();

            $emailEN->setSid($this->apiConfig->getSid());
            $emailEN->setFromemail($config["address"]);
            $emailEN->setToemail($email);

            $emailEN->setTitle($title);
            $emailEN->setContent($content);

            $emailEN->setAddTime(nowTime());
            $emailEN->setSendTime(nowTime());
            $emailEN->setSendCode($code);

            $emailEN->setTemplate($template);
            $emailEN->setStatus($tempInfo[2]);

            $this->add($emailEN)->flush($emailEN);

        }
        return $emailEN;
    }


}