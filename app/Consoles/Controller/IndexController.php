<?php

namespace Consoles\Controller;

use Admin\DModel\AcornAuditDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WelfareLuckyDModel;
use Admin\DModel\WelfareOrderDModel;
use Admin\DModel\WelfareRewardDModel;
use Admin\DModel\WelfareSnackDModel;
use Admin\Entity\Company;
use Admin\Entity\CompanyOpenapi;
use Admin\Entity\Share;
use Admin\Entity\Task;
use Consoles\Service\MenuService;
use Admin\DModel\ShareDModel;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Jeechange\SDK\DingSDK;

class IndexController extends CommonController {

    public function index() {

        if($this->getUser("sid")==0){
            return $this->display("Login/guide.latte");
        }

        //检测收费账户是否到期
        $company = CompanyDModel::getINstance()->findOneBy(array("id"=>$this->sid));
        //计算离过期的天数
        $thisTime = date("Y-m-d H:i:s");
        $remainDay=floor((strtotime(totime($company->getExpireTime()))-strtotime($thisTime)));
        if($remainDay<=0){
            return $this->display("renew.latte");
        }

        if (Q()->server->has("HTTP_AJAX")) {
            return $this->dashboard();
        }
        $menuService = new MenuService($this->access);

        $menuService->isSuperTypes_1 = $this->isSuperTypes(1);
        $menuService->isSuperTypes_2 = $this->isSuperTypes(2);
        $menuService->isSuperTypes_3 = $this->isSuperTypes(3);

        $menu = "menu.yml";
        if ($this->getUser('sid') == 0) {
            $menu = "erMenu.yml";
        }
        $menus = RbacAccessDModel::BuildMenu($menuService, $this->isSuper(), "", $menu);
        $this->assign("user", $this->getUser());
        $this->assign("menus", $menus);
        $this->assign("isSuperTypes", $this->isSuperTypes());

        $userAgent = Q()->headers->get("user-agent");
        if (preg_match("#DingTalk#", $userAgent)) {
            $this->dingSDKinit();
        }
        return $this->display();
    }
    public function indexMenu($menu) {
        if (Q()->server->has("HTTP_AJAX")) {
            return $this->dashboard();
        }

        $menuService = new MenuService($this->access);

        $menuService->isSuperTypes_1 = $this->isSuperTypes(1);
        $menuService->isSuperTypes_2 = $this->isSuperTypes(2);
        $menuService->isSuperTypes_3 = $this->isSuperTypes(3);
        $this->assign("topMenuActive", $menu);
        if ($this->getUser('sid') == 0) {
            $this->redirect("consoles_login_login");
        } elseif($menu) {
            $menu = "menu-{$menu}.yml";
        }else{
            $menu="menu.yml";
        }
        $menus = RbacAccessDModel::BuildMenu($menuService, $this->isSuper(), "", $menu);
        $this->assign("user", $this->getUser());
        $this->assign("menus", $menus);
        $this->assign("isSuperTypes", $this->isSuperTypes());

        $userAgent = Q()->headers->get("user-agent");
        if (preg_match("#DingTalk#", $userAgent)) {
            $this->dingSDKinit();
        }

        $companyDM = CompanyDModel::getInstance();
        $sid = $this->getUser("sid") ?: 0;

        /** @var Company $company */
        $company = $companyDM->find($sid);
        if ($company) {
            $apps = explode(",", $company->getApps());
            $this->assign("apps", $apps);
        } else {
            $this->assign("apps", array());
        }

        $this->assign("allApps", $companyDM->apps);
        $this->assign("company", $company);
        $this->assign("isSuper", $this->isSuperTypes(3));

        return $this->display("index");
    }
    /**首页
     * @return \phpex\Foundation\Response
     */
    public function dashboard() {
//        $m = new \MongoClient(); // 连接默认主机和端口为：mongodb://localhost:27017
//        $db = $m->test; // 获取名称为 "test" 的数据库
//        dump($db);
//        exit;

//        function request_by_curl($remote_server, $post_string) {
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $remote_server);
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
//            // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
//            // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
//
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
//            $data = curl_exec($ch);
//            curl_close($ch);
//            return $data;
//        }
//
//        $webhook = "https://oapi.dingtalk.com/robot/send?access_token=c926e3714cbb66d8d6b4ebade8f53ae313da6c52eebf62f84dcc03c95266ef0e";
//        $message = "我就是我, 是不一样的烟火";
//        $data = array('msgtype' => 'text',
//            'text' => array(
//                'content' => $message,
//            ),
//            "at" => array(
//                "atMobiles" => array("13635107649", "18977141727"),
//                "isAtAll" => true
//            )
//        );
//        $data_string = json_encode($data);
//
//        $result = request_by_curl($webhook, $data_string);
//        echo $result;
//
//        exit;

