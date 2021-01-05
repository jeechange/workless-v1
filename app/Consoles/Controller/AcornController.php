<?php

namespace Consoles\Controller;

use Admin\DModel\AcornAuditDetailDModel;
use Admin\DModel\AcornAuditDModel;
use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\RedDotDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffStationDModel;
use Admin\DModel\StandardClassifyDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\AcornAuditDetail;
use Jeechange\SDK\DingSDK;
use Jeechange\SDK\WxSDK;

class AcornController extends CommonController {
    /**
     * 明细列表
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $types = Q()->get->get("types") ?: "All";

        $standardClassifyDM = new \Admin\DModel\StandardClassifyDModel();

        $acornDM = AcornDModel::getInstance();
        $search = $this->search();
        $search->labelType("placeholder");
        $search->addKeyword("s.names,u1.fullName,u2.fullName", "标准/申请人/审核人");
        $search->addExport("excel", "导出到excel");
        $search->bindData(Q()->get->all());

        $where = "a.status = 1 AND a.sid in (0," . $this->sid . ") AND a.userId = " . $this->getUser('id');
        if ($types != 'All') {
            $pId = $standardClassifyDM->getStdId($types);
            $where .= "AND a.scId=" . $pId;
        }

        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        if (Q()->get->has("__export__excel")) {
            return $this->exprotLists($where, $params);
        }

        $lists = $acornDM->name('a')
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("User", "u1", "u1.id = a.fromUser")
            ->leftJoin("User", "u2", "u2.id = a.auditor")
            ->leftJoin("StandardClassify", "sc", "a.scId = sc.id")
            ->leftJoin("Standard", "s", "a.names = s.id")
            ->select('a,u.fullName as uName,u1.fullName,u2.fullName as fullNames,sc.names, s.names as sNames')
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("a.addTime", "DESC")
            ->order("a.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        $this->assign("types", $types);
        return $this->display();
    }

    public function exprotLists($where, $params) {
        $acornDM = new \Admin\DModel\AcornDModel();
        $lists = $acornDM->name('a')
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("User", "u1", "u1.id = a.fromUser")
            ->leftJoin("User", "u2", "u2.id = a.auditor")
            ->leftJoin("StandardClassify", "sc", "a.scId = sc.id")
            ->leftJoin("Standard", "s", "a.names = s.id")
            ->select('a,u.fullName as uName,u1.fullName,u2.fullName as fullNames,sc.names, s.names as sNames')
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("a.addTime", "DESC")
            ->order("a.id", "DESC")
            ->getArray(true);
        if (!$lists) {
            $url2 = sprintf("%s#%s", url("~consoles_index_index"), url("consoles_lists", "con=acorn"));
            return $this->redirect($url2);
        }
        foreach ($lists as $key => &$item) {
            $item['sNamesMemo'] = "[" . $item['names'] . "]" . $item['sNames'];
            if (preg_match('/^\d+$/', $item['a_sysMemo'])) {
                $item['sNamesMemo'] .= "[#" . $item['a_sysMemo'] . "]";
            }
        }

        $headder = array(
            'A' => 'uName:姓名%s',
            'B' => 'sNamesMemo:标准',
            'C' => 'a_acorn:积分',
            'D' => 'fullName:申请人%s',
            'E' => 'fullNames:审核人%s',
            'F' => 'a_addTime:发放时间',
        );
        return excelExprot(sprintf("积分明细%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }

    /**
     * 申请列表
     * @return \phpex\Foundation\Response
     */
    public function applyLists() {
        $types = Q()->get->get("types") ?: 'my';
        $this->assign("types", $types);
        $acornAuditDM = AcornAuditDModel::getInstance();
        $shareDM = new \Admin\DModel\ShareDModel();
        $seach = $this->search();
        $seach->labelType("placeholder");
        $seach->addKeyword("aa.tags", "标准/申请人/被申请人");
        $seach->addSelect("aa.status", "状态", array(0 => "审核中", 1 => "已审核", 2 => "不通过"), "全部");
        $seach->addExport("excel", "导出到excel");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        $where = "aa.userId = " . $this->getUser('id') . "and aa.sid in (0," . $this->sid . ")";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        if (Q()->get->has("__export__excel")) {
            return $this->applyExprotLists($where, $params);
        }
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u1", "u1.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u1.fullName as fullNames")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("aa.id", "desc")
            ->getArray(true);
        $taskDM = TaskDModel::getInstance();
        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
            $share = $shareDM->name("sh")->select("sh.id")->where("sh.eventId=" . $v['aa_id'] . " AND sh.sid=" . $this->sid . " AND sh.template='APPLY_INFLUENCE' AND sh.userId=" . $this->getUser('id'))->setMax(1)->getOneArray();
            if (!$share) {
                //添加分享记录
                $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $v['aa_id'], $this->sid, $this->getUser('id'));
            }
            $lists[$k]['share'] = $share['id'] ? $share['id'] : 0;
        }
