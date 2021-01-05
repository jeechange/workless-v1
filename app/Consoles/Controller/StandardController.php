<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-07-16
 * Time: 17:50
 */

namespace Consoles\Controller;

use Admin\DModel\CompanyDModel;
use Admin\DModel\StandardClassifyDModel;
use Admin\Entity\Admin;
use Admin\DModel\ShareDModel;
use Admin\Entity\Standard;

class StandardController extends CommonController {
    /**列表
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $shareDM = new \Admin\DModel\ShareDModel();
        $active = Q()->get->get('active');
        $types = Q()->get->get('types') ?: 1;
        $names = $stadardClassDM->getTypes($types);

        if ($active != "All") {
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
        $seach->labelType("placeholder");
        $seach->addKeyword("s.names", "名称");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        if ($active != 'All') {
            $where = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid . "AND s.status=1 AND s.types=0";
            $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where($where)
                ->setParameter($params)
                ->setPage()->data_sort()
                ->order("s.acorn", "DESC")
                ->getArray(true);
        } else {
            $where = "s.sid=" . $this->sid . "AND s.status=1 AND s.types=0";
            $stadardClass['names'] = "热门";
            $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where($where)
                ->setParameter($params)
                ->setPage()->data_sort()
                ->order("s.hot", "DESC")
                ->order("s.acorn", "DESC")
                ->getArray(true);
        }

        foreach ($lists as $k => $v) {
            $lists[$k]['s_workloadOne'] = explode(",", $v['s_workload']);
            $share = $shareDM->name("sh")->select("sh.id")->where("sh.eventId=" . $v['s_id'] . " AND sh.sid=" . $this->sid . " AND sh.template='APPLY_STANDARD' AND sh.userId=" . $this->getUser('id'))->setMax(1)->getOneArray();
            if (!$share) {
                //添加分享记录
                $shareDM->chooseTemplate("APPLY_STANDARD", $standardDM, $v['s_id'], $this->sid, $this->getUser('id'));
            }
            $lists[$k]['share'] = $share['id'] ? $share['id'] : 0;
        }
        $this->assign("active", $active);//选中状态
        $this->assign("title", $stadardClass['names']);//标题名字
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        return $this->display();
    }

    /**添加
     * @return \phpex\Foundation\Response
     */
    public function add() {
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $names = Q()->get->get("active");
        if ($names != "All") {
            $where = "sc.namesEn='" . $names . "' AND sc.sid=0";
        } else {
            $where = "sc.pid=0 AND sc.sid=0";
        }
        $options = $stadardClassDM->name("sc")
            ->select("sc")
            ->where($where)
            ->getArray();
        if (Q()->isGet()) {
            $this->assign("options", $options);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());
            $this->assign("names", $names);
            return $this->display();
        }

        $post = Q()->post->all();
        if (!$post['names']) {
            return $this->error("名称标准不能为空");
        }
        if ($post['pid'] == 4 && $post['acornTypes'] == 0) {
            if (!preg_match("/^([0-9]|[1-9][0-9]*)$/", $post['acceptDay'])) return $this->error("任务量天数须为整数");
            if ($post['acceptDay'] == 0 && $post['acceptHour'] == 0 && $post['acceptMinute'] == 0) return $this->error("请填选任务量");
        }
        if ($post['acornTypes'] == 1) {
            $post['acorn'] = 0;
        } else {
            if (!$post['acorn']) {
                return $this->error("积分不为0");
            }
        }
        if ($post['pid'] == 4 && $post['acornTypes'] == 0) {
            $post['workload'] = $post['acceptDay'] . "," . $post['acceptHour'] . "," . $post['acceptMinute'];
        }
        $post['classify'] = $post['pid'] ?: 0;//标准父类
        $post['subClassify'] = $post['id'] ?: 0;//标准子类
        $post['sid'] = $this->sid;
        $post['status'] = 1;
        $post['addTime'] = nowTime();
        $post['hot'] = $post['hot'] ?: 0;//热度
        $post['types'] = 0;//维度类型，0：显示，1：隐藏
        $post['sNo'] = $stadardClassDM->getlists($post['classify'])['abbreviation'] . rand_string(6, 6) ?: "XZ" . rand_string(6, 6);
        $standardDM->create($post, $standrd = $standardDM->newEntity());
        $standardDM->add($standrd)->flush();

