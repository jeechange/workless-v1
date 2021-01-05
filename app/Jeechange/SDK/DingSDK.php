<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/9/29
 * Time: 11:31
 */

namespace Jeechange\SDK;


use Admin\Entity\CompanyOpenapi;

class DingSDK {


    public $sid = 0;
    public $agentId = "194733706";
    public $corpid = "ding97b01f7440a7146b35c2f4657eb6378f";
    public $corpsecret = "00qWs8sHpnP17F6El5TSSRAzvQjeuKH9joLNQhsoH0NKGBnhfg_MjxeEOCsDAhEF";

    public function __construct() {

    }

    public function initConfig(CompanyOpenapi $api) {
        $this->agentId = $api->getAgentid();
        $this->corpid = $api->getCorpid();
        $this->corpsecret = $api->getCorpsecret();
        $this->sid = $api->getSid();
    }


    public function getUserInfo($code) {

        $accessToken = $this->getAccessToken();

        $userCodeUrl = 'https://oapi.dingtalk.com/user/getuserinfo?access_token=' . $accessToken . '&code=' . $code;

        $codeRes = curl($userCodeUrl);
        if (!$codeRes) {
            loginfo("codeRes", "codeRes");
            return false;
        }

        $codeResArr = json_decode($codeRes, true);

        if (!$codeResArr || $codeResArr["errcode"] != 0) {
            loginfo("codeRes", $codeRes);
            return false;
        }

        $userUrl = 'https://oapi.dingtalk.com/user/get?access_token=' . $accessToken . '&userid=' . $codeResArr["userid"];

        $userRes = curl($userUrl);

        if (!$userRes) {
            loginfo("userRes", "userRes");
            return false;
        }
        $userResArr = json_decode($userRes, true);

        if (!$userResArr || $userResArr["errcode"] != 0) {
            loginfo("userRes", $userRes);
            return false;
        }

        return $userResArr;
    }

    public function getUserId($code) {
        $accessToken = $this->getAccessToken();

        $userCodeUrl = 'https://oapi.dingtalk.com/user/getuserinfo?access_token=' . $accessToken . '&code=' . $code;

        $codeRes = curl($userCodeUrl);
        if (!$codeRes) {
            return false;
        }

        $codeResArr = json_decode($codeRes, true);

        if (!$codeResArr || $codeResArr["errcode"] != 0) return false;

        return $codeResArr["userid"];
    }


    public function getJsApiConfig() {
        $config = array(
            "agentId" => $this->agentId,
            "corpId" => $this->corpid,
            "timeStamp" => time(),
            "nonceStr" => rand_string(16, 2),
        );
        $ticket = $this->getJsTicket();

        $url = urldecode(Q()->getSchemeAndHttpHost() . Q()->getRequestUri());


        $config["signature"] = $this->sign($ticket, $config["nonceStr"], $config["timeStamp"], $url);
        $config["jsApiList"] = array(
            "runtime.permission.requestAuthCode", //获取免登授权码code
            "channel.permission.requestAuthCode", //获取服务窗免登授权码code
            "biz.contact.choose", //选人
            "biz.contact.chooseMobileContacts", //选择手机联系人
            "biz.user.get", //获取用户信息
            "biz.util.uploadImage", //上传图片
            "biz.ding.create", //发钉
            "biz.ding.post", //发ding消息
            "biz.telephone.call", //拨打钉钉电话
            "biz.telephone.showCallMenu", //通用电话拨打接口
            "biz.telephone.checkBizCall", //检查某企业下的办公电话开通状态
            "biz.telephone.quickCallList", //拨打单人电话选项（可定制）
            "biz.contact.createGroup", //创建群聊
            "biz.map.locate", //定位到地图页面
            "biz.map.search", //地图页面支持搜索
            "biz.map.view", //地图预览
            "biz.util.uploadImageFromCamera", //拍照上传附件
            "biz.customContact.multipleChoose", //多选自定义选人
            "biz.customContact.choose", //单选自定义选人
            "biz.contact.complexPicker", //选人选部门
            "biz.contact.complexChoose", //选人选部门（不再维护）
            "biz.contact.departmentsPicker", //选部门
            "biz.contact.setRule", //设置选人规则
            "biz.contact.externalComplexPicker", //选取外部联系人
            "biz.contact.externalEditForm", //编辑外部联系人
            "biz.chat.pickConversation", //获取会话信息
            "biz.intent.fetchData", //在聊天页面，用户通过聊天消息跳转到微应用，用于获取用户所选择消息及会话内容信息，并在微应用页面显示
            "biz.chat.chooseConversationByCorpId", //通过corpid选取会话
            "biz.chat.openSingleChat", //打开单聊会话
            "biz.chat.toConversation", //根据chatid跳转到对应会话
            "biz.cspace.saveFile", //保存钉盘文件
            "biz.cspace.preview", //预览钉盘文件
            "biz.cspace.chooseSpaceDir", //选取钉盘目录
            "biz.util.uploadAttachment", //上传钉盘文件
            "biz.clipboardData.setData", //复制到粘贴板
            "biz.chat.locationChatMessage", //打开聊天详情到某条消息
            "device.audio.startRecord", //开始录音
            "device.audio.stopRecord", //结束录音
            "device.audio.onRecordEnd", //录音结束
            "device.audio.download", //下载录音
            "device.audio.play", //播放录音
            "device.audio.pause", //暂停播放语音
            "device.audio.resume", //录音播放恢复
            "device.audio.stop", //录音播放停止
            "device.audio.onPlayEnd", //录音播放停止
            "device.audio.translateVoice", //语音转文字
            "biz.alipay.pay", //支付接口支付
            "device.nfc.nfcWrite", //NFC数据写入
            "biz.util.encrypt", //数据加密
            "biz.util.decrypt", //数据解密
            "runtime.permission.requestOperateAuthCode", //获取发送响应式消息Code
            "biz.util.scanCard", //扫名片
            "device.screen.rotateView", //旋转屏幕视图到横屏状态，并隐藏页面导航栏
            "device.screen.resetView", //重置屏幕状态
        );
        return $config;
    }

