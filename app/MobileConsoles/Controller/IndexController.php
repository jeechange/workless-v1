<?php

namespace MobileConsoles\Controller;

use Admin\DModel\AcornAuditDModel;
use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\TaskAllotDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WelfareLuckyDModel;
use Admin\DModel\WelfareOrderDModel;
use Admin\DModel\WelfareRewardDModel;
use Admin\Entity\Task;
use Home\Service\PageService;
use Jeechange\SDK\DingSDK;

class IndexController extends CommonController {

    public function index() {
        //控制器示例代码
        $this->flushUser();
        $acornDM = AcornDModel::getInstance();
        $lists = $acornDM->name("a")
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("Standard", "s", "s.id = a.names")
            ->select("a,u.fullName,s.names as sNames")
            ->where("a.status = 1 and a.sid = " . $this->sid)
            ->setMax(100)
            ->order("a.id", "DESC")
            ->getArray(true);
        foreach ($lists as $k => $v) {
            $lists[$k]['addTimes'] = $acornDM->time_tran(totime($v['a_addTime']));
        }
        //个人当月价值指数
        $RankingDM = new \Admin\DModel\RankingDModel();
        $year = date('Y');
        $month = date('m');
        $myValue = $RankingDM->name('rk')->where('rk.sid=' . $this->sid . ' AND rk.userId=' . $this->getUser("id") . " AND rk.year = " . $year . " AND rk.month = " . $month)->sum('rk.acorn');
        $myValue = $myValue ? $myValue : 0;

        $allValue = $RankingDM->name('rk')->where('rk.sid=' . $this->sid . " AND rk.year = '" . $year . "' AND rk.month = '" . $month . "'")->sum('rk.acorn');
        $allValue = $allValue ? $allValue : 0;

        $memberDM = new \Admin\DModel\CompanyMemberDModel();
        $mwhere = 'm.sid=' . $this->sid . ' AND m.acorn>0';
        $num = $memberDM->name('m')->where($mwhere)->count();
        $num = $num ? $num : 1;
        $memberValue = intval($allValue / $num);
        if ($memberValue) {
            $valueIndex = round($myValue * 100 / $memberValue, 2);
        } else {
            $valueIndex = 0;
        }

//        dump($allValue);die;
        $this->assign("valueIndex", $valueIndex);
        $this->assign("lists", $lists);
        $this->assign("isSuper", $this->isSuper());

        return $this->display();
    }

    public function query() {
        $taskDM = TaskDModel::getInstance();
        $post = Q()->post->all();

        $userId = $this->getUser("id") ?: 0;
        $keywords = Q()->post->get("keyword") ?: "";
        $page = Q()->post->get("page") ?: 0;

        $where = "t.types=2 and t.sid =" . $this->sid;
        $params = array();
        if ($keywords) {
            $where .= " and (search LIKE :search)";
            $params["search"] = "%" . $keywords . "%";
            unset($params['keywords']);
            $whereSelect = $taskDM->userSelect("vendor", $userId, $this->sid, $where, $params, 1);
            if ($whereSelect) {
                $where2 = "t.types=2 and t.sid =" . $this->sid;
                $where = "({$where2}) and {$whereSelect}";
                $this->assign("params", 1);
            }
        }

        $lists = $taskDM->name("t")->select("t,u,u2")
            ->leftJoin("User", "u", "u.id=t.issueId")
            ->leftJoin("User", "u2", "u2.id=t.acceptId")
            ->where($where)->setParameter($params)
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->page($page, 50)
            ->getArray(true);

        $zero = $post['zero'] ?: 0;
        if ($zero == 1) {
            $count = $taskDM->name("t")->select("t,u,u2")
                ->leftJoin("User", "u", "u.id=t.issueId")
                ->leftJoin("User", "u2", "u2.id=t.acceptId")
                ->where($where)->setParameter($params)
                ->count() ?: 0;
        } else {
            $count = 0;
        }

        if ($lists) {
            $status = "y";
            $info = "查询成功";
        } else {
            $status = "n";
            $info = "查询为空";
        }

        return $this->ajaxReturn(array("status" => $status, "info" => $info, "data" => $lists, "page" => $page + 1, "count" => $count));
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

    public function sharePage() {
        $get = Q()->get->all();

        $shareid = $get['share'];

        $shareDM = ShareDModel::getInstance();
        $shareEN = $shareDM->name("s")->where("s.id={$shareid}")->getOneArray();

        $dingSDK = new DingSDK();
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->findOneBy(array("sid" => $this->sid, "namesEn" => "dingtalk"));
        if ($api) $dingSDK->initConfig($api);
        $this->initSDK();//初始化SDK

        $shareUrl = explode(",", $shareEN['shareUrl']);
        $shareUrl = $shareUrl[1];
        $shareUrl = $shareUrl . "?share=" . $shareid;

        $gobackUrl = explode(",", $shareEN['gobackUrl']);
        $gobackUrl = $gobackUrl[1];

//        $shareUrl = $shareEN['shareUrl'] . "?share=" . $shareid;
        $content = $shareEN['content1'] . $shareEN['content2'];
        $this->assign([
            "content" => $content,
            "shareUrl" => $shareUrl,
            "gobackUrl" => $gobackUrl,
            "contentAndShareUrl" => $content . " 点击链接查看详情：" . $shareUrl,
            "corpId" => $dingSDK->corpid,
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

        return $this->display();
    }

    public function share() {
        $get = Q()->get->all();
        $eventId = $get['eventid'];
        $template = $get['template'];

        switch ($template) {
            case 'GET_GIFT':
                $dm = WelfareRewardDModel::getInstance();
                break;
            case 'GET_LUCKY':
                $dm = WelfareLuckyDModel::getInstance();
                break;
            case 'RELEASE_TASK':
                $dm = TaskDModel::getInstance();
                break;
            default:
                $dm = AcornAuditDModel::getInstance();
                break;
        }

        $shareDM = ShareDModel::getInstance();
        $shareEN = $shareDM->name("s")->where("s.userId={$this->getUser('id')} and s.eventId={$eventId} and s.sid={$this->sid}")->getOneArray();

        if (!$shareEN) {
            $shareid = $shareDM->chooseTemplate($template, $dm, $eventId, $this->sid, $this->getUser('id'));
        } else {
            $shareid = $shareEN['id'];
        }

        if ($shareid > 0) {
            return $this->redirect(url("~mobileConsoles_index_sharePage", array("share" => $shareid)));
        }
        return $this->success("未找到分享数据", url("~mobileConsoles_index_index"));
    }

}