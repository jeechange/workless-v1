<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/27
 * Time: 10:52
 */

namespace Consoles\Controller;


use Admin\DModel\CompanyDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StaffGroupDModel;
use Admin\DModel\UserDModel;

class StaffGroupController extends CommonController {


    public function lists($sid) {
        if(!$sid)  $sid = $this->sid ?: 0;
        $this->assign("types", "xiaozu");

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addKeyword("g.names,u1.fullName,u2.fullName", "小组名称，组长或者副组长");

        $where = "g.sid = {$sid}";
        $search->bindData(Q()->get->all());
        $search->build($where, $searchForm, $params);

        $groupDM = StaffGroupDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $groupLists = $groupDM->name("g")
            ->select("g,u1.fullName as u1_fullName,u2.fullName as u2_fullName")
            ->leftJoin("User", "u1", "g.leader = u1.id")
            ->leftJoin("User", "u2", "g.helper = u2.id")
            ->where($where)
            ->setParameter($params)
            ->setPage()
            ->getArray(true);

        foreach ($groupLists as $k => $v) {
            if ($v['g_members']) {
                $str = "";
                $members = explode(",", $v['g_members']);
                foreach ($members as $kk => $vv) {
                    $meb = $userDM->find($vv);
                    if (!$meb) {
                        continue;
                    } else {
                        $str .= $meb->getFullName() . " ";
                    }
                }
                $groupLists[$k]['g_members'] = $str;
            }
        }

        $this->assign([
            "search" => $searchForm,
            "groupLists" => $groupLists,
        ]);
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

        $staffDM = StaffDModel::getInstance();
        if (Q()->isGet()) {
            $members = $staffDM->workerList($sid, "members", array(), 999);
            $this->assign("members", $members);
            $leader = $staffDM->workerList($sid, "leader", array(), 1);
            $this->assign("leader", $leader);
            $helper = $staffDM->workerList($sid, "helper", array(), 1);
            $this->assign("helper", $helper);
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post["leader"]) {
            return $this->error("组长不能为空");
        }
        if ($post["leader"] == $post["helper"]) {
            return $this->error("组长和副组长不能是同一个人");
        }
//        $members = explode(",", $post["members"]);
        $post['members'] = implode(",", $post["members"]);

        if (!$post["names"]) return $this->error("小组名称不能为空");

        $groupDM = StaffGroupDModel::getInstance();

        $hasGroup = $groupDM->findOneBy(array("sid" => $sid, "names" => $post["names"]));

        if ($hasGroup) return $this->error("存在重复的小组名称");
        $group = $groupDM->newEntity();

        $post['leader'] = $post['leader'][0];
        $post['helper'] = $post['helper'][0];
//        $post["members"] = join(",", array_unique($members));
        $groupDM->create($post, $group);

        $group->setAddTime(nowTime());
        $group->setSid($sid);
        $groupDM->add($group)->flush($group);

        return $this->success("添加成功");
    }

    public function modify($id) {
        $groupDM = StaffGroupDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        if (Q()->getMethod() == "GET") {
            $curGroup = $groupDM->name("g")->where("g.id=$id")->getOneArray();
            if (!$curGroup) {
                return $this->error("找不到此小组");
            }

            $members = $staffDM->workerList($this->sid, "members", $curGroup['members'], 999);
            $leader = $staffDM->workerList($this->sid, "leader", $curGroup['leader'], 1);
            $helper = $staffDM->workerList($this->sid, "helper", $curGroup['helper'], 1);

            $this->assign([
                "curGroup" => $curGroup,
                "members" => $members,
                "leader" => $leader,
                "helper" => $helper,
            ]);

            return $this->display();
        }

        $post = Q()->post->all();

        if (!$post["leader"]) {
            return $this->error("组长不能为空");
        }
        if ($post["leader"] == $post["helper"]) {
            return $this->error("组长和副组长不能是同一个人");
        }

        $post['members'] = implode(",", $post["members"]);

        if (!$post["names"]) return $this->error("小组名称不能为空");

        $hasGroup = $groupDM->name("g")->where("g.sid = {$this->sid} and g.names = '{$post['names']}' and g.id != $id")->getArray();

        if ($hasGroup) return $this->error("存在重复的小组名称");
        $curGroup = $groupDM->find($id);

        $post['leader'] = $post['leader'][0];
        $post['helper'] = $post['helper'][0];

        $curGroup->setNames($post['names']);
        $curGroup->setSubject($post['subject']);
        $curGroup->setAddTime(nowTime());
        $curGroup->setSid($this->sid);
        $curGroup->setLeader($post['leader']);
        $curGroup->setHelper($post['helper']);
        $curGroup->setMembers($post['members']);
        $curGroup->setStatus($post['status']);

        $groupDM->save($curGroup)->flush($curGroup);

        return $this->success("修改成功");
    }


    public function deleteMul() {
        $groupDM = StaffGroupDModel::getInstance();
        $post = Q()->post->all();
        $ids = $post['ids'];
        foreach ($ids as $k => $v) {
            $del = $groupDM->find($v);
            if (!$del) {
                continue;
            }
            $groupDM->remove($del)->flush();
        }
        return $this->success("删除成功");
    }

}
