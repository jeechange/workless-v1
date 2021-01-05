<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-07-16
 * Time: 17:50
 */

namespace MobileConsoles\Controller;

use Admin\DModel\CompanyDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\StandardClassifyDModel;

class StandardController extends CommonController {
    private $menu = "standard";
    public function _initialize() {
        parent::_initialize();
        $this->assign("menu", $this->menu);
    }

    /**列表2018
     * @return \phpex\Foundation\Response
     */
    public function listsV1() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $active = Q()->get->get('active') ?: "all";
        $types = Q()->get->get('types') ?: 1;
        $names = $stadardClassDM->getTypes($types);

        if ($active != "all") {
            $where = "sc.sid=0 AND sc.namesEn='" . $active . "'";
            $stadardClass = $stadardClassDM->name("sc")
                ->select("sc")
                ->where($where)
                ->setMax(1)
                ->getOneArray();//一级类

            if (!$stadardClass) {
                $stadardClassDM->addClass($names, $active, 0, $pid = 0);
            }
        }
        $seach = $this->search();
        $seach->addKeyword("s.names", "名称");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        if ($active == "all") {
            $where = "s.sid=" . $this->sid . "AND s.status=1";
            $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where($where)
                ->setParameter($params)
                ->order("s.hot", "DESC")
                ->order("s.acorn", "DESC")
                ->getArray(true);
        } else {
            $where = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid . "AND s.status=1";
            $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where($where)
                ->setParameter($params)
                ->order("s.acorn", "DESC")
                ->getArray(true);
        }


