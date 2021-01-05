<?php

namespace Consoles\Controller;


use Admin\DModel\AcornDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\UserDModel;

class ShareController extends CommonController {

    public function sharePage() {

        $shareDM = ShareDModel::getInstance();

        $get = Q()->get->all();

        $curShare = $shareDM->name("s")->where("s.id={$get['share']}")->getOneArray();

        if($this->isMobile()){
            $shareUrl = explode(",",$curShare['shareUrl']);
            return $this->redirect($shareUrl[1]."?share={$get['share']}");
        }

        if(!$curShare['acornAdded']){

            $standardDM = StandardDModel::getInstance();
            $standard = $standardDM->name("s")->where("s.names='分享申请事项' and s.sid={$curShare['sid']} and s.status=1")->getOneArray();
            $acornDM = AcornDModel::getInstance();
            $result = $acornDM->addAcorn($curShare['sid'], $curShare['userId'], $curShare['userId'], $curShare['userId'], $standard['classify'], $standard['id'], $standard['acorn'], "分享申请事项", "分享申请事项");
            if (!$result) {
                return $this->error($acornDM->getError());
            }

            $modifyCurShare = $shareDM->find($get['share']);
            if(!$modifyCurShare){
                return $this->error("找不到分享数据");
            }
            $modifyCurShare->setAcornAdded(1);
            $shareDM->save($modifyCurShare)->flush();
        }

        $terminal = explode(",",$curShare['gobackUrl']);
//        if($this->isMobile()){
//            $gobackUrl = $terminal[1];
//        }else{
//            $gobackUrl = $terminal[0];
//        }
//        $gobackUrl = $terminal[0]."&share={$curShare['id']}&terminal=isMobile";
        $gobackUrl = $terminal[0];

        $this->assign([
            "curShare" => $curShare,
            "userid" => $this->getUser('id'),
            "gobackUrl" => $gobackUrl
        ]);

        return $this->display();

    }

    public function sharePageTask() {

        $shareDM = ShareDModel::getInstance();

        $get = Q()->get->all();

        $curShare = $shareDM->name("s")->where("s.id={$get['share']}")->getOneArray();

        if($this->isMobile()){
            $shareUrl = explode(",",$curShare['shareUrl']);
            return $this->redirect($shareUrl[1]."?share={$get['share']}");
        }

        if (!$curShare['acornAdded']) {

            $standardDM = StandardDModel::getInstance();
            $standard = $standardDM->name("s")->where("s.names='分享申请事项' and s.sid={$curShare['sid']} and s.status=1")->getOneArray();
            $acornDM = AcornDModel::getInstance();
            $result = $acornDM->addAcorn($curShare['sid'], $curShare['userId'], $curShare['userId'], $curShare['userId'], $standard['classify'], $standard['id'], $standard['acorn'], "分享申请事项", "分享申请事项");
            if (!$result) {
                return $this->error($acornDM->getError());
            }

            $modifyCurShare = $shareDM->find($get['share']);
            if (!$modifyCurShare) {
                return $this->error("找不到分享数据");
            }
            $modifyCurShare->setAcornAdded(1);
            $shareDM->save($modifyCurShare)->flush();
        }

        $taskDM = TaskDModel::getInstance();
        $curTask = $taskDM->name("t")->where("t.id={$curShare['eventId']}")->getOneArray();

        $userDM = UserDModel::getInstance();
        $executorsArr = explode(",",$curTask['executors']);
        $executors = "";
        foreach ($executorsArr as $k=>$v){
            $a = $userDM->find($v);
            if(!$a){
                $executors .= ',';
            }else{
                $executors .= $a->getFullName().',';
            }
        }
        $executors = rtrim($executors,",");

        $accept = $userDM->find($curTask['acceptId']);
        if(!$accept){
            $accept = '无';
        }else{
            $accept = $accept->getFullName();
        }

        $standardDM = StandardDModel::getInstance();
        if($curTask['standardId'] == 0){
            $acorn = "日基础分 × 难度系数 × 完成质量 × 天数";
        }else{
            $standardEN = $standardDM->find($curTask['standardId']);
            if(!$standardEN){
                $acorn = "暂无";
            }else{
                $acorn = $standardEN->getAcorn();
            }
        }

        $curTask['deadline'] = totime($curTask['deadline'],"Y-m-d H:i:s");
        $curTask['types'] = $taskDM->typesMemo[$curTask['types']];

        $terminal = explode(",",$curShare['gobackUrl']);
//        if($this->isMobile()){
//            $gobackUrl = $terminal[1];
//        }else{
//            $gobackUrl = $terminal[0];
//        }
//        $gobackUrl = $terminal[0]."&share={$curShare['id']}&terminal=isMobile";
        $gobackUrl = $terminal[0];

        $this->assign([
            "curTask" => $curTask,
            "executors" => $executors,
            "accept" => $accept,
            "acorn" => $acorn,
            "curShare" => $curShare,
            "gobackUrl" => $gobackUrl
        ]);

        return $this->display();

    }

    public function shareDetail(){

        $post = Q()->post->all();

        if($this->isMobile()){
            $terminal = 'isMobile';
            return $this->ajaxReturn(array("status"=>"y","url"=>url('~mobileConsoles_login',array('share'=>$post['shareid'],'terminal'=>$terminal))));
        }


        $userAgent = Q()->headers->get("user-agent");
        if (!preg_match("#DingTalk#", $userAgent)) {
            if(!$this->getUser('id')){
                $terminal = 'isPc';
                return $this->ajaxReturn(array("status"=>"y","url"=>url('~consoles_login_login',array('share'=>$post['shareid'],'terminal'=>$terminal))));
            }
        }

        return $this->ajaxReturn(array("status"=>"y","url"=>$post['gobackurl']));
    }

    public function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

}