        $get = Q()->get->all();
        $rankingDM = new \Admin\DModel\RankingDModel();

        //排名
        $types = $get['types'] ?: 0;
        switch ($types) {
            case 0:
                $lists = $rankingDM->weekRanking($this->sid);//周排
                $atvice = "week";
                break;
            case 1:
                $lists = $rankingDM->monthRanking($this->sid);//月排
                $atvice = "month";
                break;
            case 2:
                $lists = $rankingDM->yearRanking($this->sid);//年排、
                $atvice = "year";
                break;
            case 3:
                $lists = $rankingDM->totalRanking($this->sid);//总排
                $atvice = "total";
                break;
            default:
                $lists = $rankingDM->weekRanking($this->sid);
                $atvice = "week";
        }
        $rankingNames = array();
        $rankingVlues = array();
        if ($lists) {
            foreach ($lists as $item) {
                $rankingNames[] = $item['userName'];
                $rankingVlues[] = $item['acorn'];
            }
        } else {
            $rankingNames[] = "暂无数据";
            $rankingVlues[] = 0;
        }

        //统计
        $countTypes = $get['countTypes'] ?: 1;
        $taskStatisticsDM = new \Admin\DModel\TaskStatisticsDModel();
        $count = $taskStatisticsDM->taskStatistics($countTypes, $this->sid);
        $countNames = array();
        $countVlues = array();
        $countVlues1 = array();
        $countVlues2 = array();

        if ($count) {
            foreach ($count as $key => $item) {
                $countNames[] = $item['userName'];
                $countVlues[] = array("value" => $item['coefficientAverage']);//平均难度
                $countVlues1[] = array("value" => $item['qualityAverage']);//平均质量
                $countVlues2[] = array("value" => $item['realWl']);//任务量
            }
        } else {
            $countNames[] = "暂无数据";
            $countVlues[] = array("value" => 0, "name" => "暂无数据");
            $countVlues1[] = array("value" => 0, "name" => "暂无数据");
            $countVlues2[] = array("value" => 0, "name" => "暂无数据");
        }

        //账号信息

        $companyMemberDM = new \Admin\DModel\CompanyMemberDModel();
        $where = "c.userId=" . $this->getUser("id") . " AND u.status=1 AND c.sid=" . $this->sid . " AND c.status=1";
        $companyMember = $companyMemberDM->name("c")
            ->select("c,u.fullName as u_fullName")
            ->leftJoin("User", "u", "u.id=c.userId")
            ->where($where)
            ->getOneArray(true);

        //本月排名
        $monthRanking = $rankingDM->monthRanking($this->sid);
        $myRanking = array(
            "ranking" => 0
        );
        foreach ($monthRanking as $key => $item) {
            if ($item['userId'] == $this->getUser("id")) {
                $myRanking['ranking'] = $key + 1;
            }
        }
        //todo总数
        $todoDM = new \Admin\DModel\TodoDModel();
        $todoWhere = "t.userId=" . $this->getUser("id") . " AND t.sid=" . $this->sid . " AND t.status=0 AND t.types=1";
        $todoCount = $todoDM->name("t")->select()->where($todoWhere)->count();
        $AtodoCount = $todoDM->name("t")->select()->where($todoWhere . " AND t.priority=1")->count();//A类型任务

