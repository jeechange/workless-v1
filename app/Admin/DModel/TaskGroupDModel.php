<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TaskGroupDModel extends DModel {

    private $statusMemo = array(
        "1" => "进行中",
        "0" => "已完成",
        "2" => "准备中",
        "3" => "已暂停",
        "4" => "已取消"


    );

    /**
     * 自动填充规则
     */
    public function _fill() {
        //$this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {

    }

    protected function resolveArray(&$result) {

        if (!$this->scalar) {

            $result["statusMemo"] = $this->getStatusMemo($result["status"]);
            $result["membersMemo"] = $this->executorMemo($result["members"]);
        } else {
            $result["statusMemo"] = $this->getStatusMemo($result["g_status"]);
            $result["membersMemo"] = $this->executorMemo($result["g_members"]);

        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskGroup();
    }


    public function getList($sid, $row = 20) {
        $lists = $this->name("tg")->where("tg.sid=$sid")->setMax($row)->order("tg.addTime", "desc")->getArray();
        return $lists ?: array();
    }

    public function getGroupHtml($sid) {
        if (!$sid) return "";
        $lists = $this->name("tg")->where("tg.sid=$sid")->setMax()->order("tg.addTime", "desc")->order("tg.id", "desc")->getArray();

        if (!$lists) return "";

        $html = "";

        foreach ($lists as $item) {
            $html .= "<li data-names='{$item["names"]}' data-val='{$item["id"]}'><div class='title-box'><span class='group-title' >{$item["names"]}</span></div><div class='select-action' onclick='selectGroup.call(this,event)'>选择</div><div class='manage-action modify-action' onclick='modifyGroup.call(this,event)'>修改</div><div class='manage-action delete-action' onclick='deleteGroup.call(this,event)'>删除</div></li>";
        };
        return $html;
    }

    public function getStatusMemo($key = null) {
        return $this->statusMemo[$key] ? $this->statusMemo[$key] : $this->statusMemo;
    }

    /**
     * 获取项目名称
     * @param $sid
     * @param $id
     * @return string
     */
    public function getGroupName($sid, $id) {
        $lists = $this->name("tg")
            ->select("tg")
            ->where("tg.sid=" . $sid . " AND tg.id=" . $id)
            ->getOneArray();
        return $lists['names'] ?: "";
    }

    public function executorMemo($executors) {
        if (!$executors) return "";

        $executorIds = explode(",", $executors);
        $staffDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $staffDM->find($id);
            if ($user) $users[] = $user->getFullName();
        }
        return join(",", $users);
    }

    /**
     * 添加项目组2019-12-14
     * @param $post
     * @param $sid
     * @throws \Exception
     */
    public static function addGroup($post, $sid, $userId) {
        $self = new \Admin\DModel\TaskGroupDModel();
        $group = $self->newEntity();
        $group->setNames($post["names"]);
        $group->setAddTime(nowTime());
        $group->setEndTime(new \DateTime(date("Y-m-d", $post["typesTime2"])));
        $group->setSid($sid);
        $group->setResponsible($post['leader'] ? $post['leader'][0] : $userId);
        $group->setMembers($post['members']);
        $group->setMemo($post["memo"]);
        $group->setSort($post["sort"] ?: 0);
        $group->setUserId($userId);
        $group->setProgress(0);
        $group->setStatus($post['status']);//项目状态
        $self->add($group)->flush();
        return $group;
    }

}
