<?php

namespace Admin\DModel;

use Admin\Entity\CompanyOpenapi;
use Admin\Entity\Task;
use phpex\DModel\DModel;

class CompanyOpenapiDModel extends DModel {

    protected $statusMemo = array(
        0 => "停用",
        1 => "启用",

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

        if (!$this->scalar) {
            $result["statusMemo"] = $this->statusMemo[$result["status"]];
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\CompanyOpenapi();
    }


    protected function request_by_curl($remote_server, $post_string) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function dingWebHookText($webhook, $content, $ats = array()) {

        $params = array(
            "msgtype" => "text",
            "text" => array("content" => str_replace(array("\r\n", "\r", "\n"), " ", $content)),
        );

        if ($ats) {
            if ($ats === true) {
                $params["at"] = array("isAtAll" => true);
            } else {
                $params["at"] = array("atMobiles" => $ats);
            }
        }

        $data_string = json_encode($params);

        $result = $this->request_by_curl($webhook, $data_string);
        return $result;
    }

    public function dingWebHookLink($webhook, $title, $content, $link, $picUrl = "") {
        $params = array(
            "msgtype" => "link",
            "link" => array(
                "text" => $content,
                "title" => $title,
                "picUrl" => $picUrl ?: 'https://m.console.xiangshuyun.com/public/mobileConsoles/default/img/share-logo.png',
                "messageUrl" => $link,
            ),
        );

        $data_string = json_encode($params);

        $result = $this->request_by_curl($webhook, $data_string);
        return $result;
    }

    public function dingWebHookMarkdown() {

    }

    public function dingWebHookActionCard() {

    }

    public function dingWebHookFeedCard() {

    }


    public static function sendMessage(Task $task, $message, $ats = true) {
        if (!$task) return false;
        if ($ats === true) {
            $userIds = array($task->getIssueId(), $task->getAcceptId());
            $executors = explode(",", $task->getExecutors());
            $userIds = array_merge($userIds, $executors);
        } elseif ($ats) {
            $userIds = is_array($ats) ? $ats : explode(",", $ats);
        } else {
            $userIds = array();
        }
        $userIds = array_filter($userIds);
        $userIds = array_unique($userIds);

        if (!$userIds) return false;

        $userDM = UserDModel::getInstance();

        $users = $userDM->name("u")->select("u.phone")->where("u.id in (:userIds)")
            ->setParameter("userIds", $userIds)->getArray(false, false);
        $phones = array();
        foreach ($users as $user) {
            if ($user["phone"]) $phones[] = $user["phone"];
        }
        if (!$phones) return false;

        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi[] $apis */
        $apis = $apiDM->findBy(array("namesEn" => "dingwebhook", "sid" => $task->getSid(), "status" => 1));
        if (!$apis) return false;

        foreach ($apis as $api) {
            $apiDM->dingWebHookText($api->getCorpsecret(), $message, $phones);
        }
        return true;
    }

    public static function sendAcornMessage($sid, $message, $ats = array()) {
        $userIds = array_filter($ats);
        $userIds = array_unique($userIds);

        if (!$userIds) return false;
        $userDM = UserDModel::getInstance();

        $users = $userDM->name("u")->select("u.phone")->where("u.id in (:userIds)")
            ->setParameter("userIds", $userIds)->getArray(false, false);
        $phones = array();
        foreach ($users as $user) {
            if ($user["phone"]) $phones[] = $user["phone"];
        }
        if (!$phones) return false;

        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi[] $apis */
        $apis = $apiDM->findBy(array("namesEn" => "dingwebhook", "sid" => $sid, "status" => 1));
        if (!$apis) return false;

        foreach ($apis as $api) {
            $apiDM->dingWebHookText($api->getCorpsecret(), $message, $phones);
        }
        return true;

    }


}