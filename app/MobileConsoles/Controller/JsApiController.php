<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/9/28
 * Time: 17:33
 */

namespace MobileConsoles\Controller;


use Jeechange\SDK\DingSDK;

class JsApiController extends CommonController {

    public function dingGetUserInfo() {

        $DingSDK = new DingSDK();

        $accessToken =$DingSDK->getAccessToken();
        dump($accessToken);
        exit;


        $url = 'https://oapi.dingtalk.com/gettoken?corpid=ding97b01f7440a7146b35c2f4657eb6378f&corpsecret=00qWs8sHpnP17F6El5TSSRAzvQjeuKH9joLNQhsoH0NKGBnhfg_MjxeEOCsDAhEF';


        $res = curl($url);


        $userCodeUrl = "https://oapi.dingtalk.com/user/getuserinfo?access_token=ACCESS_TOKEN&code=CODE";

        $userUrl = "https://oapi.dingtalk.com/user/get?access_token=ACCESS_TOKEN&userid=zhangsan";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch); //执行并获取HTML文档内容

        $error = curl_error($ch);
        curl_close($ch);

        dump($output);
        dump($error);
        exit;


//        include Main()->getAppRoot() . "/Jeechange/Sdk/ding/TopSdk.php";
//
//        $url = 'https://oapi.dingtalk.com/user/getuserinfo?access_token=ACCESS_TOKEN&code=CODE';
//        $accessTokenUrl = 'https://oapi.dingtalk.com/service/get_corp_toke';
//        $accessTokenUrl = 'https://oapi.dingtalk.com/service/get_corp_toke';
//
//        $client = new \DingTalkClient(\DingTalkConstant::$CALL_TYPE_OAPI, \DingTalkConstant::$METHOD_GET,"json");
//
//        $req = new \OapiGettokenRequest();
//        $req->getApiMethodName();
//        $req->setCorpid("ding97b01f7440a7146b35c2f4657eb6378f");
//        $req->setCorpsecret("00qWs8sHpnP17F6El5TSSRAzvQjeuKH9joLNQhsoH0NKGBnhfg_MjxeEOCsDAhEF");
//        $res = $client->executeWithAccessKey($req,  $accessTokenUrl,"ding97b01f7440a7146b35c2f4657eb6378f","00qWs8sHpnP17F6El5TSSRAzvQjeuKH9joLNQhsoH0NKGBnhfg_MjxeEOCsDAhEF");
//        dump($res);
//        exit;

    }

}