//        dump($lists);exit;
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function applyExprotLists($where, $params) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u1", "u1.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u1.fullName as fullNames")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("aa.id", "desc")
            ->getArray(true);
        $taskDM = TaskDModel::getInstance();
        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
            $lists[$k]['sNamesMemo'] = "[" . $v['sNames'] . "]" . $v['names'];
        }

        if (!$lists) {
            return $this->error("暂无数据导出");
        }
        $headder = array(
            'A' => 'aa_addTime:申请时间',
            'B' => 'sNamesMemo:标准',
            'C' => 'aa_acorn:积分',
            'D' => 'fullName:申请人%s',
            'E' => 'toUser:被申请人%s',
            'F' => 'fullNames:审核人%s',
            'G' => 'aa_auditTime:审核时间',
            'H' => 'statusMemo:状态',
        );
        return excelExprot(sprintf("我的申请%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }

    public function submitApply() {
        $types = Q()->get->get("types") ?: 'submit';
        $menuId = Q()->get->get("menuId") ?: '';
        $this->assign("active", $types);
        $this->assign("menuId", $menuId);
        $urls = 'http://console.xiangshuyun';
        $this->assign("url_s", $urls);
        $standardClassifyDM = StandardClassifyDModel::getInstance();
        $menu = $standardClassifyDM->name('sc')
            ->where("sc.pid = 0")
            ->order("sc.id", "ASC")
            ->getArray();
        foreach ($menu as $k => $v) {
            $menu[$k]["sub"] = $standardClassifyDM->name('sc')
                ->where("sc.pid = " . $v["id"])
                ->order("sc.id", "ASC")
                ->getArray();
        }
        $this->assign("menu", $menu);
        $get = Q()->get->all();
        $lists = $this->showLists($get);
        $this->assign("lists", $lists);
        $this->assign("mid", $get['menuId']);
        return $this->display();
    }

    public function showLists($get = array()) {
        $standardDM = StandardDModel::getInstance();
        switch ($get) {
            case !$get['menuId'] AND !$get['subId']:
                $seach = $this->search();
                $seach->labelType("placeholder");
                $seach->addKeyword("s.names", "标准");
                $seach->bindData(Q()->get->all()); //绑定查询数据
                $where = "s.sid in (0," . $this->sid . ")";
                $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
                $lists = $standardDM->name("s")
                    ->leftJoin("StandardClassify", "sc", "sc.id=s.subClassify")
                    ->select("s,sc")
                    ->where($where)
                    ->setParameter($params)
                    ->data_sort()
                    ->order("s.hot", "DESC")
                    ->order("s.acorn", "DESC")
                    ->order("s.id", "DESC")
                    ->getArray(true);
                break;

            case $get['menuId'] == 7 AND !$get['subId']:
                $seach = $this->search();
                $seach->labelType("placeholder");
                $seach->addKeyword("s.names", "标准");
                $seach->bindData(Q()->get->all()); //绑定查询数据
                $where = "s.sid in (0," . $this->sid . ")";
                $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
                $lists = $standardDM->name("s")
                    ->leftJoin("StandardClassify", "sc", "sc.id=s.subClassify")
                    ->select("s,sc")
                    ->where($where)
                    ->setParameter($params)
                    ->data_sort()
                    ->order("s.acorn", "DESC")
                    ->order("s.id", "DESC")
                    ->getArray(true);
                break;
            case $get['menuId'] == 8 AND !$get['subId']:
                $seach = $this->search();
                $seach->labelType("placeholder");
                $seach->addKeyword("s.names", "标准");
                $seach->bindData(Q()->get->all()); //绑定查询数据
                $where = "s.classify = '{$get['menuId']}' and s.sid in (0," . $this->sid . ")";
                $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
                $lists = $standardDM->name("s")
                    ->leftJoin("StandardClassify", "sc", "sc.id=s.subClassify")
                    ->select("s,sc")
                    ->where($where)
                    ->setParameter($params)
                    ->data_sort()
                    ->order("s.acorn", "DESC")
                    ->order("s.id", "DESC")
                    ->getArray(true);
                break;

            default:
                $seach = $this->search();
                $seach->labelType("placeholder");
                $seach->addKeyword("s.names", "标准");
                $seach->bindData(Q()->get->all()); //绑定查询数据
                $where = "s.classify = '{$get['menuId']}' AND s.subClassify = '{$get['subId']}' and s.sid in (0," . $this->sid . ")";
                $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
                $lists = $standardDM->name("s")
                    ->leftJoin("StandardClassify", "sc", "sc.id=s.subClassify")
                    ->select("s,sc")
                    ->where($where)
                    ->setParameter($params)
                    ->data_sort()
                    ->order("s.acorn", "DESC")
                    ->order("s.id", "DESC")
                    ->getArray(true);
        }
        return $lists;
    }

    /**
     * 申请积分v1.0版
     */
    public function applyAdd() {
        $get = Q()->get->all();
        $staffDM = StaffDModel::getInstance();
        $standardDM = StandardDModel::getInstance();
        $lists = $standardDM->name('s')
            ->where("s.id = '{$get['id']}' AND s.status = 1 and s.sid in (0," . $this->sid . ") AND s.types=0")
            ->getOneArray();
        if (Q()->isGet()) {
            $executors1 = $staffDM->workerList($this->sid, "toUser");
            $executors2 = $staffDM->workerList($this->sid, "auditor", array(), 1);
            $this->assign("executors", $executors1);
            $this->assign("executors2", $executors2);
            $this->assign("lists", $lists);
            $session = Q()->getSession();
            $session->set("__token__", rand_string(32));
            $session->save();
            $this->assign("__token__", $session->get("__token__"));
            return $this->display();
        }
        $session = Q()->getSession();
        $post = Q()->post->all();
        $token = $session->get("__token__");
        if ($post["__token__"] != $token) {
            return $this->error("请不要重复提交申请,请刷新页面后再试");
        }
        $session->remove("__token__");
        $session->save();
        if (utf8_encode($post['acorn']) == '不预设') {
            $post['acorn'] = 0;
        }
        if (!$post['acorn']) return $this->error("请填写积分分数");
        if (!$post['toUser']) return $this->error("请选择奖扣对象");
        if (!$post['auditor']) return $this->error("请选择影审核人");
//        $newStandard = $standardDM->name('s')->where("s.id = '{$get['id']}' AND s.status = 1 and s.sid in (0," . $this->sid . ")")->getOneArray();
        $post['sid'] = $this->sid;
        $post['userId'] = $this->getUser('id');
        $post["toUser"] = join(",", $post["toUser"]);
        $post["auditor"] = join(",", $post["auditor"]);
        $post['fromUser'] = $this->getUser('id');
        $post['scId'] = $lists['classify'];
        $post['names'] = $lists['id'];
        $post['addTime'] = nowTime();
        $post['status'] = 0;
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDM->create($post, $acornAudit = $acornAuditDM->newEntity());
        $acornAuditDM->add($acornAudit)->flush();
        $standardDM = StandardDModel::getInstance();
        $addHot = $standardDM->addHot($lists['id'], $this->sid);
        if (!$addHot) {
            return $this->error($standardDM->getError());
        }
        $shareDM = ShareDModel::getInstance();
        if ($post['acorn'] < 0) {
            $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE_MINUS", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        } else {
            $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        }

        if ($shareId > 0) {
            $this->assign("showOnPopup", true);
            return $this->success("提交申请成功", url("consoles_index_sharePage", array("share" => $shareId)));
        }
        return $this->success("提交申请成功");
    }

    /**
     * 积分分享
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function acornShare($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $acornA = $acornAuditDM->name('aa')->where("aa.id =" . $id)->getOneArray();
        $user = $userDM->name("u")->where("u.id = " . $acornA['userId'])->getOneArray();
        $taskDM = TaskDModel::getInstance();
        if ($acornA['userId'] == $acornA['toUser']) {
            $lists = '自己';
        } else {
            $lists = $taskDM->executorMemo($acornA['toUser']);
        }
        $standardDM = StandardDModel::getInstance();
        $sNames = $standardDM->name('s')->where("s.id = " . $acornA['names'])->getOneArray();
        $content = $user['fullName'] . '为[' . $lists . ']申请了积分，申请事项：[' . $sNames['names'] . ']，点击链接查看详情。';
        $url_host = 'https://m.console.xiangshuyun.com/acornShare?aa=' . $id;
        $this->assign("content", $content);
        $this->assign("url", $url_host);
        $this->assign("url_host", '申请积分：' . $content . $url_host);
//        $this->initSDK();//初始化SDK
        return $this->display();
    }

    /**
     *删除和取消
     */
    public function cancel($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDetailDM = AcornAuditDetailDModel::getInstance();
        $todoDM = TodoDModel::getInstance();
        $lists = $acornAuditDM->name("aa")->select("aa")->where("aa.id=" . $id)->getOneObject();

        DM()->getManager()->beginTransaction();
        if (!$lists) {
            DM()->getManager()->rollback();
            return $this->error("该记录不存在");
        }
        //审核人的todo更新
        $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $lists->getSid(), "types" => 4, "userId" => $lists->getAuditor(), "status" => 0));
        if ($item1) {
            $item1->setStatus(1);
            $todoDM->save($item1)->flush();
        }
        $acornAuditDetail = $acornAuditDetailDM->findOneBy(array("userId" => $lists->getAuditor(), "auditId" => $id));
        if ($acornAuditDetail) {
            $acornAuditDetailDM->remove($acornAuditDetail)->flush();
        }

        //抄送人的todo更新
        $cPerson = explode(",", $lists->getCPerson());
        foreach ($cPerson as $userId) {
            $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $lists->getSid(), "types" => 5, "userId" => $userId, "status" => 0));
            if ($item1) {
                $item1->setStatus(1);
                $todoDM->save($item1)->flush();
            }
            $acornAuditDetail = $acornAuditDetailDM->findOneBy(array("userId" => $userId, "auditId" => $id));
            if ($acornAuditDetail) {
                $acornAuditDetailDM->remove($acornAuditDetail)->flush();
            }
        }

        $acornAuditDM->remove($lists)->flush();
        DM()->getManager()->commit();
        return $this->success("操作成功", url('consoles_acorn_applyLists'));
    }

    /**
     * 审核列表
     * @return \phpex\Foundation\Response
     */
    public function auditLists() {
        $types = Q()->get->get("types") ?: 'audit';
        $acornAuditDM = AcornAuditDModel::getInstance();
        //$acornAuditDM->updateData();die;//审核表数据动态修改
        $seach = $this->search();
        $seach->labelType("placeholder");
        $seach->addKeyword("aa.tags", "标准/申请人/被申请人");
        $seach->addSelect("aa.status", "状态", array(0 => "审核中", 1 => "已审核", 2 => "不通过"), "全部");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        $where = "aa.auditor = '{$this->getUser('id')}' and aa.sid = " . $this->sid;

        if ($types == "audit") {
            $where .= "AND aa.status=0";
        }
        if ($types == "audited") {
            $where .= "AND aa.status in(1,2)";
        }

        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u1", "u1.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u1.fullName as fullNames")
            ->where($where)
            ->setParameter($params)
            ->setPage()->data_sort()
            ->order("aa.status", "ASC")
            ->order("aa.id", "desc")
            ->getArray(true);
        $taskDM = TaskDModel::getInstance();
        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
        }
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        return $this->display();
    }

    /**
     * 提交审核
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function audits($id) {
        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'AcornAuditDetail');
        $acornAuditDM = AcornAuditDModel::getInstance();
        $todoDM = TodoDModel::getInstance();
        $acornAuditDetailDM = AcornAuditDetailDModel::getInstance();
        $standardDM = StandardDModel::getInstance();
        $taskDM = TaskDModel::getInstance();

        if ($id > 2236) {
            $acornAuditDetail = $acornAuditDetailDM->find($id);
            if ($acornAuditDetail) {
                $acornAuditEN = $acornAuditDM->findOneBy(array("id" => $acornAuditDetail->getAuditId()));
            } else {
                return $this->error("记录不存在");
            }
        } else {
            $acornAuditEN = $acornAuditDM->findOneBy(array("id" => $id));
        }
        if (!$acornAuditEN) {
            return $this->error("记录不存在");
        }
        $standardEN = $standardDM->find($acornAuditEN->getNames());
        $toUser = $taskDM->executorMemo($acornAuditEN->getToUser());
        if (Q()->isGet()) {
            $this->assign("standardEN", $standardDM->toArray($standardEN));
            $this->assign("toUser", $toUser);
            $this->assign("lists", $acornAuditDM->toArray($acornAuditEN));
            $this->assign("userId", $this->getUser("id"));
            $thumbs = json_decode($acornAuditEN->getThumbs(), true);
            $parseThumbs = array();
            if ($thumbs) {
                foreach ($thumbs as $thumb) {
                    if (!$thumb[0]) continue;
                    $parseThumbs[] = array(
                        "name" => $thumb[1] ?: basename($thumb[0]),
                        "src" => $this->cdnBase . $thumb[0],
                        "val" => $thumb[0],
                        "type" => $this->getThumbType($thumb[0])
                    );
                }
            }
            $this->assign("thumbs", $parseThumbs);
            $this->assign("cdnThumbBase", $this->cdnThumbBase);
            return $this->display();
        }

        $post = Q()->post->all();
        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;


        DM()->getManager()->beginTransaction();
        if (!$standardEN) {
            DM()->getManager()->rollback();
            return $this->error("该维度不存在，请重新申请");
        }
        //抄送人处理
        if (in_array($this->getUser("id"), explode(",", $acornAuditEN->getCperson()))) {
            $item = $todoDM->findOneBy(array("relateId" => $id, "sid" => $sid, "types" => 5, "userId" => $userId, "status" => 0));
            if ($item) {
                $item->setStatus(1);
                $todoDM->save($item)->flush();
            }
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                $acornAuditDetail->setStatus(1);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            DM()->getManager()->commit();
            return $this->success("已经阅读");
        }

        if ($post['status'] == 1 && !$post["acorn"]) {
            DM()->getManager()->rollback();
            return $this->error("请填写积分分数");
        }

        //最高审核分
        $staffDM = StaffDModel::getInstance();
        $staffEN = $staffDM->name("s")->select("s,ss")
            ->leftJoin("StaffStation", "ss", "s.station = ss.id")
            ->where("s.userId = {$userId} and s.sid = {$sid}")
            ->limit(0, 1)
            ->order("ss.riseAcorn", "asc")
            ->getArray(true);
        $riseAcorn = $staffEN[0]['ss_riseAcorn'];
        $limitAcorn = $staffEN[0]['ss_limitAcorn'];
        if ($post["acorn"] > $riseAcorn and $limitAcorn == 1) {
            DM()->getManager()->rollback();
            return $this->error("验收失败(本次任务已经超出您的最高审核分：{$riseAcorn})");
        }

        $post['auditTime'] = nowTime();
        $post['types'] = $post['acorn'];

        if ($id > 2236) {
            $lists = $acornAuditDM->name("aa")->where("aa.id =" . $acornAuditDetail->getAuditId())->getOneArray();
            if ($lists['status'] == 1 || $lists['status'] == 2) {
                DM()->getManager()->rollback();
                return $this->error("该事项已审核！");
            }
        } else {
            $lists = $acornAuditDM->name("aa")->where("aa.id =" . $id)->getOneArray();
            if ($lists['status'] == 1 || $lists['status'] == 2) {
                DM()->getManager()->rollback();
                return $this->error("该事项已审核！");
            }
        }
        //审核人的todo更新
        $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $sid, "types" => 4, "userId" => $userId, "status" => 0));
        if ($item1) {
            $item1->setStatus(1);
            $todoDM->save($item1)->flush();
        }
        if ($post['status'] == 1) {
            $acornAuditDM->create($post, $acornAuditEN);
            if (!$acornAuditDM->check($post, $acornAuditEN)) {
                DM()->getManager()->rollback();
                return $this->error($acornAuditDM->getError());
            }
            $acornAuditDM->save($acornAuditEN)->flush($acornAuditEN);
            $acornDM = AcornDModel::getInstance();
            $s_id = $standardDM->find($acornAuditEN->getNames());
            $toUser = $acornDM->toUserId($acornAuditEN->getToUser());
            foreach ($toUser as $k => $v) {
                $result = $acornDM->addAcorn($this->sid, $v['id'], $acornAuditEN->getFromUser(), $acornAuditEN->getAuditor(), $acornAuditEN->getScId(), $acornAuditEN->getNames(), $post['acorn'], "完成[" . $s_id->getNames() . "]标准", $acornAuditEN->getMemo(), $acornAuditEN->getAddTime());
                if (!$result) {
                    DM()->getManager()->rollback();
                    return $this->error($acornDM->getError());
                }
            }
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                if ($post['acorn'] != $acornAuditDetail->getAcorn()) {
                    $acornAuditDetail->setAcorn($post['acorn']);
                }
                $acornAuditDetail->setStatus(1);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            DM()->getManager()->commit();
            return $this->success("审核成功");
        } else {
            if (!$post['sysMemo']) {
                DM()->getManager()->rollback();
                return $this->error("请填写审核不通过说明");
            }
            $acornAuditDM->create($post, $acornAuditEN);
            if (!$acornAuditDM->check($post, $acornAuditEN)) {
                DM()->getManager()->rollback();
                return $this->error($acornAuditDM->getError());
            }
            $acornAuditDM->save($acornAuditEN)->flush($acornAuditEN);
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                $acornAuditDetail->setStatus(2);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            DM()->getManager()->commit();
            return $this->success("审核不通过");
        }
    }

    /**
     * 文件后缀
     * @param $name
     * @return string
     */
    private function getThumbType($name) {
        $ext = strtolower(substr(strrchr($name, "."), 1));

        if (in_array($ext, array("png", "jpg", "gif", "jpeg", "bmp"))) return "img";

        return "file";
    }

    /**
     * 分析
     * @return \phpex\Foundation\Response
     */
    public function analysis() {
        $acornDM = AcornDModel::getInstance();
        $standardDM = StandardDModel::getInstance();
        $userId = $this->getUser('id');
        $lists = $acornDM->name('a')
            ->select("a")
            ->groupBy("a.names")
            ->where("a.userId = '{$userId}' AND a.status = 1 and a.sid =" . $this->sid)
            ->getArray();
        foreach ($lists as $k => $v) {
            $standardEN = $standardDM->name('s')->where("s.id = {$v['names']}")->getOneArray();
            $lists[$k]['total'] = array(
                'names' => $standardEN['names'],
                'acorn' => $acornDM->name('a')->where("a.userId = '{$userId}' AND a.names = '{$v['names']}' AND a.status = 1")->sum("a.acorn"),
            );
        }
        $ass = array();
        $vss = array();
        foreach ($lists as $k => $val) {
            $ks = $val['total']['names'];
            $ass[] = array("value" => $lists[$k]['total']['acorn'], "name" => $ks);
            $vss[] = $ks;
        }
        $this->assign('vss', $vss);
        $this->assign('ass', $ass);
        $this->assign("lists", $lists);
        $this->assign("types", 'analysis');
        return $this->display();
    }

    /**
     * 明细列表
     * @return \phpex\Foundation\Response
     */
    public function allLists() {
        $acornDM = AcornDModel::getInstance();
        $search = $this->search();
        $search->labelType("placeholder");
        $search->addKeyword("s.names,u.fullName,u1.fullName,u2.fullName", "标准/被申请人/申请人/审核人");
        $search->addExport("excel", "导出到excel");
        $search->bindData(Q()->get->all());
        $where = "a.status = 1 AND a.sid in (0," . $this->sid . ")";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
         if (Q()->get->has("__export__excel")) {
            return $this->exprotLists($where, $params);
        }
        $lists = $acornDM->name('a')
            ->leftJoin("User", "u", "u.id = a.userId")
            ->leftJoin("User", "u1", "u1.id = a.fromUser")
            ->leftJoin("User", "u2", "u2.id = a.auditor")
            ->leftJoin("StandardClassify", "sc", "a.scId = sc.id")
            ->leftJoin("Standard", "s", "a.names = s.id")
            ->select('a,u.fullName as uName,u1.fullName,u2.fullName as fullNames,sc.names, s.names as sNames')
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->data_sort()
            ->order("a.addTime", "DESC")
            ->order("a.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("searchForm", $searchForm);
        return $this->display();
    }

    /**
     *申请积分v.2版
     * @return \phpex\Foundation\Response
     *
     */
    public function apply() {
        $staffDM = new \Admin\DModel\StaffDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $acornAuditDM = AcornAuditDModel::getInstance();
        $active = Q()->get->get("active") ?: 'Action';
        if (Q()->isGet()) {
            $where = "sc.namesEn='" . $active . "' AND s.sid in (0," . $this->sid . ") AND s.status=1 AND s.types=0";
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s.id as id,s.names as names,s.acorn as acorn, s.memo as memo")
                ->where($where)
                ->getArray(true);
            $acorn = array();
            foreach ($lists as $key => $item) {
                $acorn[$item['id']]['acorn'] = $item['acorn'];
                $acorn[$item['id']]['memo'] = $item['memo'];
            }
            $executors1 = $staffDM->workerList($this->sid, "toUser", $this->getUser("id"));
            $executors2 = $staffDM->workerList($this->sid, "auditor", array(), 1);
            $executors3 = $staffDM->workerList($this->sid, "cPerson");
            $this->assign("executors", $executors1);
            $this->assign("executors2", $executors2);
            $this->assign("executors3", $executors3);
            $this->assign("lists", $lists);
            $this->assign("active", $active);
            $this->assign("types", "apply");
            $this->assign("acorn", $acorn);
            $this->assign("url", url('consoles_acorn_apply'));
            $this->assign("sid", $this->sid);

            return $this->display();
        }
        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();
        if (!$post['names']) {
            DM()->getManager()->rollback();
            return $this->error("请选择相对应的维度", url("consoles_acorn_apply", "active=" . $active));
        }
        $where = "s.id='" . $post['names'] . "' AND s.sid=" . $this->sid;
        $lists = $standardDM->name("s")
            ->select("s.id as id,s.classify as classify,s.names as names")
            ->where($where)
            ->getOneArray();

        if (utf8_encode($post['acorn']) == '不预设') {
            $post['acorn'] = 0;
        }

        if (!$post['acorn']) {
            DM()->getManager()->rollback();
            return $this->error("请填写积分分数", url("consoles_acorn_apply", "active=" . $active));
        }
        if (!$post['toUser']) {
            DM()->getManager()->rollback();
            return $this->error("请选择奖扣对象", url("consoles_acorn_apply", "active=" . $active));
        }
        if (!$post['auditor']) {
            DM()->getManager()->rollback();
            return $this->error("请选择审核人", url("consoles_acorn_apply", "active=" . $active));
        }

        if (in_array($post['auditor'][0], explode(",", $post['toUser']))) {
            DM()->getManager()->rollback();
            return $this->error("奖扣对象不能是审核人", url("consoles_acorn_apply", "active=" . $active));
        }
        if ($post['cPerson']) {
            if (in_array($post['auditor'][0], explode(",", $post['cPerson']))) {
                DM()->getManager()->rollback();
                return $this->error("抄送人不能是审核人", url("consoles_acorn_apply", "active=" . $active));
            }
        }

        $array = $post["toUser"];
        $post['sid'] = $this->sid;
        $post['userId'] = $this->getUser('id');
        $post["toUser"] = join(",", $post["toUser"]);
        $post["auditor"] = join(",", $post["auditor"]);
        $post["cPerson"] = join(",", $post["cPerson"]);
        $post['fromUser'] = $this->getUser('id');
        $post['scId'] = $lists['classify'];
        $post['names'] = $lists['id'];
        $post['addTime'] = nowTime();
        $post['status'] = 0;
        $post['tags'] = array();
        $thumbs = array();
        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }
        $post["thumbs"] = json_encode($thumbs);
        //tags字段处理
        if (!in_array($post["auditor"], $array)) {
            $array[] = $post["auditor"];
        }
        foreach ($post["cPerson"] as $id) {
            if (!in_array($id, $array)) {
                $array[] = $post["cPerson"];
            }
        }
        $userDM = new \Admin\DModel\UserDModel();
        foreach ($array as $v) {
            $userEN = $userDM->find($v);
            if ($userEN) $post['tags'][] = $userEN->getFullname();
        }
        if (!in_array($this->getUser('fullName'), $post['tags'])) {
            $post['tags'][] = $this->getUser('fullName');
        }
        $post['tags'] = array_unique($post['tags']);
        $post['tags'] = join(",", $post['tags']);
        $post['tags'] .= ',' . $lists['names'];

        $acornAuditDM->create($post, $acornAudit = $acornAuditDM->newEntity());
        $acornAuditDM->add($acornAudit)->flush();

        //添加到每个执行人的详情
        $acorndetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $acorndetailDM->adds($acornAudit, $this->getUser('id'));

        //热门
        $standardDM = StandardDModel::getInstance();
        $addHot = $standardDM->addHot($lists['id'], $this->sid);

        if (!$addHot) {
            DM()->getManager()->rollback();
            return $this->error($standardDM->getError());
        }
        $shareDM = ShareDModel::getInstance();
        if ($post['acorn'] < 0) {
            $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE_MINUS", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        } else {
            $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        }
        if ($shareId > 0) {
            $share = $shareDM->getLastShare();
            $shareUrl = explode(",", $share->getShareUrl());
            $message = sprintf("%s%s%s,点击链接查看详情%s?share=%d",
                $share->getContent1(),
                $share->getContent2(),
                $share->getContent3() ? sprintf("，附言：%s", $share->getContent3()) : "",
                $shareUrl[0],
                $share->getId()
            );
            $users = explode(",", $acornAudit->getToUser());

            $users[] = $acornAudit->getUserId();
            $users[] = $acornAudit->getAuditor();
            $users[] = explode(",", $acornAudit->getCPerson());//抄送人
            CompanyOpenapiDModel::sendAcornMessage($acornAudit->getSid(), $message, $users);

            $this->assign("showOnPopup", true);
            DM()->getManager()->commit();
            return $this->success("提交申请成功", url("consoles_index_sharePage", array("share" => $shareId)));
        }
        DM()->getManager()->commit();
        return $this->success("提交申请成功", url("consoles_acorn_apply", "active=" . $active));
    }

    /**
     * 申请积分详情
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function detail($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAudit = $acornAuditDM->name("aa")
            ->select("aa")
            ->where("aa.id=" . $id . "AND aa.sid=" . $this->sid)
            ->order("aa.id", "DESC")
            ->getOneArray();
        $thumbs = json_decode($acornAudit['thumbs'], true);
        $parseThumbs = array();
        if ($thumbs) {
            foreach ($thumbs as $thumb) {
                if (!$thumb[0]) continue;
                $parseThumbs[] = array(
                    "name" => $thumb[1] ?: basename($thumb[0]),
                    "src" => $this->cdnBase . $thumb[0],
                    "val" => $thumb[0],
                    "type" => $this->getThumbType($thumb[0])
                );
            }
        }
        $this->assign("thumbs", $parseThumbs);
        $this->assign("cdnThumbBase", $this->cdnThumbBase);
        $this->assign("lists", $acornAudit);
        return $this->display();
    }

    /**
     * 积分账户
     * @return \phpex\Foundation\Response
     */
    public function account() {
        $companyMemberDM = new \Admin\DModel\CompanyMemberDModel();
        $rankingDM = new \Admin\DModel\RankingDModel();
        $companyMember = $companyMemberDM->name("c")
            ->select("c")
            ->where("c.userId=" . $this->getUser("id") . "AND c.sid=" . $this->sid . " AND c.status=1")
            ->setMax(1)
            ->getOneArray();

        $year = date("Y");
        $monthlenght = date("m");
        $lists = array();
        $months = array();//月份
        $month = array();//月份
        for ($i = 1; $i <= $monthlenght; $i++) {
            $months[] = $i;
        }
        foreach ($months as $t => $value) {
            $where = "rk.sid=:sid AND rk.year=:year AND rk.userId=:userId AND rk.month=:month";
            $parameter = array(
                "sid" => $this->sid,
                "year" => $year,
                "userId" => $this->getUser("id"),
                "month" => $value,
            );
            $lists[$t] = $rankingDM->name("rk")
                ->select("sum(rk.acorn) as acorn, rk.month as month")
                ->where($where)
                ->setParameter($parameter)
                ->order("acorn", "DESC")
                ->groupBy("rk.month")
                ->getOneArray();
        }

        $values = array();//对应月份的值
        foreach ($lists as $key => $item) {
            $month[$key] = $months[$key] . "月";
            $values[$key] = $item['acorn'] ?: 0;
        }

        $this->assign("month", $month);
        $this->assign("values", $values);
        $this->assign("companyMember", $companyMember);//总积分
        $this->assign("yesAcorn", $rankingDM->yesAcorn($this->sid, $this->getUser("id")));//昨日积分
        return $this->display();
    }


}