        //本月积分
        $monthAcorn = $rankingDM->monthAcorn($this->sid, $this->getUser("id"));

        $this->assign("todoCount", $todoCount);
        $this->assign("myRanking", $myRanking);//本月排名
        $this->assign("AtodoCount", $AtodoCount);
        $this->assign("myTodo", $this->myTodo());//todo任务
        $this->assign("monthAcorn", $monthAcorn);//本月积分
        $this->assign("typesNames", array(1 => "赏", 2 => "临", 3 => "周"));
        $this->assign("user", $companyMember ?: 0);
        $this->assign("dynamics", $this->dynamics());
        $this->assign("rankingNames", $rankingNames);
        $this->assign("rankingVlues", $rankingVlues ?: array(array("value" => 0)));

        $this->assign("countNames", $countNames ?: array(array("value" => 0)));
        $this->assign("countVlues", $countVlues ?: array(array("value" => 0)));
        $this->assign("countVlues1", $countVlues1 ?: array(array("value" => 0)));
        $this->assign("countVlues2", $countVlues2 ?: array(array("value" => 0)));
        $this->assign("atvice", $atvice);
        $this->assign("countatvice", $countTypes);

        return $this->display("dashboard");
    }

    /**平均积分
     * @param $userId
     * @return \array[]
     */
    public function avgNum() {
        $standardClassifyDM = new \Admin\DModel\StandardClassifyDModel();
        $rankingDM = new \Admin\DModel\RankingDModel();
        $userDM = new \Admin\DModel\UserDModel();
        $lists = $standardClassifyDM->name("sc")->select("sc")->where("sc.pid=0 AND sc.status=1")->select("sc")->getArray();

        foreach ($lists as $key => &$values) {
            $where = "rk.sid=:sid AND rk.pId=:pId";
            $parameter = array(
                "sid" => $this->sid,
                "pId" => $values['id'],
            );

            $rankingLists = $rankingDM->name("rk")
                ->leftJoin("User", "u", "u.id=rk.userId")
                ->select("sum(rk.acorn) as acorn")
                ->where($where)
                ->setParameter($parameter)
                ->order("acorn", "DESC")
                ->groupBy("u.id")
                ->getArray();//每个人每个分类的总积分

            $max = array();
            foreach ($rankingLists as $i => $item) {
                $max[] = $item['acorn'];
            }
            $values['max'] = max($max) ? max($max) : '1';
            $ranking = $rankingDM->name("rk")
                ->leftJoin("User", "u", "u.id=rk.userId")
                ->select("sum(rk.acorn) as acorn")
                ->where($where)
                ->setParameter($parameter)
                ->order("acorn", "DESC")
                ->groupBy("rk.pId")
                ->getOneArray();//所有人每个分类的总积分

            $myRanking = $rankingDM->name("rk")
                ->leftJoin("User", "u", "u.id=rk.userId")
                ->select("sum(rk.acorn) as acorn")
                ->where($where . " AND rk.userId=" . $this->getUser('id'))
                ->setParameter($parameter)
                ->order("acorn", "DESC")
                ->groupBy("rk.pId")
                ->getOneArray();//个人每个分类的总积分

            $usercount = $userDM->name("u")->select('u')->where("u.sid=" . $this->sid)->count();
            $values['avgAcorn'] = round($ranking['acorn'] / $usercount) > 0 ? round($ranking['acorn'] / $usercount) : 0;//
            $values['myAcorn'] = round($myRanking['acorn']) > 0 ? round($myRanking['acorn']) : 0;
        }

        return $lists;
    }

    /**积分动态
     * @return \array[]
     */
    public function dynamics() {
        $acornDM = new \Admin\DModel\AcornDModel();
        $lists = $acornDM->name("a")
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("Standard", "s", "s.id = a.names")
            ->select("a,u.fullName,s.names as sNames")
            ->where("a.status = 1 and a.sid = " . $this->sid)
            ->limit(0, 100)
            ->order("a.id", "DESC")
            ->getArray(true);
        foreach ($lists as $k => $v) {
            $lists[$k]['addTimes'] = $acornDM->time_tran(totime($v['a_addTime']));
        }
        return $lists;
    }

    /**我的todo
     * @return \array[]
     */
    public function myTodo() {
        $todoDM = new \Admin\DModel\TodoDModel();
        $lists = $todoDM->name("t")
            ->select("t")
            ->where("t.sid=" . $this->sid . " AND t.userId=" . $this->getUser('id') . " AND t.types in(1,2,3) AND t.status=0")
            ->limit(0, 4)
            ->getArray();
        return $lists;
    }

    /**分享页
     * @return \phpex\Foundation\Response
     */
    public function sharePage() {
        $get = Q()->get->all();

        $shareid = $get['share'];

        $shareDM = ShareDModel::getInstance();
        $shareEN = $shareDM->name("s")->where("s.id={$shareid}")->getOneArray();
        $shareUrl = explode(",", $shareEN['shareUrl']);
        $shareUrl = $shareUrl[0];
        $shareUrl = $shareUrl . "?share=" . $shareid;

        $gobackUrl = explode(",", $shareEN['gobackUrl']);
        $gobackUrl = $gobackUrl[0];

        $content = $shareEN['content1'] . $shareEN['content2'];


        $this->assign([
            "shareid" => $shareid,
            "content" => $content,
            "shareUrl" => $shareUrl,
            "gobackUrl" => $gobackUrl,
            "contentAndShareUrl" => $content . " 点击链接查看详情：" . $shareUrl,
//            "corpId" => $dingSDK->corpid,
            "title" => $shareEN['templateName']
        ]);

        if ($shareEN["template"] == "RELEASE_TASK") {
            $taskDM = TaskDModel::getInstance();
            /** @var Task $task */
            $task = $taskDM->find($shareEN["eventId"] ?: 0);
            if ($task) {
                if ($task->getIssueId() == $this->getUser("id")) {
                    if (Q()->get->has("modify") && Q()->get->get("modify")) {
                        $content = str_replace("分享了", "修改了", $shareEN['content1']) . $shareEN['content2'];
                    } else {
                        $content = str_replace("分享了", "发布了", $shareEN['content1']) . $shareEN['content2'];
                    }
                }
                CompanyOpenapiDModel::sendMessage($task, $content . " 点击链接查看详情：" . $shareUrl, true);
            }
        }


//        $apiDM = CompanyOpenapiDModel::getInstance();
//        $lists = $apiDM->name("a")->where("a.sid=:sid and a.namesEn = :namesEn")->setParameter(array("sid" => $this->sid, "namesEn" => "dingwebhook"))->getArray();
        // $this->assign("lists", $lists);


        return $this->display();

    }


    public function share() {
        $post = Q()->post->all();
        $eventId = $post['eventid'];
        $template = $post['template'];

        switch ($template) {
            case 'APPLY_INFLUENCE':
                $dm = AcornAuditDModel::getInstance();
                break;
            case 'APPLY_STANDARD':
                $dm = StandardDModel::getInstance();
                break;
            case 'GET_GIFT':
                $dm = WelfareRewardDModel::getInstance();
                break;
            case 'GET_LUCKY':
                $dm = WelfareLuckyDModel::getInstance();
                break;
            case 'EXCHANGE_SNACK':
                $dm = WelfareOrderDModel::getInstance();
                break;
            case 'RELEASE_TASK':
                $dm = TaskDModel::getInstance();

                break;
            default:
                $dm = AcornAuditDModel::getInstance();
                break;
        }

        $shareDM = ShareDModel::getInstance();
        $shareEN = $shareDM->name("s")->where("s.userId={$this->getUser('id')} and s.eventId={$eventId} and s.sid={$this->sid} and s.template='{$template}' and  s.status=1")->getOneArray();

        if (!$shareEN) {
            $shareid = $shareDM->chooseTemplate($template, $dm, $eventId, $this->sid, $this->getUser('id'));
            $shareEN = $shareDM->name("s")->where("s.id={$shareid}")->getOneArray();
        } else {
            $shareid = $shareEN['id'];
        }

        $shareUrl = explode(",", $shareEN['shareUrl']);
        $shareUrl = $shareUrl[0];
        $shareUrl = $shareUrl . "?share=" . $shareid;
        $content = $shareEN['content1'] . $shareEN['content2'];
        return $this->ajaxReturn(array("status" => "y", "info" => "复制成功", "url" => $content . " 点击链接查看详情：" . $shareUrl));
    }

    public function toTask() {
        $post = Q()->post->all();
        $taskDM = TaskDModel::getInstance();
        /** @var Task $taskEN */
        $taskEN = $taskDM->name('t')->where("t.codeNo = '{$post['codeNo']}' and t.sid = {$this->sid}")->order("t.cycleUse", "DESC")->getOneObject();

        if (!$taskEN) {
            return $this->ajaxReturn(array('status' => 'n', 'info' => '未找到任务'));
        }
        $userId = $this->getUser("id");

        $canTo = $userId == $taskEN->getIssueId() || $userId == $taskEN->getAcceptId();

        if (!$canTo) {
            $executors = explode(",", $taskEN->getExecutors());
            $canTo = in_array($userId, $executors);
        }

        if (!$canTo && !$this->isSuperTypes()) {
            return $this->ajaxReturn(array('status' => 'n', 'info' => '您未参与此任务'));
        }


        return $this->ajaxReturn(array('status' => 'y', 'info' => '正在跳转', 'url' => url("consoles_task_details", array('id' => $taskEN->getId()))));
    }

    public function shareDingWebHook() {
        $post = Q()->post->all();

        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi[] $apis */
        $apis = $apiDM->findBy(array("namesEn" => "dingwebhook", "sid" => $this->sid, "status" => 1));
        if (!$apis) {
            return $this->ajaxReturn(array('status' => 'n', 'info' => "机器人获取失败"));
        }
        $shareDModel = ShareDModel::getInstance();

        /** @var Share $share */

        $share = $shareDModel->find($post["shareid"] ?: 0);
        if (!$share) {
            return $this->ajaxReturn(array('status' => 'n', 'info' => "记录获取失败"));
        }
        // $webhook = "https://oapi.dingtalk.com/robot/send?access_token=c926e3714cbb66d8d6b4ebade8f53ae313da6c52eebf62f84dcc03c95266ef0e";

        $userDM = UserDModel::getInstance();

        $userIds = array_merge(array($share->getUserId()), explode(",", $share->getToUser()));
        if ($share->getTemplate() == "RELEASE_TASK") {
            $taskDM = TaskDModel::getInstance();
            /** @var Task $task */
            $task = $taskDM->find($share->getEventId() ?: 0);
            if ($task) {
                $executors = explode(",", $task->getExecutors());
                $executors[] = $task->getIssueId();
                $executors[] = $task->getAcceptId();
                $userIds = array_merge($userIds, $executors);
                $userIds = array_filter($userIds);
                $userIds = array_unique($userIds);
            }
        }
        $users = $userDM->name("u")->select("u.phone")->where("u.id in (:userIds)")
            ->setParameter("userIds", $userIds)->getArray(false, false);
        $phones = array();
        foreach ($users as $user) {
            $phones[] = $user["phone"];
        }
        //$apiDM->dingWebHookLink($webhook, $share->getContent1(), $post["content"], $post["shareUrl"]);
        //sleep(1);
        foreach ($apis as $api) {
            if ($phones) $apiDM->dingWebHookText($api->getCorpsecret(), $post["contentAndShareUrl"], $phones);
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "消息已经发送成功"));

    }

    public function waitingForDevelopment() {
        $get = Q()->get->all();

        $this->assign("con", $get['con'] ?: "Index");

        $title = "";
        if ($get['types'] == "xingwei") {
            $title = "行为";
        } elseif ($get['types'] == "huibao") {
            $title = "汇报";
        } elseif ($get['types'] == "yeji") {
            $title = "业绩";
        } elseif ($get['types'] == "wodemubiao") {
            $title = "我的目标";
        } elseif ($get['types'] == "bumenmubiao") {
            $title = "目标管理";
        } elseif ($get['types'] == "gongsimubiao") {
            $title = "目标管理";
        } elseif ($get['types'] == "yuanjingshiming") {
            $title = "目标管理";
        } elseif ($get['types'] == "mubiaoguanli") {
            $title = "目标管理";
        } elseif ($get['types'] == "kaoheguanli") {
            $title = "考核管理";
        } elseif ($get['types'] == "renwu") {
            $title = "任务";
        } elseif ($get['types'] == "jiazhikaohe") {
            $title = "价值考核";
        } elseif ($get['types'] == "4") {
            $title = "目标管理";
        } elseif ($get['types'] == "jiazhizhishu") {
            $title = "价值指数";
        }

        $this->assign("title", $title);
        $this->assign("types", $get['types']);

        return $this->display();
    }

    /**
     * 个人价值指数
     */
    public function valueIndex() {
        $RankingDM = new \Admin\DModel\RankingDModel();
        $where = "rk.sid=:sid AND rk.userId=:userId";
        $parameter = array(
            "sid" => $this->sid,
            "userId" => $this->getUser("id"),
        );
        $myValue = $RankingDM->name('rk')->select("sum(rk.acorn) as acorn,rk.month,rk.year")->where($where)->setParameter($parameter)->order("rk.year", "DESC")->order("rk.month", "DESC")->groupBy("rk.month")->getArray();
        $myValue = $myValue ? array_slice($myValue, 0, 12) : $myValue;

        $awhere = "rk.sid=:sid";
        $aparameter = array(
            "sid" => $this->sid
        );
        $allValue = $RankingDM->name('rk')->select("sum(rk.acorn) as acorn,rk.month,rk.year")->where($awhere)->setParameter($aparameter)->order("rk.year", "DESC")->order("rk.month", "DESC")->groupBy("rk.month")->getArray();
        $allValue = $allValue ? array_slice($allValue, 0, 12) : $allValue;

        $memberDM = new \Admin\DModel\CompanyMemberDModel();
        $mwhere = 'm.sid=' . $this->sid . ' AND m.acorn>0';
        $num = $memberDM->name('m')->where($mwhere)->count();
        $num = $num ? $num : 1;
//        dump($myValue);die;
        $valueIndex = [];
        $month = [];
        foreach ($allValue as $k => &$v) {
            $v['acorn'] = intval($v['acorn'] / $num);
            $pig = 0;
            foreach ($myValue as $kk => $vv) {
                if ($v['year'] == $vv['year'] && $v['month'] == $vv['month']) {
                    $valueIndex[] = round($vv['acorn'] * 100 / $v['acorn'], 2);
                    $pig = 1;
                }
            }
            $month[] = $v['month'];
            if (!$pig) $valueIndex[] = 0;
        }
        $month = array_reverse($month);
        $valueIndex = array_reverse($valueIndex);

        $totalAvg = $this->avgNum();
        $myavg = array();
        $avg = array();
        foreach ($totalAvg as $key => $item) {
            $myavg[] = $item['myAcorn'];
            $avg[] = $item['avgAcorn'];
            $max[] = array("name" => $item['names'], "max" => $item['max'] > 0 ? $item['max'] : 0);
        }
        $this->assign("myavg", $myavg);
        $this->assign("avg", $avg);
        $this->assign("max", $max);

        $this->assign('month', $month);
        $this->assign('valueIndex', $valueIndex);
        return $this->display();
    }

}
