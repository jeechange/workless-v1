<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 16:54
 */

namespace Jeechange\SDK;

use Admin\Entity\CompanyOpenapi;

class WxSDK {
    public $sid = 0;
    public $agentId = "1000005";
    public $corpid = "wx88137952dcecc786";
    public $corpsecret = "ui8RV-N9oAcWaxNiatcViZFxxTNOdmH4oiBAb2ElM2s";

    public function initConfig(CompanyOpenapi $api) {
        $this->agentId = $api->getAgentid();
        $this->corpid = $api->getCorpid();
        $this->corpsecret = $api->getCorpsecret();
        $this->sid = $api->getSid();
    }


    public function getCodeUrl($redirect_uri) {
        $querys = array(
            "agentid" => $this->agentId,
            "appid" => $this->corpid,
            "redirect_uri" => $redirect_uri,
            "response_type" => "code",
            "scope" => "snsapi_privateinfo",
            "state" => "state",
        );
        $query = http_build_query($querys);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?{$query}#wechat_redirect";
        return $url;
    }

    public function getJsApiConfig() {

        $config = array(
            "beta" => true,
            "debug" => false,
            "appId" => $this->corpid,
            "timestamp" => time(),
            "nonceStr" => rand_string(16, 2),
        );
        $ticket = $this->getJsTicket();
        $url = urldecode(Q()->getSchemeAndHttpHost() . Q()->getRequestUri());
        $config["signature"] = $this->sign($ticket, $config["nonceStr"], $config["timeStamp"], $url);
        $config["jsApiList"] = array(
            'checkJsApi',
            'onMenuShareAppMessage',
            'onMenuShareWechat',
            'onMenuShareTimeline',
            'shareAppMessage',
            'shareWechatMessage',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'closeWindow',
            'scanQRCode',
            'previewFile',
            'openEnterpriseChat',
            'selectEnterpriseContact',
            'onHistoryBack',
            'openDefaultBrowser',
        );
        return $config;
    }

    public function sign($ticket, $nonceStr, $timeStamp, $url) {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }

    public function getJsTicket() {

        $JsTicketPath = Main()->getRuntime() . "/Temp/AccessToken/wxworJsTicket_{$this->sid}.php";
        $this->createDir(dirname($JsTicketPath));
        $JsTicket = array();

        if (is_file($JsTicketPath)) {
            $JsTicket = include $JsTicketPath;
        }
        if ($JsTicket && $JsTicket["timeout"] > time()) return $JsTicket["ticket"];

        $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=" . $this->getAccessToken();
        $res = curl($url);
        if (!$res) return "";
        $resArr = json_decode($res, true);

        $accessToken = array(
            "timeout" => time() + 5400,
            "ticket" => $resArr["ticket"]
        );

        $content = "<?php return " . var_export($accessToken, true) . ";";

        file_put_contents($JsTicketPath, $content);

        return $resArr["ticket"];

    }

    public function getUserId($code) {
        $accessToken = $this->getAccessToken();

        $userCodeUrl = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=' . $accessToken . '&code=' . $code;

        $codeRes = curl($userCodeUrl);
        if (!$codeRes) {
            return false;
        }

        $codeResArr = json_decode($codeRes, true);

        if (!$codeResArr || $codeResArr["errcode"] != 0) return false;

        return isset($codeResArr["UserId"]) ? $codeResArr["UserId"] : $codeResArr["OpenId"];
    }

    public function getUserInfo($userId) {
        $accessToken = $this->getAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token={$accessToken}&userid={$userId}";


        $userRes = curl($url);
        if (!$userRes) {
            return false;
        }

        $userResArr = json_decode($userRes, true);
        if (!$userResArr || $userResArr["errcode"] != 0) return false;
        return $userResArr;
    }


    public function getAccessToken($flush = false) {


        $accessTokenPath = Main()->getRuntime() . "/Temp/AccessToken/wxworAccessToken_{$this->sid}.php";
        $this->createDir(dirname($accessTokenPath));

        $accessToken = array();

        if (!$flush) {
            if (is_file($accessTokenPath)) {
                $accessToken = include $accessTokenPath;
            }
            if ($accessToken && $accessToken["timeout"] > time()) return $accessToken["accessToken"];
        }


        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $this->corpid . '&corpsecret=' . $this->corpsecret;

        $res = curl($url);
        if (!$res) return "";

        $resArr = json_decode($res, true);

        if (!$resArr || $resArr["errcode"] != 0) return "";

        $accessToken = array(
            "timeout" => time() + 5400,
            "accessToken" => $resArr["access_token"]
        );

        $content = "<?php return " . var_export($accessToken, true) . ";";

        file_put_contents($accessTokenPath, $content);

        return $resArr["access_token"];

    }

    private function createDir($dir) {
        if (is_dir($dir)) return;
        $parentDir = dirname($dir);
        if (!is_dir($parentDir)) $this->createDir($parentDir);
        mkdir($dir, 0777, true);
        chmod($dir, 0777);
    }


}