        $this->assign("active", $active);//选中状态
        $this->assign("title", $stadardClass['names']);//标题名字
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        $this->assign("isSuper", $this->isSuper());
        return $this->display();
    }

    /**列表20190111
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $active = Q()->get->get('active') ?: "Achievements";
        $types = Q()->get->get('types') ?: 1;
        $names = $stadardClassDM->getTypes($types);

        if ($active != "all") {
            $where = "sc.sid=0 AND sc.namesEn='" . $active . "'";
            $stadardClass = $stadardClassDM->name("sc")
                ->select("sc")
                ->where($where)
                ->setMax(1)
                ->getOneArray();//一级类

            if (!$stadardClass) {
                $stadardClassDM->addClass($names, $active, 0, $pid = 0);
            }
        }
        $seach = $this->search();
        $seach->addKeyword("s.names", "名称");
        $seach->bindData(Q()->get->all()); //绑定查询数据

        $where = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid . "AND s.status=1";
        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where)
            ->setParameter($params)
            ->order("s.acorn", "DESC")
            ->getArray(true);

        $where1 = "sc.namesEn='" . 'Achievement' . "' AND s.sid=" . $this->sid . "AND s.status=1";
        $lists1 = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where1)
            ->order("s.acorn", "DESC")
            ->getArray(true);
        $where2 = "sc.namesEn='" . 'Report' . "' AND s.sid=" . $this->sid . "AND s.status=1";
        $lists2 = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where2)
            ->order("s.acorn", "DESC")
            ->getArray(true);

        $this->assign("active", $active);
        $this->assign("title", $stadardClass['names']);//标题名字
        $this->assign("lists", $lists);
        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("types", $types);
        $this->assign("isSuper", $this->isSuper());
        if ($active == "Achievements") {
            return $this->display("achievements.latte");
        }
        if ($active == "Ability" || $active == "MentalityAndCulture") {
            return $this->display("addStandard.latte");
        }


        return $this->display();
    }

    /**添加
     * @return \phpex\Foundation\Response
     */
    public function add() {
        $active = Q()->get->get("active") ?: "Achievement";
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        if ($active) {
            $where = "sc.namesEn='" . $active . "' AND sc.status=1";
        } else {
            $where = "sc.pid=0 AND sc.status=1";
        }
        $options = $stadardClassDM->name("sc")
            ->select("sc")
            ->where($where)
            ->getArray();
        if (Q()->isGet()) {
            $this->assign("options", $options);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            return $this->display();
        }

        $post = Q()->post->all();
        if ($post['pid'] == 4 && $post['acornTypes'] == 0) {
            if (!preg_match("/^([0-9]|[1-9][0-9]*)$/", $post['acceptDay'])) return $this->ajaxReturn(array("status" => "n", "info" => "任务量天数须为整数"));
            if ($post['acceptDay'] == 0 && $post['acceptHour'] == 0 && $post['acceptMinute'] == 0) return $this->ajaxReturn(array("status" => "n", "info" => "请填选任务量"));
        }
        if ($post['pid'] == 4 && $post['acornTypes'] == 0) {
            $post['workload'] = $post['acceptDay'] . "," . $post['acceptHour'] . "," . $post['acceptMinute'];
        }
        $post['classify'] = $post['pid'] ?: 0;
        $post['subClassify'] = $post['id'] ?: 0;
        $post['status'] = 1;
        $post['addTime'] = nowTime();
        $post['sid'] = $this->sid;
        $post['hot'] = $post['hot'] ?: 0;
        $post['types'] = 0;//维度类型，0：显示，1：隐藏
        $post['sNo'] = $stadardClassDM->getlists($post['classify'])['abbreviation'] . rand_string(6, 6) ?: "XZ" . rand_string(6, 6);

        if (!$post['names']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "名称不能为空"));
        }
        if (!$post['pid']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择类型"));
        }
        if ($post['acornTypes'] == "-1") {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择是否预设"));
        }
        if ($post['acornTypes'] == 0) {
            if (!$post['acorn']) {
                return $this->ajaxReturn(array("status" => "n", "info" => "积分大于0"));
            }
        } else {
            $post['acorn'] = 0;
        }

        $standardDM->create($post, $standrd = $standardDM->newEntity());
        $standardDM->add($standrd)->flush();

        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("APPLY_STANDARD", $standardDM, $standrd->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->ajaxReturn(array("status" => "y", "info" => "提交成功", "url" => url("mobileConsoles_standard_lists"), "shareUrl" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }

        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => url("mobileConsoles_standard_lists")));

    }

    /**修改
     * @return \phpex\Foundation\Response
     */
    public function modify($id) {
        $standardDM = new \Admin\DModel\StandardDModel();
        $standardApplyDM = new \Admin\DModel\StandardApplyDModel();

        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where("s.id=" . $id . "AND s.sid=" . $this->sid)
            ->getOneArray(true);
        $workload = explode(",", $lists['s_workload']);
        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("workload", $workload);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            return $this->display();
        }
        $post = Q()->post->all();
        if ($post['acornTypes'] == 0) {
            if (!$post['acorn']) {
                return $this->ajaxReturn(array("status" => "n", "info" => "积分大于0"));
            }
        } else {
            $post['acorn'] = 0;
        }

        if (!$lists) {
            return $this->ajaxReturn(array("status" => "n", "info" => "此条记录不存在"));
        }
        if ($post['classify'] == 4 && $post['acornTypes'] == 0) {
            if (!preg_match("/^([0-9]|[1-9][0-9]*)$/", $post['acceptDay'])) return $this->error("任务量天数须为整数");
            if ($post['acceptDay'] == 0 && $post['acceptHour'] == 0 && $post['acceptMinute'] == 0) return $this->error("请填选任务量");
        }
        $lists = $standardDM->find($id);
        $lists->setNames($post['names']);
        $lists->setAcorn($post['acorn']);
        $lists->setMethods($post['methods']);
        $lists->setCycle($post['cycle']);
        $lists->setMemo($post['memo']);
        $lists->setHot($post['hot']);
        $lists->setClassify($post['classify']);
        if ($post['classify'] == 4 && $post['acornTypes'] == 0) {
            $post['workload'] = $post['acceptDay'] . "," . $post['acceptHour'] . "," . $post['acceptMinute'];
            $lists->setWorkload($post['workload']);
        }
        if ($post['status']) {
            $lists->setStatus($post['status']);
        } else {
            $standardApply = $standardApplyDM->name("sa")->select("sa")->where("sa.stardId=" . $id . "AND sa.status=2")->getOneObject();
            if ($standardApply) {
                $standardApply->setStatus(0);
                $standardApplyDM->save($standardApply)->flush();
            }
        }
        $standardDM->save($lists)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "修改成功", "url" => url("mobileConsoles_standard_lists")));

    }

    /**单条删除
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function delete($id) {
        $active = Q()->get->get("active");

        $standardDM = new \Admin\DModel\StandardDModel();
        $standardApplyDM = new \Admin\DModel\StandardApplyDModel();

        $lists = $standardDM->name("s")->select("s")->where("s.id=" . $id)->getOneObject();
        if (!$lists) {
            return $this->error("该条记录不存在");
        }

        $standardApply = $standardApplyDM->name("sa")->where("sa.stardId=" . $lists->getId())->getOneObject();//删除申请标准记录
        if ($standardApply) {
            if ($standardApply->getStatus() == 1) {
                return $this->ajaxReturn(array("status" => "n", "info" => "该条申请标准已审核通过，不允许删除", "url" => url("mobileConsoles_standard_lists")));
            }
            $standardApplyDM->remove($standardApply)->flush();
        }
        $standardDM->remove($lists)->flush();
        if ($active) {
            $url = url("mobileConsoles_standard_lists", "active=" . $active);
        } else {
            $url = url("mobileConsoles_standard_lists");
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "删除成功", "url" => $url));
    }

    /**标准详情
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function detail($id) {
        $standardDM = new \Admin\DModel\StandardDModel();
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where("s.id=" . $id . "AND s.sid=" . $this->sid)
            ->getOneArray(true);
        $workload = explode(",", $lists['s_workload']);

        if (Q()->isGet()) {
            $this->assign("lists", $lists);
            $this->assign("workload", $workload);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            $this->assign("isSuper", $this->isSuper());
            return $this->display();
        }
    }

    /**标准申请
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function applyStandard() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $standardApplyDM = new \Admin\DModel\StandardApplyDModel();
        $options = $stadardClassDM->name("sc")
            ->select("sc")
            ->where("sc.pid=0")
            ->getArray();
        if (Q()->isGet()) {
            $this->assign("options", $options);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            return $this->display();
        }

        $post = Q()->post->all();
        $post['classify'] = $post['pid'] ?: 0;
        $post['subClassify'] = $post['id'] ?: 0;
        $post['status'] = 2;//申请
        $post['addTime'] = nowTime();
        $post['sid'] = $this->sid;
        $post['hot'] = 0;
        $post['sNo'] = $stadardClassDM->getlists($post['classify'])['abbreviation'] . rand_string(6, 6) ?: "XZ" . rand_string(6, 6);
        $post['types'] = 0;//维度类型，0：显示，1：隐藏

        if (!$post['names']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "名称不能为空"));
        }
        if (!$post['pid']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择类型"));
        }
        if ($post['acornTypes'] == "-1") {
            return $this->ajaxReturn(array("status" => "n", "info" => "请选择是否预设"));
        }
        if ($post['acornTypes'] == 0) {
            if (!$post['acorn']) {
                return $this->ajaxReturn(array("status" => "n", "info" => "积分大于0"));
            }
        } else {
            $post['acorn'] = 0;
        }
        $standardDM->create($post, $standrd = $standardDM->newEntity());
        $standardDM->add($standrd)->flush();

        $data['stardId'] = $standrd->getId();//标准id
        $data['userId'] = $this->getUser('id');//申请用户的id
        $data['addTime'] = nowTime();//申请时间
        $data['status'] = 0;//申请状态
        $data['sid'] = $this->sid;
        $standardApplyDM->create($data, $standardApply = $standardApplyDM->newEntity());
        $standardApplyDM->add($standardApply)->flush();

        $shareDM = ShareDModel::getInstance();
        $shareid = $shareDM->chooseTemplate("APPLY_STANDARD", $standardDM, $standrd->getId(), $this->sid, $this->getUser('id'));
        if ($shareid > 0) {
            return $this->ajaxReturn(array("status" => "y", "info" => "提交成功", "url" => url("mobileConsoles_standard_lists"), "shareUrl" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }

        return $this->ajaxReturn(array("status" => "y", "info" => "提交成功"));
    }

    /**标准申请列表
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function applyLists() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $active = Q()->get->get('active') ?: "all";
        $types = Q()->get->get('types') ?: 1;
        $names = $stadardClassDM->getTypes($types);

        if ($active != "all") {
            $where = "sc.sid=0 AND sc.namesEn='" . $active . "'";
            $stadardClass = $stadardClassDM->name("sc")
                ->select("sc")
                ->where($where)
                ->setMax(1)
                ->getOneArray();//一级类

            if (!$stadardClass) {
                $stadardClassDM->addClass($names, $active, 0, $pid = 0);
            }
        }
        $seach = $this->search();
        $seach->addKeyword("s.names", "名称");
        $seach->bindData(Q()->get->all()); //绑定查询数据

        if ($active == "all") {
            $where = "s.sid=" . $this->sid . " AND s.status=2";
        } else {
            $where = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid . "AND s.status=2";
        }

        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->leftJoin("StandardApply", "sa", "sa.stardId=s.id")
            ->select("s,sc,sa")
            ->where($where)
            ->setParameter($params)
            ->order("s.addTime", "DESC")
            ->getArray(true);
        $this->assign("active", $active);//选中状态
        $this->assign("title", $stadardClass['names']);//标题名字
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        $this->assign("isSuper", $this->isSuper());
        return $this->display();
    }

    /**标准审核
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function auditorStandard($id) {
        $standardDM = new \Admin\DModel\StandardDModel();
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardApplyDM = new \Admin\DModel\StandardApplyDModel();
        $options = $stadardClassDM->name("sc")
            ->select("sc")
            ->where("sc.pid=0")
            ->getArray();
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->leftJoin("StandardApply", "sa", "sa.stardId=s.id")
            ->leftJoin("User", "u", "u.id=sa.userId")
            ->select("s,sc,sa,u.fullName,u.phone")
            ->where("s.id=" . $id . "AND s.sid=" . $this->sid)
            ->getOneArray(true);

        if (Q()->isGet()) {
            $lists['sa_memo'] = unserialize($lists['sa_memo']);
            $this->assign("lists", $lists);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            $this->assign("options", $options);
            $this->assign("userId", $this->getUser('id'));

            return $this->display();
        }
        $post = Q()->post->all();
        if ($post['acornTypes'] == 0) {
            if (!$post['acorn']) {
                return $this->ajaxReturn(array("status" => "n", "info" => "积分大于0"));
            }
        } else {
            $post['acorn'] = 0;
        }
        if (!$lists) {
            return $this->ajaxReturn(array("status" => "n", "info" => "此条申请不存在"));
        }
        $lists = $standardDM->find($id);
        $lists->setNames($post['names']);
        $lists->setAcorn($post['acorn']);
        $lists->setMethods($post['methods']);
        $lists->setCycle($post['cycle']);
        $lists->setMemo($post['memo']);
        $lists->setStatus(1);
        $standardApply = $standardApplyDM->name("sa")->select("sa")->where("sa.stardId=" . $id . "AND sa.status=0")->getOneObject();
        if (!$standardApply) {
            return $this->ajaxReturn(array("status" => "n", "info" => "记录不存在或者其他管理员已审核"));
        }

        $standardApply->setAuditor($this->getUser('id'));
        $standardApply->setApplyTime(nowTime());
        $standardApply->setStatus(1);
        $standardApplyDM->save($standardApply)->flush();
        $standardDM->save($lists)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "审核成功", "url" => url("mobileConsoles_standard_lists")));
    }

    /**申请拒绝
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function refusal($id) {
        $post = Q()->post->all();
        if (!$post['sysMemo']) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写拒绝理由"));
        }
        $standardDM = new \Admin\DModel\StandardDModel();
        $standardApplyDM = new \Admin\DModel\StandardApplyDModel();

        $lists = $standardDM->name("s")->select("s")->where("s.id=" . $id)->getOneObject();
        if (!$lists) {
            return $this->error("该条记录不存在");
        }
        $standardApply = $standardApplyDM->name("sa")->where("sa.stardId=" . $lists->getId())->getOneObject();//申请标准记录
        if (!$standardApply) {
            return $this->ajaxReturn(array("status" => "n", "info" => "该条申请标准已删除", "url" => url("mobileConsoles_standard_lists")));
        }
        $data["memo"] = $post["sysMemo"];
        $data['addTime'] = nowTime();
        $returnDetail = unserialize($standardApply->getMemo());
        if (!$returnDetail) {
            $returnDetail[] = $data;
        } else {
            array_push($returnDetail, $data);
        }
        $standardApply->setMemo(serialize($returnDetail));
        $standardApply->setStatus(2);
        $standardApplyDM->save($standardApply)->flush();
        return $this->ajaxReturn(array("status" => "y", "info" => "拒绝成功"));

    }

    /**维度批量修改（仅限能力和心态文化）
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function modifyStandard() {
        $standardDM = new \Admin\DModel\StandardDModel();
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $post = Q()->post->all();
        $title = $stadardClassDM->name("sc")->select("sc")->where("sc.namesEn='" . $post['active'] . "'")->getOneArray();

        $acorn1 = 0;
        foreach ($post['names'] as $key => $item) {
            $acorn1 += $post['acorn1'][$key];
        }
        DM()->getManager()->beginTransaction();
        if ($acorn1 > 100 || $acorn1 < 100) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "总分超过100或者不足100分"));
        }
        foreach ($post['names'] as $key => $item) {
            if (!$item) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "名称不能为空"));
            }
            if (!preg_match("/^[0-9]*$/", $post['acorn1'][$key])) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "请输入正确的数字"));
            }
            $lists = $standardDM->find($key);
            if (!$lists) {
                $post['names'] = $item;
                $post['acorn'] = $post['acorn1'][$key];
                $post['sid'] = $this->sid;
                $post['addTime'] = nowTime();
                $post['status'] = 1;
                $post['classify'] = $title['id'] ?: 0;
                $post['subClassify'] = 0;
                $post['hot'] = $post['hot'] ?: 0;//热度
                $post['sNo'] = $title['abbreviation'] . rand_string(6, 6) ?: "XZ" . rand_string(6, 6);
                $standard = $standardDM->newEntity();
                $standardDM->create($post, $standard);
                $standardDM->add($standard)->flush();
                continue;
            }
            if (!$post['names'][$key]) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "名称不能为空"));
            }
            $lists->setNames($post['names'][$key]);
            $lists->setAcorn($post['acorn1'][$key]);
            $standardDM->save($lists)->flush();
        }
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "保存成功", "url" => url("mobileConsoles_standard_lists", "active=" . $post['active'])));

    }

}