    public function getPCJsApiConfig() {
        $config = array(
            "agentId" => $this->agentId,
            "corpId" => $this->corpid,
            "timeStamp" => time(),
            "nonceStr" => rand_string(16, 2),
        );
        $ticket = $this->getJsTicket();

        $url = urldecode(Q()->getSchemeAndHttpHost() . Q()->getRequestUri());


        $config["signature"] = $this->sign($ticket, $config["nonceStr"], $config["timeStamp"], $url);
        $config["jsApiList"] = array(
            "device.notification.alert", //alert弹窗
            "device.notification.confirm", //confirm弹窗
            "device.notification.prompt", //prompt弹窗
            "device.notification.toast", //toast弹窗
            "device.notification.actionSheet", //actionSheet弹窗
            "biz.navigation.setLeft", //设置左侧导航按钮文案，只在SlidePanel里起作用
            "biz.navigation.setTitle", //发钉
            "biz.navigation.quit", //触发关闭，只在SlidePanel和Modal里起作用
            "runtime.permission.requestAuthCode", //获取免登授权码
            "runtime.permission.requestOperateAuthCode", //获取微应用反馈式操作的临时授权码
            "biz.cspace.preview", //预览钉盘文件
            "biz.util.open", //打开应用内页面
            "biz.util.openModal", //	打开模态框
            "biz.util.openSlidePanel", //打开侧边面板
            "biz.util.previewImage", //预览图片
            "biz.util.openLink", //在浏览器上打开链接
            "biz.util.downloadFile", //下载文件
            "biz.util.openLocalFile", //打开调用下载文件下载后的本地文件
            "biz.util.isLocalFileExist", //批量检测下载的文件是否存在
            "biz.util.ut", //提供的数据埋点能力
            "biz.util.uploadImage", //上传图片
            "biz.ding.post", //发送Ding
            "biz.contact.choose", //选择企业内的人
            "biz.customContact.choose", //单选自定义选人列表
            "biz.customContact.multipleChoose", //多选自定义选人列表
            "biz.contact.externalComplexPicker", //选择外部联系人
            "biz.user.get", //通用JSAPI接口获取用户信息
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


    public function getAccessToken($flush = false) {
        $accessTokenPath = Main()->getRuntime() . "/Temp/AccessToken/DingAccessToken_{$this->sid}.php";
        $this->createDir(dirname($accessTokenPath));

        $accessToken = array();

        if (!$flush) {
            if (is_file($accessTokenPath)) {
                $accessToken = include $accessTokenPath;
            }
            if ($accessToken && $accessToken["timeout"] > time()) return $accessToken["accessToken"];
        }

        $url = 'https://oapi.dingtalk.com/gettoken?corpid=' . $this->corpid . '&corpsecret=' . $this->corpsecret;

        $res = curl($url);
        if (!$res) {
            loginfo("accessToken", $url);
            return "";
        }

        $resArr = json_decode($res, true);

        if (!$resArr || $resArr["errcode"] != 0) {
            loginfo("accessToken", strval(__LINE__) . $res);
            return "";
        }

        $accessToken = array(
            "timeout" => time() + 5400,
            "accessToken" => $resArr["access_token"]
        );

        $content = "<?php return " . var_export($accessToken, true) . ";";

        file_put_contents($accessTokenPath, $content);

        return $resArr["access_token"];
    }

    public function getJsTicket() {

        $JsTicketPath = Main()->getRuntime() . "/Temp/AccessToken/DingJsTicket_{$this->sid}.php";
        $this->createDir(dirname($JsTicketPath));
        $JsTicket = array();

        if (is_file($JsTicketPath)) {
            $JsTicket = include $JsTicketPath;
        }
        if ($JsTicket && $JsTicket["timeout"] > time() && $JsTicket["ticket"]) return $JsTicket["ticket"];

        $url = "https://oapi.dingtalk.com/get_jsapi_ticket?access_token=" . $this->getAccessToken();
        $res = curl($url);
        if (!$res) {
            loginfo("getJsTicket","getJsTicket");
            return "";
        }
        loginfo("getJsTicketRes",$res);
        $resArr = json_decode($res, true);

        $accessToken = array(
            "timeout" => time() + 5400,
            "ticket" => $resArr["ticket"]
        );

        $content = "<?php return " . var_export($accessToken, true) . ";";

        file_put_contents($JsTicketPath, $content);

        return $resArr["ticket"];

    }


    private function createDir($dir) {
        if (is_dir($dir)) return;
        $parentDir = dirname($dir);
        if (!is_dir($parentDir)) $this->createDir($parentDir);
        mkdir($dir, 0777, true);
        chmod($dir, 0777);
    }


}
