<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/27
 * Time: 15:03
 */

namespace Consoles\Controller;


use Admin\DModel\AcornAuditDModel;
use Admin\DModel\CompanyDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\StaffStationDModel;

class StaffStationController extends CommonController {

    public function lists($sid) {
        if(!$sid)  $sid = $this->sid ?: 0;
        $this->assign("types", "zhiwei");

//        $options = DepartmentDModel::getInstance()->getOptions($sid, Q()->get->get("did") ?: 0);
        $search = $this->search();
        $search->labelType("placeholder");
//        $search->addSelectOptions("d.id", "部门", '<option value="">=全部=</option> ' . $options); //添加select方式的表单

        $stationDM = StaffStationDModel::getInstance();

        $li = $stationDM->riseAcorn(2, 8);

        $this->search()->bindData(Q()->get->all()); //绑定查询数据
        $where = "s.sid =" . $sid;
//        $this->search()->build($where, $searchForm, $params); //构建$where和$searchForm

        $lists = $stationDM->name("s")->select("s")
            ->where($where)
//            ->setParameter($params)
            ->setPage()->data_sort()
            ->getArray(true);
        $this->assign("lists", $lists);

        $company = CompanyDModel::getInstance()->find($sid);
        $this->assign("company", $company);
        $this->assign("sid", $sid);

        return $this->display();
    }


    public function add() {
        $sid = Q()->get->all()['sid'];
        if($sid==null || $sid=='') return $this->error("错误的请求：缺少企业标识");

        $company = CompanyDModel::getINstance()->findOneBy(array("id"=>$sid));
        $this->assign("company", $company);

        $departmentDM = DepartmentDModel::getInstance();
        if (Q()->isGet()) {
//            $parents = $departmentDM->getOptions($sid, 0);
//            $this->assign("parents", $parents);
            return $this->display();
        }

        $stationDM = StaffStationDModel::getInstance();

        $post = Q()->post->all();

        if(trim($post['names'])=='') return $this->error("职位名称	不能为空");

        $same = $stationDM->name("s")->where("s.names='{$post['names']}' and s.sid={$sid}")->getArray();
        if ($same) {
            return $this->error("已存在相同职位名");
        }
        $stationDM->create($post, $stationEN = $stationDM->newEntity());
        if (!$stationDM->check($post, $stationEN)) {
            return $this->error($stationDM->getError());
        }
        $stationEN->setSid($sid);
        $departmentDM->add($stationEN)->flush($stationEN);

        return $this->success("添加成功");
    }

    public function modify($id) {

        $stationDM = StaffStationDModel::getInstance();
//        $departmentDM = DepartmentDModel::getInstance();

        $stationEN = $stationDM->find($id);
        if (!$stationEN) {
            return $this->error("记录不存在", url("consoles_lists", "con=Department"));
        }

        if (Q()->isGet()) {
//            $parents = $departmentDM->getOptions($this->sid, $stationEN->getDepartment());
//            $this->assign("parents", $parents);
            $this->assign("stationEN", $stationEN);
            return $this->display();
        }

        $post = Q()->post->all();

        if(trim($post['names'])=='') return $this->error("职位名称	不能为空");

        $same = $stationDM->name("s")->where("s.names='{$post['names']}' and s.sid={$this->sid} and s.id!={$stationEN->getId()}")->getArray();
        if ($same) {
            return $this->error("已存在相同职位名");
        }

        $stationDM->create($post, $stationEN);
        if (!$stationDM->check($post, $stationEN)) {
            return $this->error($stationDM->getError());
        }
        $stationDM->save($stationEN)->flush($stationEN);
        return $this->success("修改成功");
    }

    public function deleteMul() {
        $stationDM = StaffStationDModel::getInstance();
        $post = Q()->post->all();
        $ids = $post['ids'];
        foreach ($ids as $k => $v) {
            $del = $stationDM->find($v);
            if (!$del) {
                continue;
            }
            $stationDM->remove($del)->flush();
        }
        return $this->success("删除成功");
    }

}
