<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2018/8/20
 * Time: 11:43
 */

namespace MobileConsoles\Controller;


use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyMemberDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\SettingsDModel;
use Admin\DModel\SmsDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\UserDModel;
use Admin\DModel\WelfareBonusDModel;
use Admin\DModel\WelfareChatDModel;
use Admin\DModel\WelfareLuckyDModel;
use Admin\DModel\WelfareOrderDModel;
use Admin\DModel\WelfareRewardDModel;
use Admin\DModel\WelfareSettingsDModel;
use Admin\DModel\WelfareSnackDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\WelfareVoucherDModel;
use Admin\Entity\Company;
use Admin\Entity\User;

class WelfareController extends CommonController {
    /*
     * 物资奖
     */
    public function lists() {
        $this->assign("tabs_sub", "lists");
        $welfareRewardDM = WelfareRewardDModel::getInstance();
        $lists = $welfareRewardDM->name("wr")
            ->leftJoin("User", "u", "u.id = wr.userId")
            ->select("wr,u.fullName")
            ->where("wr.sid = " . $this->sid . " and wr.types = 1")
            ->order("wr.id", "DESC")
            ->getArray(true);
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        foreach ($lists as $k => $v) {
            if ($v['wr_wvId']) {
                $lists[$k]['wv'] = $welfareVoucherDM->name('wv')->where("wv.id = " . $v['wr_wvId'])->getOneArray();
            } else {
                $lists[$k]['wv'] = '';
            }
        }
        $this->assign("lists", $lists);
        $this->assign("isSuper", $this->isSuper());
        return $this->display();
    }

    /*
     * 物资奖详情页
     */
    public function receive($id) {
        $this->assign("tabs_sub", "lists");
        $welfareRewardDM = WelfareRewardDModel::getInstance();
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $welfareRewardEN = $welfareRewardDM->find($id);//查询奖券信息

        if (!$welfareRewardEN) return $this->error("该记录不存在");

        $user = $userDM->findOneBy(array("id" => $welfareRewardEN->getUserId()));//收奖人ID
        $fromUser = $userDM->findOneBy(array("id" => $welfareRewardEN->getFromUser()));//发奖人ID
        $this->assign("user", $user);
        $this->assign("fromUser", $fromUser);
        $this->assign("wr", $welfareRewardEN);
        $this->assign("userId", $this->getUser('id'));
        if ($welfareRewardEN->getWvId()) {
            $wv = $welfareVoucherDM->name("wv")
                ->where("wv.id = " . $welfareRewardEN->getWvId())
                ->getOneArray();//获取抵扣券的详细信息
            $this->assign("wv", $wv);
        }
        $welfareChatDM = WelfareChatDModel::getInstance();
        $lists = $welfareChatDM->name("wc")//查看回复
        ->leftJoin("User", "u", "u.id = wc.userId")
            ->select("wc,u.fullName")
            ->where("wc.wrId =" . $id . " and wc.status = 1 and wc.sid =" . $this->sid)
            ->order("wc.id", "ASC")
            ->getArray(true);
        $this->assign("lists", $lists);
        if (Q()->isPost()) {
            $this->chat($this->getUser('id'));
        }
        return $this->display();
    }

    /*
     * 发放物资奖
     */
    public function addLists() {
        $this->assign("tabs_sub", "lists");
        if (Q()->isGet()) {
            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workers($this->sid, array());
            $this->assign("executors", $executors);
            $welfareVoucherDM = WelfareVoucherDModel::getInstance();
            $voucher = $welfareVoucherDM->name("wv")
                ->where("wv.sid = " . $this->sid . " and wv.types = 1")
                ->order("wv.sort")
                ->getArray();
            $this->assign("voucher", $voucher);
            return $this->display();
        }
        $post = Q()->post->all();
//      if (!$post['names'][0]) return $this->error("请输入物资奖名称");
        if ($post['if_types'] == 1) {
            if (!$post['names']) return $this->error("请输入物资奖名称");
        } elseif ($post['if_types'] == 2) {
            if (!$post['wvId']) return $this->error("请选择抵扣券");
        }
        if (!$post['userId']) return $this->error("请选择奖励对象");
        if (!$post['memo']) return $this->error("请输入奖励原因");
        $welfareRewardDM = WelfareRewardDModel::getInstance();
        $acornDM = AcornDModel::getInstance();
        $toUser = $acornDM->toUserId($post['userId']);
        foreach ($toUser as $k => $v) {
            if ($post['if_types'] == 1) {
                $post['wvId'] = '';
            } elseif ($post['if_types'] == 2) {
                $post['names'] = '';
            }
            $post['userId'] = $v['id'];
            $post["sid"] = $this->sid;
            $post["fromUser"] = $this->getUser("id");
            $post["addTime"] = nowTime();
            $post["status"] = 0;
            $post["types"] = 1;
            $welfareRewardDM->create($post, $welfareReward = $welfareRewardDM->newEntity());
            $welfareRewardDM->add($welfareReward)->flush($welfareReward);
        }
        return $this->success(url("mobileConsoles_welfare"));
    }

