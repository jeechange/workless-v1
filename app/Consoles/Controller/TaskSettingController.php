<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/7/20
 * Time: 10:52
 */

namespace Consoles\Controller;


use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TaskSettingDModel;
use Admin\Entity\TaskGroup;

class TaskSettingController extends CommonController {


    public function lists() {

        $taskSettingDM = TaskSettingDModel::getInstance();


        $lists1 = $taskSettingDM->getLists($this->sid, 1);
        $lists2 = $taskSettingDM->getLists($this->sid, 2);
        $lists3 = $taskSettingDM->getLists($this->sid, 3);
        $lists4 = $taskSettingDM->getLists($this->sid, 4);
        $lists5 = $taskSettingDM->getLists($this->sid, 5);
        $lists6 = $taskSettingDM->getLists($this->sid, 6);
        $lists7 = $taskSettingDM->getLists($this->sid, 7);

        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("lists3", $lists3);
        $this->assign("lists4", $lists4);
        $this->assign("lists5", $lists5);
        $this->assign("lists6", $lists6);
        $this->assign("lists7", $lists7);
        return $this->display();

    }

    public function settings() {

        $post = Q()->post->all();

        $taskSettingDM = TaskSettingDModel::getInstance();
        $lists3 = $taskSettingDM->getLists($this->sid, 3, "object");
        $lists4 = $taskSettingDM->getLists($this->sid, 4, "object");
        $lists5 = $taskSettingDM->getLists($this->sid, 5, "object");
        $lists6 = $taskSettingDM->getLists($this->sid, 6, "object");
        $lists7 = $taskSettingDM->getLists($this->sid, 7, "object");

        $lists3[0]->setNames($post["list3_0_names"]);
        $lists3[1]->setNames($post["list3_1_names"]);
        $lists4[0]->setNames($post["list4_0_names"]);
        $lists4[1]->setNames($post["list4_1_names"]);
        $lists5[0]->setNames($post["list5_0_names"]);
        $lists5[1]->setNames($post["list5_1_names"]);
        $lists6[0]->setNames($post["list6_0_names"]);
        $lists6[1]->setNames($post["list6_1_names"]);
        $lists6[2]->setNames($post["list6_2_names"]);
        $lists7[0]->setNames($post["list7_0_names"]);
        $lists7[1]->setNames($post["list7_1_names"]);
        if ($post["list7_1_names"] > 0) $lists7[2]->setNames($post["list7_2_names"]);
        if ($post["list7_1_names"] > 1) $lists7[3]->setNames($post["list7_3_names"]);
        if ($post["list7_1_names"] > 2) $lists7[4]->setNames($post["list7_4_names"]);

        $taskSettingDM->flush();

        return $this->success("修改成功", url("consoles_lists", "con=taskSetting"));

    }

    public function add() {

        $types = Q()->get->get("types") ?: 1;
        if (Q()->isGet()) {
            return $this->display($types == 1 ? "" : "add2");
        }

        $post = Q()->post->all();

        $taskSettingDM = TaskSettingDModel::getInstance();


        $taskSetting = $taskSettingDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"], "types" => $types));

        if ($taskSetting) return $this->error("存在同名的设置，请检查");

        $post["types"] = $types;
        $post["sid"] = $this->sid;


        $taskSettingDM->create($post, $taskSetting = $taskSettingDM->newEntity());

        $taskSettingDM->add($taskSetting)->flush($taskSetting);

        return $this->success("添加成功");


    }

    public function modify($id) {
        $taskSettingDM = TaskSettingDModel::getInstance();

        $taskSetting = $taskSettingDM->find($id);
        $types = Q()->get->get("types") ?: 1;
        if (!$taskSetting || $types != $taskSetting->getTypes()) return $this->error("记录获取失败");

        if (Q()->isGet()) {
            $this->assign("taskSetting", $taskSetting);

            return $this->display($types == 1 ? "" : "modify2");
        }
        $post = Q()->post->all();
        $oldtaskSetting = $taskSettingDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"], "types" => $types));
        if ($oldtaskSetting && $oldtaskSetting->getId() != $id) return $this->error("存在同名的设置，请检查");

        $post["types"] = $types;
        $post["sid"] = $this->sid;


        $taskSettingDM->create($post, $taskSetting);

        $taskSettingDM->save($taskSetting)->flush($taskSetting);

        return $this->success("修改成功");

    }


    public function groupAdd() {

        $post = Q()->post->all();


        if (!$post["names"]) return $this->error("项目名称必须填写");
        if (!$post["addTime"]) $post["addTime"] = date("Y-m-d");


        if (!$this->sid) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请先加入一个企业，再进行任务发布等相关操作"));
        }

        $id = $post["id"] ?: 0;

        $groupDM = TaskGroupDModel::getInstance();
        if ($id) {
            /** @var TaskGroup $group */
            $group = $groupDM->findOneBy(array("sid" => $this->sid, "id" => $id));
            if (!$group) {
                return $this->ajaxReturn(array("status" => "n", "info" => "项目信息获取失败"));
            }
            if (isset($post["del"]) && $post["del"] == 1) {

                $groupDM->remove($group)->flush($group);
                return $this->ajaxReturn(array("status" => "y"));
            }
            if ($post["names"] == $group->getNames()) {
                return $this->ajaxReturn(array("status" => "n", "info" => "项目名称未发生改成"));
            }
        } else {
            $group = $groupDM->newEntity();
        }
        $has = $groupDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($has) {
            return $this->ajaxReturn(array("status" => "n", "info" => "任务项目名称必须唯一"));
        }

        $group=TaskGroupDModel::addGroup($post,$this->sid,$this->getUser("id"));

        $html = "<li data-names='{$group->getNames()}' data-val='{$group->getId()}'><div class='title-box'><span class='group-title' >{$group->getNames()}</span></div><div class='select-action' onclick='selectGroup.call(this,event)'>选择</div><div class='manage-action modify-action' onclick='modifyGroup.call(this,event)'>修改</div><div class='manage-action delete-action' onclick='deleteGroup.call(this,event)'>删除</div></li>";
        return $this->ajaxReturn(array("status" => "y", "data" => array("id" => $group->getId(), "html" => $html)));

    }

}
