<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-03-28
 * Time: 14:23
 */

namespace Consoles\Controller;


class AcornAuditDetailController extends CommonController {

    public function lists() {
        $auditDetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $types = Q()->get->get("types") ?: "audit";


        $seach = $this->search();
        $seach->labelType("placeholder");
        $seach->addKeyword("ad.fromUser", "申请人");
        $seach->addSelect("ad.types", "类型", array(0 => "审核", 1 => "抄送"), "全部");
        $seach->addSelect("ad.status", "状态", array(0 => "审核中/未阅读", 1 => "已审核/已阅读", 2 => "不通过"), "全部");
        $seach->addExport("excel", "导出到excel");
        $seach->bindData(Q()->get->all()); //绑定查询数据
        $where = "ad.sid=" . $this->sid . "AND ad.userId=" . $this->getUser("id");
        if ($types == "audit") {
            $where .= "AND ad.status=0";
            $titel = "待审核列表";
        } elsE {
            $where .= "AND ad.status=1";
            $titel = "已审核列表";
        }

        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm
        if (Q()->get->has("__export__excel")) {
            return $this->exprotLists($where, $params, $titel);
        }

        $lists = $auditDetailDM->name("ad")
            ->where($where)
            ->setParameter($params)
            ->order("ad.id", "DESC")
            ->setPage()
            ->getArray(true);
        $this->assign("lists", $lists);
        $this->assign("types", $types);
        return $this->display();
    }

    public function exprotLists($where, $params, $titel) {
        $auditDetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $lists = $auditDetailDM->name("ad")
            ->where($where)
            ->setParameter($params)
            ->order("ad.id", "DESC")
            ->setPage()
            ->getArray(true);
        if (!$lists) {
            return $this->error("暂无数据导出");
        }

        foreach ($lists as &$item) {
            $item['sNames'] = "[" . $item['ad_scNames'] . "]" . $item['ad_sNames'];
        }


        $headder = array(
            'A' => 'ad_addTime:时间',
            'B' => 'sNames:标准',
            'C' => 'ad_acorn:积分',
            'D' => 'ad_fromUser:申请人%s',
            'E' => 'typeMemo:类型%s',
            'F' => 'statusMemo:状态%s',
            'G' => 'a_addTime:发放时间',
        );
        return excelExprot(sprintf("{$titel}%s-%d.xls", date("YmdH"), rand(10, 999)), $headder, $lists);
    }


}
