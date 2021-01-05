<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/26
 * Time: 15:53
 */

namespace Consoles\Controller;


use Admin\DModel\CompanyDModel;
use Admin\DModel\DepartmentDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffStationDModel;

class DepartmentController extends CommonController {

    public function lists($sid) {
        if(!$sid)  $sid = $this->sid ?: 0;

        $this->assign("types", "bumen");

        $departmentDM = DepartmentDModel::getInstance();
        $departmentDM->getParentName = true;
        $lists = $departmentDM->name("d")->select("d,count(s.id) as a_total")
            ->leftJoin("Staff", "s", "s.department=d.id")
            ->groupBy("d.id")
            ->where("d.sid =" . $sid)
            ->data_sort()
            ->setPage()
            ->getArray(true);

        $this->assign("lists", $lists);
        $this->assign("sid", $sid);
        $company = CompanyDModel::getInstance()->find($sid);
        $this->assign("company", $company);

        return $this->display();

    }

    public function add() {
        $sid = Q()->get->all()['sid'];
        if($sid==null || $sid=='') return $this->error("错误的请求：缺少企业标识");

        $company = CompanyDModel::getINstance()->findOneBy(array("id"=>$sid));
        $this->assign("company", $company);

        $departmentDM = DepartmentDModel::getInstance();
        if (Q()->isGet()) {
            $parents = $departmentDM->getOptions($sid, 0);
            $this->assign("parents", $parents);
            return $this->display();
        }
        $post = Q()->post->all();

        if (!$post['names']) {
            return $this->error("请填写部门名称");
        }

        $same = $departmentDM->name("d")->where("d.sid={$sid} and d.names='{$post['names']}'")->getArray();
        if ($same) {
            return $this->error("存在相同的部门名称");
        }

        $departmentDM->create($post, $departEN = $departmentDM->newEntity());
        if (!$departmentDM->check($post, $departEN)) {
            return $this->error($departmentDM->getError());
        }
        $departEN->setSid($sid);
        $departmentDM->add($departEN)->flush($departEN);

        return $this->success("添加成功");
    }

    public function modify($id) {
        $this->flushUser();
        $departmentDM = DepartmentDModel::getInstance();

        $departEN = $departmentDM->find($id);
        if (!$departEN) {
            return $this->error("记录不存在", url("consoles_lists", "con=Department"));
        }

        if (Q()->isGet()) {
            $parents = $departmentDM->getOptions($departEN->getSid(), $departEN->getParentid());
            $this->assign("parents", $parents);
            $this->assign("departEN", $departEN);
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post['names']) {
            return $this->error("请填写部门名称");
        }

        $same = $departmentDM->name("d")->where("d.sid={$this->sid} and d.names='{$post['names']}' and d.id!={$departEN->getId()}")->getArray();
        if ($same) {
            return $this->error("存在相同的部门名称");
        }

        if ($post['parentid'] == $departEN->getId()) {
            return $this->error("不能选择自身");
        }

        $parentid = $departmentDM->Superior($departEN->getId(), $post['parentid']);
        if (!$parentid) {
            return $this->error("不能选择自身下级部门");
        }

//        if($post['status'] == 0){
//            $a = 1;
//            $pid = $id;
//            $arr = array();
////            dump($id);exit;
//            while($a != null){
//                $a = $departmentDM->name("d")->where("d.parentid={$pid}")->getArray();
//                $arr[] = $pid = $a['id'];
//            }
//            dump($arr);exit;
//        }

        $departmentDM->create($post, $departEN);
        if (!$departmentDM->check($post, $departEN)) {
            return $this->error($departmentDM->getError());
        }

        $departmentDM->save($departEN)->flush();
        return $this->success("修改成功");
    }

    //删除部门
    public function deleteMul() {
        $post = Q()->post->all();
        $ids = $post['ids'];
        foreach ($ids as $key => $value) {
            $id = $value;
            $departmentDM = DepartmentDModel::getInstance();
            $findlower = $departmentDM->findOneBy(array("parentid" => $id));
            if ($findlower) {
                return $this->error("此部门含有下级部门");
            }
            $staffDM = StaffDModel::getInstance();
            $staffEN = $staffDM->name("s")->where("s.department={$id} and s.sid={$this->sid} and s.status=1")->getArray();
            foreach ($staffEN as $k => $v) {
                $isSingle = $staffDM->name("s")
                    ->where("s.sid={$this->sid} and s.userId={$v['userId']} and s.id!={$v['id']} and s.department!=0")
                    ->getArray();
                if ($isSingle) {
                    $del = $staffDM->find($v['id']);
                    $staffDM->remove($del)->flush();
                } else {
                    $modify = $staffDM->find($v['id']);
                    $data = array("department" => 0);
                    $staffDM->create($data, $modify);
                    $staffDM->save($modify)->flush();
                }
            }

            $stationDM = StaffStationDModel::getInstance();
            $stationEN = $stationDM->name("st")->where("st.department={$id}")->getArray();
            if ($stationEN) {
                foreach ($stationEN as $kk => $vv) {
                    $removeStation = $stationDM->find($vv['id']);
                    $stationDM->remove($removeStation)->flush();
                }
            }

            $departmentEN = $departmentDM->find($id);
            $departmentDM->remove($departmentEN)->flush();
        }
        return $this->success("删除成功");
    }

}
