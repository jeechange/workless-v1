<?php

namespace MobileConsoles\Controller;

use Admin\DModel\TargetDetailDModel;
use Admin\DModel\TargetDModel;
use Admin\DModel\TargetMyDModel;
use Admin\DModel\TargetOthersDModel;

class TargetController extends CommonController {

    private  $menu="target";
    public function _initialize() {
        parent::_initialize();
        $this->assign("menu", $this->menu);
    }
    public function lists() {
        $get = Q()->get->all();
        $this->assign("types", $get["types"]);

        $targetDM = TargetDModel::getInstance();
        $targetMyDM = TargetMyDModel::getInstance();
        $targetDetailDM = TargetDetailDModel::getInstance();

        $this->assign("ASM", $targetMyDM->getALLStatusMemo());

        $where = "t.sid=" . $this->sid . "AND tm.userId=" . $this->getUser('id');
        $lists = $targetDM->name("t")
            ->leftJoin("TargetMy", "tm", "tm.tId=t.id")
            ->select("t,tm")
            ->where($where)
            ->order("t.id", "DESC")
            ->getArray(true);
        foreach ($lists as &$item) {
            $item['targetDetail'] = $targetDetailDM->name("td")->select("td")->where("td.mId=" . $item['tm_id'] . "AND td.userId=" . $this->getUser('id'))->getArray(true);
        }

        $this->assign("lists", $lists);

        return $this->display();
    }

    public function ajaxOthers() {
        $post = Q()->post->all();

        $targetMyDM = TargetMyDModel::getInstance();
        $targetOthersDM = TargetOthersDModel::getInstance();

        $lists = $targetOthersDM->name("to")->select("to,u.fullName")
            ->leftJoin("User", "u", "u.id = to.userId")
            ->where("to.mId = {$post['mid']} and to.tdId = {$post['did']} and to.status = 1")
            ->getArray(true);

        $arr = '';
        if ($lists) {
            $types = 1;
            foreach ($lists as $v) {
                $html = '';
                $html .= '<div style="width:100%; text-align:center; color:#0064da;">' . $v['fullName'] . '</div>';
                $html .= '<div>自评分数：<span>' . $v['to_score'] . '</span>分</div>';
                $html .= '<div>';
                $html .= '结果汇报：';
                $html .= '<div>' . $v['to_content'] . '</div>';
                $html .= '</div>';

                $arr .= $html;
            }
        } else {
            $types = 2;
        }

        return $this->ajaxReturn(array("status" => "y", "date" => $arr, "types" => $types));
    }

}