    /**
     * 抵扣券管理
     * @return \phpex\Foundation\Response
     */
    public function voucher() {
        $this->assign("tabs_sub", "lists");
        $this->assign("isSuper", $this->isSuper());
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        $lists = $welfareVoucherDM->name("wv")
            ->where("wv.sid = " . $this->sid . " and wv.types = 1")
            ->order("wv.sort")
            ->getArray();
        $this->assign("lists", $lists);
        return $this->display();
    }

    /**
     * 抵扣券添加
     * @return \phpex\Foundation\Response
     * @throws \phpex\Error\Exception
     */
    public function addVoucher() {
        $this->assign("tabs_sub", "lists");
        $this->assign("isSuper", $this->isSuper());
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        $welfareVoucherEN = $welfareVoucherDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"], "types" => 1));
        if (!$post["names"]) return $this->ajaxReturn(array("status" => "n", "info" => "抵扣券名称不能为空"));
        if (!$post["acorn"]) return $this->ajaxReturn(array("status" => "n", "info" => "抵扣券面额不能为空"));
        if ($welfareVoucherEN) return $this->ajaxReturn(array("status" => "n", "info" => "存在同名的抵扣券，请检查"));
        $post["types"] = 1;
        $post["sid"] = $this->sid;
        $welfareVoucherDM->create($post, $welfareVoucher = $welfareVoucherDM->newEntity());
        $welfareVoucherDM->add($welfareVoucher)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "添加抵扣券成功", "url" => url("mobileConsoles_welfare_voucher")));
    }

    /**
     * 抵扣券修改
     * @param $id
     * @return \phpex\Foundation\Response
     * @throws \phpex\Error\Exception
     */
    public function voucherModify($id) {
        $this->assign("tabs_sub", "lists");
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        $lists = $welfareVoucherDM->name("wv")
            ->select("wv")
            ->where("wv.sid=" . $this->sid . "AND wv.id=" . $id)
            ->getOneArray();

        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "记录获取失败", "url" => url("mobileConsoles_welfare_voucher")));
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            return $this->display();
        }

        $post = Q()->post->all();
        if (!$post["names"]) return $this->ajaxReturn(array("status" => "n", "info" => "抵扣券名称不能为空"));
        if (!$post["acorn"]) return $this->ajaxReturn(array("status" => "n", "info" => "抵扣券面额不能为空"));
        $welfareVoucher = $welfareVoucherDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"], "types" => 1));
        if ($welfareVoucher && $welfareVoucher->getId() != $id) return $this->ajaxReturn(array("status" => "n", "info" => "存在同名的设置，请检查"));

        $welfareVoucherEN = $welfareVoucherDM->find($id);
        $post["sid"] = $this->sid;
        $welfareVoucherDM->create($post, $welfareVoucherEN);
        $welfareVoucherDM->save($welfareVoucherEN)->flush($welfareVoucherEN);
        return $this->ajaxReturn(array("status" => "y", "info" => "修改设置成功", "url" => url("mobileConsoles_welfare_voucher")));
    }

    public function voucherDelete($id) {
        $welfareVoucherDM = WelfareVoucherDModel::getInstance();
        $lists = $welfareVoucherDM->name("wv")
            ->select("wv")
            ->where("wv.sid=" . $this->sid . "AND wv.id=" . $id)
            ->getOneArray();
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "记录获取失败", "url" => url("mobileConsoles_welfare_voucher")));
        $welfareVoucher = $welfareVoucherDM->find($id);
        $welfareVoucher->setTypes(0);
        $welfareVoucherDM->save($welfareVoucher)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除设置成功", "url" => url("mobileConsoles_welfare_voucher")));
    }

    /*
     *话题评论
     */
    public function chat($id) {
        $welfareChatDM = WelfareChatDModel::getInstance();
        $post = Q()->post->all();
        if (!$post['chat']) return $this->ajaxReturn(array("status" => "n", "info" => "提交内容不能为空", "url" => url("mobileConsoles_welfare_receive", array("id" => $id))));
        $post["sid"] = $this->sid;
        $post["wrId"] = $id;
        $post["userId"] = $this->getUser("id");
        $post["status"] = 1;
        $post["addTime"] = nowTime();
        $welfareChatDM->create($post, $welfareChat = $welfareChatDM->newEntity());
        $welfareChatDM->add($welfareChat)->flush($welfareChat);
        return $this->ajaxReturn(array("status" => "y", "info" => "提交成功", "url" => url("mobileConsoles_welfare_receive", array("id" => $id))));
    }

    /*
     * 领取奖品
     */
    public function getGift($id) {
        $get = Q()->get->all();
        $welfareRewardDM = WelfareRewardDModel::getInstance();
        $welfareReward = $welfareRewardDM->find($id);
        $welfareReward->setStatus($get['status']);
        $welfareReward->setUseTime(nowTime());
        $welfareRewardDM->save($welfareReward)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "领取成功", "url" => url("mobileConsoles_welfare_receive", array("id" => $id))));
    }

    public function listsRevoke($id) {
        $welfareRewardDM = WelfareRewardDModel::getInstance();
        $lists = $welfareRewardDM->name("wr")
            ->where("wr.id =" . $id)
            ->getOneArray();
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "该记录不存在", "url" => url("mobileConsoles_welfare")));
        $post = Q()->post->all();
        $welfareSnackEN = $welfareRewardDM->find($id);
        $welfareSnackEN->setTypes($post['types']);
        $welfareRewardDM->save($welfareSnackEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "撤销成功", "url" => url("mobileConsoles_welfare")));
    }

    /*
     * 幸运奖
     */
    public function lucky() {

        $this->assign("tabs_sub", "lucky");
        $this->assign("isSuper", $this->isSuper());
        $settingsDM = WelfareSettingsDModel::getInstance();

        if (Q()->isGet()) {
            $settings = $settingsDM->getSettings($this->sid);

            $this->assign("prizes", $settings->getLuckyPrize() ? explode(",", $settings->getLuckyPrize()) : array());

            $staffDM = StaffDModel::getInstance();

            $staffs = $staffDM->name("s")->where("s.sid={$this->sid} and s.status in(1,2)")->getArray();

            $depDM = DepartmentDModel::getInstance();

            $deps = $depDM->name("d")->where("d.sid={$this->sid} and d.status=1")->order("d.parentid")->getArray();

            $members = array(0 => array());

            foreach ($staffs as $staff) {
                $members[0][] = array($staff["userId"], $staff["fullName"]);
                $members[$staff["department"]][] = array($staff["userId"], $staff["fullName"]);
            }

            $this->assign("deps", $deps);
            $this->assign("members", $members);
            return $this->display();
        }

        $post = Q()->post->all();


        $luckyDM = WelfareLuckyDModel::getInstance();

        $lucky = $luckyDM->newEntity();

        $lucky->setSid($this->sid);
        $lucky->setDrawId($this->getUser("id"));
        $lucky->setUserId($post["userId"]);
        $lucky->setTitle($post["title"]);
        $lucky->setLucky($post["lucky"]);
        $lucky->setAddTime(nowTime());
        $lucky->setScopes($post["scopes"]);
        $lucky->setStatus(1);
        $luckyDM->add($lucky)->flush($lucky);

        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("GET_LUCKY", $luckyDM, $lucky->getId(), $this->sid, $this->getUser('id'));

        if ($shareid > 0) {
            return $this->ajaxReturn(array("status" => "y", "info" => "抽奖成功", "url" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }

        return $this->ajaxReturn(array("status" => "y", "info" => "抽奖成功", "url" => url("mobileConsoles_welfare_lucky")));

    }

    /*
     * 小吃柜
     */
    public function snack() {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareSettingsDM = WelfareSettingsDModel::getInstance();
        $snackNum = $welfareSettingsDM->name('ws')->where("ws.sid =" . $this->sid)->getOneArray();
        $companyMemberDM = CompanyMemberDModel::getInstance();
//        $cmUser = $companyMemberDM->name("s")->where("s.sid =" . $this->sid . " and s.userId = " . $this->getUser("id"))->getOneArray();
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $weekStart = $this->weekStart();
        $weekEnd = $this->weekEnd();
        $cmUser = $welfareOrderDM->name("wo")->where("wo.userId = :userId and wo.sid = :sid and wo.addTime<=:weekEnd and wo.addTime>=:weekStart and wo.status in (0,1)")
            ->setParameter(array(
                "userId" => $this->getUser("id"),
                "sid" => $this->sid,
                "weekStart" => $weekStart,
                "weekEnd" => $weekEnd,
            ))->count();
        $num = $snackNum['snackNum'] - $cmUser;
        if ($num <= 0) {
            $sNum = 0;
        } else {
            $sNum = $num;
        }
        $this->assign("sNum", $sNum);
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name("ws")
            ->where("ws.status = 1 AND ws.num > 0 AND ws.types = 1 AND ws.sid = " . $this->sid)
            ->order("ws.id", "DESC")
            ->getArray();
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function addSnack() {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['names']) return $this->error("请输入小吃的名称");
        if (!$post['icon']) return $this->error("请添加小吃展示图片");
        if (!$post['everyNum']) return $this->error("请输入兑换数量");
        if (!$post['acorn']) return $this->error("请输入小吃的价格");
        if (!$post['num']) return $this->error("请输入小吃的库存量");
        if ($post['num'] <= 0) return $this->error("小吃的库存量不能小于等于零");
        if (!$post['unit']) return $this->error("请输入小吃的单位");
        $post['sid'] = $this->sid;
        $post['userId'] = $this->getUser('id');
        $post['status'] = 1;
        $post['addTime'] = nowTime();
        $post['types'] = 1;
        $welfareSnackDM->create($post, $welfareSnack = $welfareSnackDM->newEntity());
        $welfareSnackDM->add($welfareSnack)->flush($welfareSnack);
        return $this->success(url("mobileConsoles_welfare_snack"));
    }

    /*
     * 小吃柜详情
     */
    public function snackDetail($id) {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $this->assign("id", $id);
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name('ws')
            ->where("ws.id = " . $id)
            ->getOneArray();
        if (!$lists) return $this->error('该商品不存在或已下架');
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnBase);
        return $this->display();
    }

    /*
     * 兑换小吃柜商品
     */
    public function exchange($id) {
//        $get = Q()->get->all();
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name('ws')
            ->where("ws.id = " . $id . " and ws.status = 1")
            ->getOneArray();
//      $welfareSnackEN = $welfareSnackDM->findOneBy(array("id" => $id));
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架", "url" => url("mobileConsoles_welfare_snack")));
        DM()->getManager()->beginTransaction();
        if ($lists['everyNum'] > $lists['num']) {//剩余数量和兑换数量
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该商品剩余数量不足以兑换", "url" => url('consoles_lists', 'con=snack')));
        }

//      减少库存
        $num = array("id" => $id, "num" => $lists['everyNum']);
        $decNum = WelfareSnackDModel::getInstance()->name("ws")->where("ws.id = :id")
            ->setParameter($num)
            ->setDec("ws.num", ":num");
        if (!$decNum) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架", "url" => url("mobileConsoles_welfare_snack")));
        }
        $welfareSettingsDM = WelfareSettingsDModel::getInstance();
        $snackNum = $welfareSettingsDM->name('ws')->where("ws.sid =" . $this->sid)->getOneArray();
        $companyMemberDM = CompanyMemberDModel::getInstance();
//        $cmUser = $companyMemberDM->name("s")->where("s.sid =" . $this->sid . " and s.userId = " . $this->getUser("id"))->getOneArray();
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $weekStart = $this->weekStart();
        $weekEnd = $this->weekEnd();
        $cmUser = $welfareOrderDM->name("wo")->where("wo.userId = :userId and wo.sid = :sid and wo.addTime<=:weekEnd and wo.addTime>=:weekStart and wo.status in (0,1)")
            ->setParameter(array(
                "userId" => $this->getUser("id"),
                "sid" => $this->sid,
                "weekStart" => $weekStart,
                "weekEnd" => $weekEnd,
            ))->count();
        $num = $snackNum['snackNum'] - $cmUser;
        if ($num <= 0) {
            $sNum = 0;
        } else {
            $sNum = $num;
        }
        if ($sNum == 0) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "您本周使用积分兑换小吃的机会已用完！"));
        }