        $shareDM = ShareDModel::getInstance();
        $shareId = $shareDM->chooseTemplate("APPLY_STANDARD", $standardDM, $standrd->getId(), $this->sid, $this->getUser('id'));
        if ($shareId > 0) {
            $this->assign("showOnPopup", true);
//            $this->assign("flushMainUrl", url('consoles_lists', 'con=Standard&types=' . $types . '&active=' . $names));
            return $this->success("添加成功", url("consoles_index_sharePage", array("share" => $shareId)));
        }

        return $this->success("添加成功");
    }

    /**修改
     * @return \phpex\Foundation\Response
     */
    public function modify($id) {
        $standardDM = new \Admin\DModel\StandardDModel();
        if (Q()->isGet()) {
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where("s.id=" . $id . "AND s.sid=" . $this->sid)
                ->getOneArray(true);
            if (!$lists) {
                return $this->error("此条记录不存在");
            }
            $workload = explode(",", $lists['s_workload']);
            $standardClassifyDM = StandardClassifyDModel::getInstance();
            $classify = $standardClassifyDM->name('sc')
                ->where('sc.id !=' . $lists['sc_id'])
                ->order('sc.id', 'ASC')
                ->getArray();
            $this->assign("classify", $classify);
            $this->assign("lists", $lists);
            $this->assign("workload", $workload);
            $this->assign("cycle", $standardDM->getCycle());
            $this->assign("methods", $standardDM->getMethods());

            return $this->display();
        }
        $post = Q()->post->all();
        if (!$post['names']) {
            return $this->error("名称标准不能为空");
        }
        if ($post['acornTypes'] == 1) {
            $post['acorn'] = 0;
        } else {
            if (!$post['acorn']) {
                return $this->error("积分不为0");
            }
        }
        if ($post['classify'] == 4 && $post['acornTypes'] == 0) {
            if (!preg_match("/^([0-9]|[1-9][0-9]*)$/", $post['acceptDay'])) return $this->error("任务量天数须为整数");
            if ($post['acceptDay'] == 0 && $post['acceptHour'] == 0 && $post['acceptMinute'] == 0) return $this->error("请填选任务量");
        }
        $lists = $standardDM->find($id);
        $lists->setClassify($post['classify']);
        $lists->setNames($post['names']);
        $lists->setAcorn($post['acorn']);
        $lists->setMethods($post['methods']);
        $lists->setCycle($post['cycle']);
        $lists->setMemo($post['memo']);
        $lists->setStatus($post['status']);
        $lists->setHot($post['hot']);
        if ($post['classify'] == 4 && $post['acornTypes'] == 0) {
            $post['workload'] = $post['acceptDay'] . "," . $post['acceptHour'] . "," . $post['acceptMinute'];
            $lists->setWorkload($post['workload']);
        }
        $standardDM->save($lists)->flush();
        return $this->success("修改成功");

    }

    /**单条删除
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function delete($id) {
        $standardDM = new \Admin\DModel\StandardDModel();
        $names = Q()->get->get("active");
        $types = Q()->get->get("types") ?: 0;

        $lists = $standardDM->name("s")->select("s")->where("s.id=" . $id)->getOneObject();
        $url = url('consoles_lists', 'con=Standard&active=' . $names);//旧标准
        if ($types != 0) {//价值维度
            $url = url('consoles_standard_allStandards', 'active=' . $names);
        }
        if (!$lists) {
            return $this->error("该记录不存在", $url);
        }
        $standardDM->remove($lists)->flush();
        return $this->success("删除成功", $url);
    }

    /**
     * 批量删除
     * @return type
     */
    public function deleteMul() {
        $ids = Q()->post->get("ids");
        $active = Q()->get->get("active");

        if (!is_array($ids)) {
            return $this->error("至少选中一项,删除失败!", url("consoles_lists", array("con" => "Standard", "active" => $active)));
        }
        $standardDM = new \Admin\DModel\StandardDModel();
        foreach ($ids as $id) {
            $standardEn = $standardDM->find($id);
            $standardDM->remove($standardEn);
        }
        $standardDM->flush();;

        return $this->success("删除成功", url("consoles_lists", array("con" => "Standard", "active" => $active)));
    }

    /**
     * 价值维度
     * @return \phpex\Foundation\Response
     */
    public function allStandards() {
        $get = Q()->get->all();
        $standardDM = new \Admin\DModel\StandardDModel();
        $active = $get['active'] ?: "Achievement";

        //成果
        $where = "sc.namesEn='" . 'Achievement' . "' AND s.sid=" . $this->sid . "AND s.status=1";
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where)
            ->order("s.acorn", "DESC")
            ->order("s.hot", "DESC")
            ->getArray(true);
        $where1 = "sc.namesEn='" . 'Report' . "' AND s.sid=" . $this->sid . "AND s.status=1";
        $lists1 = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where1)
            ->order("s.acorn", "DESC")
            ->order("s.hot", "DESC")
            ->getArray(true);

        $where2 = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid . "AND s.status=1 AND s.types=0";
        $lists2 = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s,sc")
            ->where($where2)
            ->order("s.acorn", "DESC")
            ->order("s.hot", "DESC")
            ->getArray(true);
        $this->assign("active", $active);
        $this->assign("lists2", $lists2);
        $this->assign("lists", $lists);
        $this->assign("lists1", $lists1);

        if ($get['active'] == "Achievements") {//成果
            return $this->display();
        }
        if ($get['active'] == "Ability") {//能力
            return $this->display("ability.latte");
        }
        if ($get['active'] == "MentalityAndCulture") {//心态和文化
            return $this->display("mentalityAndCulture.latte");
        }
        return $this->display("lists2.latte");

    }

    /**
     * 修改价值维度-废弃
     * @return \phpex\Foundation\Response
     */
    public function addStandards() {
        $StandardClassifyDM = new \Admin\DModel\StandardClassifyDModel();
        $standardDM = new \Admin\DModel\StandardDModel();
        $post = Q()->post->all();
        if ($post['names1'] == null) {
            return $this->ajaxReturn(array("status" => "n", "info" => "名称必填"));
        }
        $title = $StandardClassifyDM->name("sc")->select("sc")->where("sc.namesEn='" . $post['active'] . "'")->getOneArray();
        if (!$title) {
            return $this->ajaxReturn(array("status" => "n", "info" => "数据出错"));
        }

        if ($post['acorn1'] > 100) {
            return $this->ajaxReturn(array("status" => "n", "info" => "该项总分已经超过100分，请重新分配"));
        }

        $post['names'] = $post['names1'];
        $post['acorn'] = $post['acorn1'];
        $post['sid'] = $this->sid;
        $post['addTime'] = nowTime();
        $post['status'] = 1;
        $post['classify'] = $title['id'] ?: 0;
        $post['subClassify'] = 0;
        $post['hot'] = $post['hot'] ?: 0;//热度
        $post['sNo'] = $title['abbreviation'] . rand_string(6, 6) ?: "XZ" . rand_string(6, 6);
        $standardDM->create($post, $standard = $standardDM->newEntity());
        $standardDM->save($standard)->flush();

        $url = url("consoles_standard_modifyStandards", "active=" . $post['active']);
        return $this->ajaxReturn(array("status" => "y", "info" => "添加成功", "url" => $url));

    }

    /**
     * 修改价值维度
     * @return \phpex\Foundation\Response
     */
    public function modifyStandards() {
        $this->flushUser();
        $active = Q()->get->get("active");
        $standardDM = new \Admin\DModel\StandardDModel();
        $stadardClassDM = new \Admin\DModel\StandardClassifyDModel();
        $title = $stadardClassDM->name("sc")->select("sc")->where("sc.namesEn='" . $active . "'")->getOneArray();
        if (Q()->isGet()) {
            $where = "sc.namesEn='" . $active . "' AND s.sid=" . $this->sid;
            $lists = $standardDM->name("s")
                ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
                ->select("s,sc")
                ->where($where)
                ->getArray(true);
            $this->assign("title", $title['names']);
            $this->assign("lists", $lists);
            $this->assign("active", $active);
            return $this->display();
        }
        $post = Q()->post->all();
        $acorn1 = 0;
        foreach ($post['names'] as $key => $item) {
            if (!preg_match("/^[0-9]*$/", $post['acorn1'][$key])) {
                return $this->error("请输入正确的数字");
            }
            $acorn1 += $post['acorn1'][$key];
        }
        if ($acorn1 > 100 || $acorn1 < 100) {
            return $this->error("总分超过100或者不足100分");
        }
        foreach ($post['names'] as $key => $item) {
            if (!$item) {
                return $this->error("名称不能为空");
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
                $post['types'] = 0;
                $standardDM->create($post, $standard = $standardDM->newEntity());
                $standardDM->save($standard)->flush();
                continue;
            }
            if (!$post['names'][$key]) {
                return $this->error("名称不能为空");
            }
            $lists->setNames($post['names'][$key]);
            $lists->setAcorn($post['acorn1'][$key]);
            $standardDM->save($lists)->flush();
        }
        return $this->success("修改成功");
    }

}