//      增加 CompanyMemberDModel内的snackNum
//        $snackNum = array("userId" => $this->getUser('id'), "sid" => $this->sid, "snackNum" => 1);
//        $incSnackNum = CompanyMemberDModel::getInstance()->name("cm")->where("cm.userId = :userId and cm.sid = :sid")
//            ->setParameter($snackNum)
//            ->setInc("cm.snackNum", ":snackNum");
//        if (!$incSnackNum) {
//            DM()->getManager()->rollback();
//            return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架"));
//        }
//      新增我的兑换记录
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $ow['sid'] = $this->sid;
        $ow['userId'] = $this->getUser('id');
        $ow['snackId'] = $lists['id'];
        $ow['everyNum'] = $lists['everyNum'];
        $ow['acorn'] = $lists['acorn'];
        $ow['addTime'] = nowTime();
        $ow['status'] = 0;
        $ow['types'] = 1;
        $welfareOrderDM->create($ow, $welfareOrder = $welfareOrderDM->newEntity());
        $welfareOrderDM->add($welfareOrder)->flush($welfareOrder);
//      减少积分分数
        $standardDM = StandardDModel::getInstance();
        $sShare = $standardDM->name('s')->where("s.names = '兑换小吃柜商品' and s.sid =" . $this->sid . " and s.status = 1")->getOneArray();
        $acornDM = AcornDModel::getInstance();
        $result = $acornDM->addAcorn($this->sid, $this->getUser('id'), $this->getUser('id'), $this->getUser('id'), $sShare['classify'], $sShare['id'], -$lists['acorn'], "兑换成功，扣除积分", "兑换成功，扣除积分");
        if (!$result) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => $acornDM->getError()));
        }
        DM()->getManager()->commit();

        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("EXCHANGE_SNACK", $welfareOrderDM, $welfareOrder->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->ajaxReturn(array("status" => "y", "info" => "兑换成功", "url" => url("mobileConsoles_welfare_snack"), "shareUrl" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }

        return $this->ajaxReturn(array("status" => "y", "info" => "兑换成功", "url" => url("mobileConsoles_welfare_snack")));

    }

    /**
     * 掌柜
     */
    public function snackWaiter() {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name('ws')
            ->where("ws.sid = " . $this->sid . " and ws.types = 1")
            ->order("ws.id", "DESC")
            ->getArray();
        $this->assign("lists", $lists);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();
    }

    public function snackModify($id) {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name('ws')
            ->where("ws.sid = " . $this->sid . " and ws.types = 1 and ws.id = " . $id)
            ->getOneArray();
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("cdnThumb", $this->cdnThumbBase);
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['names']) return $this->ajaxReturn(array("status" => "n", "info" => "小吃名称不能为空"));
        if (!$post['everyNum']) return $this->ajaxReturn(array("status" => "n", "info" => "请输入兑换数量"));
        if (!$post['acorn']) return $this->ajaxReturn(array("status" => "n", "info" => "小吃价格不能为空"));
        if (!$post['num']) return $this->ajaxReturn(array("status" => "n", "info" => "小吃库存不能为空"));
        if (!$post['unit']) return $this->ajaxReturn(array("status" => "n", "info" => "小吃单位不能为空"));
        $welfareSnackEN = $welfareSnackDM->find($id);
        $welfareSnackDM->create($post, $welfareSnackEN);
        $welfareSnackDM->save($welfareSnackEN)->flush($welfareSnackEN);
        return $this->ajaxReturn(array("status" => "y", "info" => "修改小吃成功", "url" => url("mobileConsoles_welfare_snackWaiter")));
    }

    /**
     * 删除小吃
     */
    public function snackDelete($id) {
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name("ws")
            ->where("ws.id =" . $id)
            ->getOneArray();
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "该兑换订单不存在", "url" => url("mobileConsoles_welfare_snackWaiter")));
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $order = $welfareOrderDM->name("wo")
            ->where("wo.snackId =" . $id . " and wo.status = 0")
            ->getArray();
        if ($order) return $this->ajaxReturn(array("status" => "n", "info" => "您还有未核销小吃单，无法删除该小吃"));
        $welfareSnackEN = $welfareSnackDM->find($id);
        $welfareSnackEN->setTypes(2);
        $welfareSnackDM->save($welfareSnackEN)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => url("mobileConsoles_welfare_snackWaiter")));
    }

    /**
     *上下架小吃
     */
    public function snackSwitch($id) {
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareSnackDM->name("ws")
            ->where("ws.id =" . $id)
            ->getOneArray();
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "该兑换订单不存在", "url" => url("mobileConsoles_welfare_snackWaiter")));
        if ($lists['status'] == 1) {
            $welfareSnackEN = $welfareSnackDM->find($id);
            $welfareSnackEN->setStatus(2);
            $welfareSnackDM->save($welfareSnackEN)->flush();
            return $this->ajaxReturn(array("status" => "y", "info" => "下架成功", "url" => url("mobileConsoles_welfare_snackWaiter")));
        } else {
            $welfareSnackEN = $welfareSnackDM->find($id);
            $welfareSnackEN->setStatus(1);
            $welfareSnackDM->save($welfareSnackEN)->flush();
            return $this->ajaxReturn(array("status" => "y", "info" => "上架成功", "url" => url("mobileConsoles_welfare_snackWaiter")));
        }
    }

    /**
     *核销列表
     */
    public function snackCheck() {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $lists = $welfareOrderDM->name("wo")
            ->leftJoin("User", "u", "u.id = wo.userId")
            ->leftJoin("WelfareSnack", "ws", "ws.id = wo.snackId")
            ->select("ws,wo,u.fullName")
            ->where("wo.sid =" . $this->sid)
            ->order("wo.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);
        return $this->display();
    }

    /**
     * 核销详情 / 核销小吃
     */
    public function checkDetail($id) {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareOrderDM->name("wo")
            ->where("wo.id =" . $id)
            ->getOneArray();
        $snack = $welfareSnackDM->name("ws")
            ->where("ws.id = " . $lists['snackId'])
            ->getOneArray();
        $this->assign("lists", $lists);
        $this->assign("snack", $snack);
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();

    }

    public function checkAdopt($id) {
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $welfareOrderEN = $welfareOrderDM->find($id);
        if (!$welfareOrderEN) return $this->ajaxReturn(array("status" => "n", "info" => "核销商品不存在", "url" => url("mobileConsoles_welfare_snackCheck")));
        $welfareOrderEN->setStatus(1);
        $welfareOrderEN->setAutoTime(nowTime());
        $welfareOrderEN->setAuditorName($this->getUser('id'));
        $welfareOrderDM->save($welfareOrderEN)->flush($welfareOrderEN);
        return $this->ajaxReturn(array("status" => "y", "info" => "核销成功", "url" => url("mobileConsoles_welfare_snackCheck")));
    }

    public function checkNotAdopt($id) {
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $welfareOrderEN = $welfareOrderDM->find($id);
        if (!$welfareOrderEN) return $this->ajaxReturn(array("status" => "n", "info" => "核销商品不存在", "url" => url("mobileConsoles_welfare_snackCheck")));
        $welfareOrder = $welfareOrderDM->name("wo")->where("wo.id = " . $id)->getOneArray();
        DM()->getManager()->beginTransaction();
//      增加库存
        $num = array("id" => $welfareOrder['snackId'], "num" => $welfareOrder['everyNum']);
        $decNum = WelfareSnackDModel::getInstance()->name("ws")->where("ws.id = :id")
            ->setParameter($num)
            ->setInc("ws.num", ":num");
        if (!$decNum) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架", "url" => url("mobileConsoles_welfare_snack")));
        }
//      减少CompanyMemberDModel内的snackNum
//        $snackNum = array("userId" => $welfareOrder['userId'], "sid" => $this->sid, "snackNum" => 1);
//        $incSnackNum = CompanyMemberDModel::getInstance()->name("cm")->where("cm.userId = :userId and cm.sid = :sid")
//            ->setParameter($snackNum)
//            ->setDec("cm.snackNum", ":snackNum");
//        if (!$incSnackNum) {
//            DM()->getManager()->rollback();
//            return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架"));
//        }
//      核销小吃失败
        $welfareOrderEN->setStatus(2);
        $welfareOrderEN->setAutoTime(nowTime());
        $welfareOrderEN->setAuditorName($this->getUser('id'));
        $welfareOrderDM->save($welfareOrderEN)->flush($welfareOrderEN);
//      增加积分分数
        $standardDM = StandardDModel::getInstance();
        $sShare = $standardDM->name('s')->where("s.names = '兑换小吃柜商品' and s.sid =" . $this->sid . " and s.status = 1")->getOneArray();
        $acornDM = AcornDModel::getInstance();
        $result = $acornDM->addAcorn($this->sid, $welfareOrderEN->getUserId(), $this->getUser('id'), $this->getUser('id'), $sShare['classify'], $sShare['id'], $welfareOrderEN->getAcorn(), "核销失败,退回积分", "核销失败,退回积分");
        if (!$result) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => $acornDM->getError()));
        }
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "核销失败", "url" => url("mobileConsoles_welfare_snackCheck")));
    }

    /**
     * 我的兑换
     */
    public function mySnack() {
        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $lists = $welfareOrderDM->name('wo')
            ->leftJoin("WelfareSnack", "ws", "wo.snackId = ws.id")
            ->select("wo,ws")
            ->where("wo.sid = " . $this->sid . " and wo.userId = " . $this->getUser('id'))
            ->order("wo.id", "DESC")
            ->getArray(true);
        $this->assign('lists', $lists);
        return $this->display();
    }

    /**
     *我的兑换详情
     */
    public function mySnackDetail($id) {
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $welfareSnackDM = WelfareSnackDModel::getInstance();
        $lists = $welfareOrderDM->name('wo')
            ->where("wo.id = " . $id)
            ->getOneArray();
        $snack = $welfareSnackDM->name("ws")
            ->where("ws.id = " . $lists['snackId'])
            ->getOneArray();

        $this->assign("tabs_sub", "snack");
        $this->assign("isSuper", $this->isSuper());
        $this->assign("snackUserId", $this->snackUserId());
        $this->assign("userId", $this->getUser('id'));
        $this->assign("lists", $lists);
        $this->assign("snack", $snack);
        if (!$lists) return $this->error("该兑换订单不存在", url("mobileConsoles_welfare_mySnack"));
        $this->assign("cdnThumb", $this->cdnThumbBase);
        return $this->display();

    }

    /**
     * 取消兑换小吃
     */
    public function snackCancel($id) {
        $welfareOrderDM = WelfareOrderDModel::getInstance();
        $lists = $welfareOrderDM->name("wo")
            ->where("wo.id =" . $id)
            ->getOneArray();
        if (!$lists) return $this->ajaxReturn(array("status" => "n", "info" => "该兑换订单不存在", "url" => url("mobileConsoles_welfare_mySnack")));
//        $welfareOrder = $welfareOrderDM->name("wo")->where("wo.id = " . $id)->getOneArray();
        DM()->getManager()->beginTransaction();
//      增加库存
        $num = array("id" => $lists['snackId'], "num" =>$lists['everyNum']);
        $decNum = WelfareSnackDModel::getInstance()->name("ws")->where("ws.id = :id")
            ->setParameter($num)
            ->setInc("ws.num", ":num");
        if (!$decNum) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该商品不存在或已下架", "url" => url("mobileConsoles_welfare_snack")));
        }
        $standardDM = StandardDModel::getInstance();
        $sShare = $standardDM->name('s')->where("s.names = '兑换小吃柜商品' and s.sid =" . $this->sid . " and s.status = 1")->getOneArray();
        $acornDM = AcornDModel::getInstance();
        $result = $acornDM->addAcorn($this->sid, $this->getUser('id'), $this->getUser('id'), $this->getUser('id'), $sShare['classify'], $sShare['id'], $lists['acorn'], "取消兑换小吃，退回积分", "取消兑换小吃，退回积分");
        if (!$result) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => $acornDM->getError()));
        }
        $welfareOrderEN = $welfareOrderDM->find($id);
        $welfareOrderDM->remove($welfareOrderEN)->flush();
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "取消兑换小吃柜商品成功", "url" => url("mobileConsoles_welfare_mySnack")));
    }

    /**
     * 小吃柜负责人
     */
    public function snackUserId() {
        $welfareSettingsDM = WelfareSettingsDModel::getInstance();
        $snackUserId = $welfareSettingsDM->name("ws")->where("ws.sid = " . $this->sid)->getOneArray();
        return $snackUserId['snackUserId'];
    }

    public function bonus() {
        $this->assign("tabs_sub", "bonus");
        $this->assign("isSuper", $this->isSuper());

        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */
        $company = $companyDM->find($this->sid);

        $staffDM = StaffDModel::getInstance();

        $staff = $staffDM->findOneBy(array("userId" => $this->getUser("id"), "sid" => $this->sid));

        $companyTotal = $company ? $company->getBonus() : 0;
        $staffTotal = $staff ? $staff->getBonus() : 0;

        $this->assign("companyTotal", $companyTotal);
        $this->assign("staffTotal", $staffTotal);
        $this->assign("scale", $staffTotal / $companyTotal * 100);

        return $this->display();
    }

    public function bonusLists() {
        $this->assign("tabs_sub", "bonus");
        $offset = Q()->get->get("offset") ?: 0;
        $keywords = Q()->get->get("keywords") ?: "";


        $bonusDM = WelfareBonusDModel::getInstance();
        $where = "b.userId= " . $this->getUser("id") . " and b.sid =" . $this->sid;
        $params = array();
        if ($keywords) {
            $where .= " and b.memo like :keywords";
            $params["keywords"] = "%" . $keywords . "%";
        }
        $lists = $bonusDM->name("b")->leftJoin("User", "u", "u.id=b.userId")->select("b,u.fullName")
            ->where($where)->setParameter($params)
            ->order("b.id", "DESC")
            ->limit($offset, $this->listsSize)
            ->getArray(true);
        $this->assign("lists", $lists);
        if (!Q()->isAjax()) {
            $this->assign("keywords", $keywords);
            $this->assign("offset", $offset + $this->listsSize);
            $this->assign("infinite", count($lists) == $this->listsSize);
            return $this->display();
        }
        return $this->success(array(
            "html" => $this->fetch("bonusListsItems"),
            "infinite" => count($lists) == $this->listsSize,
            "offset" => $offset + $this->listsSize,
        ));

    }

    public function addBonus() {
        $this->assign("tabs_sub", "bonus");
        if (Q()->isGet()) {


            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workers($this->sid, array(), 1);
            $this->assign("executors", $executors);
            return $this->display();
        }

        $post = Q()->post->all();

        $bonusDM = WelfareBonusDModel::getInstance();

        $isAddedOK = $bonusDM->added($post["userId"], $this->sid, $post["bonus"], $post["memo"]);

        if (!$isAddedOK) return $this->error($bonusDM->getError());

        return $this->success(url("mobileConsoles_welfare_bonus"));
    }

    public function bonusSetting() {

        $settingsDM = WelfareSettingsDModel::getInstance();
        $settings = $settingsDM->getSettings($this->sid);

        if (Q()->isGet()) {

            $companyDM = CompanyDModel::getInstance();

            /** @var Company $company */


            $company = $companyDM->find($this->sid);

            $staffDM = StaffDModel::getInstance();
            $executors = $staffDM->workers($this->sid, $settings->getSnackUserId(), 1);
            $this->assign("executors", $executors);
            $this->assign("settings", $settings);
            $this->assign("companyBonus", $company ? $company->getBonus() : 0);
            return $this->display();
        }

        $post = Q()->post->all();
        $standardDM = StandardDModel::getInstance();
        $snackStandar = $standardDM->name("s")
            ->where("s.sid = " . $this->sid . " and s.names = '兑换小吃柜商品' and s.status = 1")
            ->getOneArray();
        if ($post['snack'] == 1) {
            if (!$snackStandar) {
                return $this->error("请先到【标准】项内添加'兑换小吃柜商品'事项后，再开启小吃柜~");
            }
        }
        $settings->setSnackUserId($post["snackUserId"]);
        $settings->setSnackNum($post["snackNum"]);
        $settings->setSnack($post["snack"] ? 1 : 0);

        $settings->setBonus($post["bonus"] ? 1 : 0);
        $settings->setBonusPool($post["bonusPool"]);

        $settings->setLucky($post["lucky"] ? 1 : 0);
        $settings->setLuckyPrize($post["luckyPrize"]);

        $settings->setMaterials($post["materials"] ? 1 : 0);

        $settingsDM->save($settings)->flush($settings);

        return $this->success("设置成功");
    }

    public function settingBonus() {
        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */


        $company = $companyDM->find($this->sid);

        if (!$company) return $this->error("企业管理员信息获取失败");

        $superid = $company->getSuperid();
        /** @var User $user */

        $user = UserDModel::getInstance()->find($superid ?: 0);

        if (!$user) return $this->error("企业管理员信息获取失败");

        $post = Q()->post->all();
        if ($company->getBonus() >= $post["companyBonus"]) {
            return $this->error("新的总股数必须大于原有的总股数");
        }

        $smsDM = SmsDModel::getInstance();
        $phone = $user->getPhone();
        $template = SmsDModel::MODIFY_SETTING;

        $sms = $smsDM->isValidSms($superid, $phone, $post["companyBonusCode"], $template);


        if (!$sms) return $this->error($smsDM->getError());

        $company->setBonus($post["companyBonus"]);
        $companyDM->save($company)->flush($company);

        return $this->success("设置成功");


    }

    public function bonusGetVerify() {
        $companyDM = CompanyDModel::getInstance();

        /** @var Company $company */


        $company = $companyDM->find($this->sid);

        if (!$company) return $this->error("企业管理员信息获取失败");

        $superid = $company->getSuperid();

        /** @var User $user */

        $user = UserDModel::getInstance()->find($superid ?: 0);

        if (!$user) return $this->error("企业管理员信息获取失败");
        $smsDM = SmsDModel::getInstance();
        $phone = $user->getPhone();
        $template = SmsDModel::MODIFY_SETTING;

        $settingsDM = SettingsDModel::getInstance();
        $settings = $settingsDM->findOneBy(array("sid" => 0, "names" => "sms"));

        if ($smsDM->setting($settings)->send($template, $phone, $superid)) {
            return $this->success("短信已经发送到" . hideInfo($phone));
        }
        return $this->error((string)$smsDM->getError());

    }

    /**
     * 开始时间
     */
    public function weekStart() {
//当前日期
        $sdefaultDate = date("Y-m-d");
//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first = 1;
//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start = date('Y-m-d 00:00:01', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));

        return $week_start;
//本周结束日期

    }

    /**
     * 结束时间
     */
    public function weekEnd() {
//当前日期
        $sdefaultDate = date("Y-m-d");
//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first = 1;
//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $week_start = date('Y-m-d 00:00:01', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
//本周结束日期
        $week_end = date('Y-m-d 23:59:59', strtotime("$week_start +6 days"));

        return $week_end;
    }
}