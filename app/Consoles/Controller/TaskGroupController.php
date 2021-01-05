<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-22
 * Time: 15:38
 */

namespace Consoles\Controller;


use Admin\DModel\DepartmentDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TaskGroupCommentDModel;
use Admin\DModel\TaskGroupDiscussDModel;
use Admin\DModel\TaskGroupDModel;
use Admin\DModel\TaskGroupDocDModel;
use Admin\DModel\TaskGroupDocVersionsDModel;
use Admin\DModel\TaskGroupFilesDModel;
use Admin\Entity\TaskGroup;
use Admin\Entity\TaskGroupDoc;
use Admin\Entity\TaskGroupFiles;

class TaskGroupController extends CommonController {

    /**
     * 项目列表
     * @return \phpex\Foundation\Response
     */
    public function lists() {
        $status = Q()->get->get("active") ?: 0;
        $types = Q()->get->get("types") ?: 0;
        $where = "g.sid=" . $this->sid . "AND g.status=" . $status;
        switch ($types) {
            case 1:
                $title = "我创建的";
                //我创建的
                $where .= " AND g.userId=" . $this->getUser("id");
                break;
            case 2:
                $title = "我参与的";
                //我参与的
                $where .= " AND( (REGEXP( g.members,'(^|\,)(" . $this->getUser("id") . ")(\,|$)')=1) OR g.responsible=" . $this->getUser("id") . ")";
                break;
            default:
                $title = "项目管理";
                break;
        }

        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $lists = $groupDM->name("g")
            ->leftJoin("User", "u", "u.id=g.responsible")
            ->select("g,u.fullName as g_responsible")
            ->where($where)
            ->order("g.sort")
            ->order("g.id", "DESC")
            ->setPage()
            ->getArray(true);
        $lists1 = array();
        $lists2 = array();
        $nowTime = date("Y-m-d H:i:s");

        foreach ($lists as $key => &$item) {
            $totalSql = "SELECT  `status`, COUNT(*)  AS total  FROM  `jee_task` WHERE `sid` ={$this->sid} AND `pid`={$item['g_id']}  GROUP BY `status`";
            DM()->execute($totalSql);
            $stat = DM()->getLastStatement();
            $total = $stat->fetchAll();
            $item['total'] = 0;//总数
            $item['finish'] = 0;//已完成
            $item['overdue'] = 0;//逾期
            $item['execution'] = 0;//执行中
            $item['cancel'] = 0;//取消
            foreach ($total as $value) {
                $item['total'] += $value['total'];//总数
                if ($value['status'] == 0 && $value['status'] == 1) {//执行中
                    $item['execution'] = $value['total'];
                }
                if ($value['status'] == 2) {
                    $item['cancel'] = $value['total'];//取消
                }
                if ($value['status'] == 3) {
                    $item['finish'] = $value['total'];//已完成
                }
            }
            //逾期任务
            $overdueSql = "SELECT COUNT(*)  AS total1  FROM  `jee_task` WHERE `sid` ={$this->sid} AND `status`=0 AND `pid`={$item['g_id']}  AND `deadline`<'" . $nowTime . "'";

            DM()->execute($overdueSql);
            $stats = DM()->getLastStatement();
            $overdue = $stats->fetch();
            $item['overdue'] = $overdue['total1'];

//            //项目更新完成状态(暂时不需要20190122)
//            if ($item['progress'] == 100) {
//                $updateSql = "UPDATE `jee_task_group`  SET `status` =0 WHERE `sid`={$this->sid} AND `id`={$item['g_id']} AND `status`=1";
//                $d = DM();
//                $d->execute($updateSql);
//            } else {
//                $updateSql = "UPDATE `jee_task_group`  SET `status` =1 WHERE `sid`={$this->sid} AND `id`={$item['g_id']} AND `status`=0";
//                $d = DM();
//                $d->execute($updateSql);
//            }
            //排序
            if (is_null($item["g_sort"]) || $item["g_sort"] <= 0) {
                $lists1[] = $item;
            }
            if ($item["g_sort"] > 0) {
                $lists2[] = $item;
            }
        }
        $lists = array_merge($lists2, $lists1);//排序重新组合
        $this->assign("lists", $lists);
        $this->assign("active", $status);
        $this->assign("title", $title);
        $this->assign("types", $types);
        return $this->display();
    }

    /**
     * 项目的任务
     * @param $id
     */
    public function taskLists($id) {
        $active = 1;
        $taskDM = TaskDModel::getInstance();
        $taskDM->setRewardId($this->getUser("id"));
        $where = "t.sid =" . $this->sid . " AND t.pid=" . $id;
        $lists = $taskDM->name("t")->select("t,s")
            ->leftJoin("Standard", "s", "t.standardId = s.id")
            ->where($where)
            ->setPage()
            ->data_sort()
            ->order("t.executor", "DESC")
            ->order("t.id", "DESC")
            ->getArray(true);

        $this->assign("id", $id);
        $this->assign("active", $active);
        $this->assign("lists", $lists);
        return $this->display("taskLists");
    }

    /**
     * 项目的添加
     * @return \phpex\Foundation\Response
     */
    public function add() {
        $staffDM = StaffDModel::getInstance();
        $groupDM = TaskGroupDModel::getInstance();

        if (Q()->isGet()) {
            $members = $staffDM->workerList($this->sid, "members", array(), 999);
            $leader = $staffDM->workerList($this->sid, "leader", array(), 1);
            $status = $groupDM->getStatusMemo();

            $this->assign("members", $members);
            $this->assign("leader", $leader);
            $this->assign("status", $status);
            return $this->display();
        }

        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();

        if (!$post["names"]) {
            DM()->getManager()->rollback();
            return $this->error("项目名称必须填写");
        }
        if (!$this->sid) {
            DM()->getManager()->rollback();
            return $this->error("请先加入一个企业，再进行任务发布等相关操作");
        }
        if (!$post["leader"]) {
            DM()->getManager()->rollback();
            return $this->error("负责人不能为空");
        }
        if (!$post["members"]) {
            DM()->getManager()->rollback();
            return $this->error("成员不能为空");
        }
        if (in_array($post["leader"], $post["members"])) {
            DM()->getManager()->rollback();
            return $this->error("負責人跟成员重复了");
        }
        if (!$post["startTime"]) {
            DM()->getManager()->rollback();
            return $this->error("开始时间不能为空");
        }
        if ($post["process"] > 100) {
            DM()->getManager()->rollback();
            return $this->error("阶段进程不能大于100%");
        }
        $post['members'] = implode(",", $post["members"]);
        $has = $groupDM->findOneBy(array("sid" => $this->sid, "names" => $post["names"]));
        if ($has) {
            DM()->getManager()->rollback();
            return $this->error("任务项目名称必须唯一");
        }
        $group = $groupDM->newEntity();
        $group->setUserId($this->getUser("id"));
        $group->setNames($post["names"]);
        $group->setAddTime(\DateTime::createFromFormat("Y-m-d", $post["startTime"]));
        if ($post["endTime"]) {
            $group->setEndTime(\DateTime::createFromFormat("Y-m-d", $post["endTime"]));
        }
        $group->setSid($this->sid);
        $group->setResponsible($post['leader'][0]);
        $group->setMembers($post['members']);
        $group->setMemo($post["memo"]);
        $group->setSort($post["sort"]);
        $group->setProgress($post["progress"]);
        $group->setStatus($post['status']);//项目状态
        $groupDM->add($group)->flush();
        DM()->getManager()->commit();
        return $this->success("新建成功");
    }

    /**
     * 修改项目
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function modify($id) {
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $staffDM = new \Admin\DModel\StaffDModel();

        /**@var  $group TaskGroup */
        $group = $groupDM->findOneBy(array("sid" => $this->sid, "id" => $id));
        if (!$group) {
            return $this->error("项目信息获取失败");
        }
        if (Q()->isGet()) {
            $this->assign("lists", $group = $groupDM->toArray($group));
            $members = $staffDM->workerList($this->sid, "members", $group['members'], 999);
            $leader = $staffDM->workerList($this->sid, "leader", $group['responsible'], 1);
            $status = $groupDM->getStatusMemo();
            $this->assign("status", $status);
            $this->assign([
                "members" => $members,
                "leader" => $leader,
            ]);

            return $this->display();
        }
        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();
        if (!$post["leader"]) {
            DM()->getManager()->rollback();
            return $this->error("负责人不能为空");
        }
        if (!$post["members"]) {
            DM()->getManager()->rollback();
            return $this->error("成员不能为空");
        }
        if (in_array($post["leader"], $post["members"])) {
            DM()->getManager()->rollback();
            return $this->error("負責人跟成员重复了");
        }
        if ($post["process"] > 100) {
            DM()->getManager()->rollback();
            return $this->error("阶段进程不能大于100%");
        }

        if (!$post["endTime"]) $post["endTime"] = date("Y-m-d H:i:s");

        $post['members'] = implode(",", $post["members"]);
        $group->setResponsible($post["leader"][0]);
        $group->setMembers($post["members"]);
        $group->setNames($post["names"]);
        $group->setMemo($post["memo"]);
        $group->setSort($post["sort"]);
        $group->setProgress($post["progress"]);
        $group->setEndTime(\DateTime::createFromFormat("Y-m-d H:i:s", $post["endTime"]));
        $group->setStatus($post["status"]);
        $groupDM->save($group)->flush($group);
        DM()->getManager()->commit();
        return $this->success("修改成功");
    }

    /**
     * 删除项目
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function delete($id) {
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $group = $groupDM->findOneBy(array("sid" => $this->sid, "id" => $id));
        if (!$group) {
            return $this->error("项目信息获取失败");
        }
        $groupDM->remove($group)->flush($group);
        return $this->success("删除成功", url('consoles_task_taskGroupLists'));
    }

    /**
     * 项目详情
     * @return \phpex\Foundation\Response
     */
    public function detail($id) {
        $active = Q()->get->get("active") ?: 0;
        $pid = Q()->get->get("pid") ?: 0;
        $sid = $this->getUser("sid");
        $this->assign("gid", $id);
        $this->assign("id", $id);
        $this->assign("fid", 0);

        if ($active == 1) return $this->taskLists($id);
        if ($active == 2) return $this->progress($id);
        if ($active == 3) return $this->member($id);
        if ($active == 4) return $this->groupDocs($id);
        if ($active == 5) return $this->groupFiles($id);
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $where = "g.sid=" . $this->sid . "AND g.id=" . $id;
        $lists = $groupDM->name("g")
            ->leftJoin("User", "u", "u.id=g.responsible")
            ->select("g,u.fullName as g_responsible")
            ->where($where)
            ->order("g.sort")
            ->order("g.id", "DESC")
            ->getOneArray(true);

        $nowTime = date("Y-m-d H:i:s");

        $totalSql = "SELECT  `status`, COUNT(*)  AS total  FROM  `jee_task` WHERE `sid` ={$this->sid} AND `pid`={$id}  GROUP BY `status`";
        DM()->execute($totalSql);
        $stat = DM()->getLastStatement();
        $total = $stat->fetchAll();
        $lists['total'] = 0;//总数
        $lists['finish'] = 0;//已完成
        $lists['overdue'] = 0;//逾期
        $lists['execution'] = 0;//执行中
        $lists['cancel'] = 0;//取消
        foreach ($total as $value) {
            $lists['total'] += $value['total'];//总数
            if ($value['status'] == 0 && $value['status'] == 1) {//执行中
                $lists['execution'] = $value['total'];
            }
            if ($value['status'] == 2) {
                $lists['cancel'] = $value['total'];//取消
            }
            if ($value['status'] == 3) {
                $lists['finish'] = $value['total'];//已完成
            }
        }
        //逾期任务
        $overdueSql = "SELECT COUNT(*)  AS total1  FROM  `jee_task` WHERE `sid` ={$this->sid} AND `status`=0 AND `pid`={$id}  AND `deadline`<'" . $nowTime . "'";

        DM()->execute($overdueSql);
        $stats = DM()->getLastStatement();
        $overdue = $stats->fetch();
        $lists['overdue'] = $overdue['total1'];

        //备忘
        $anyThingDM = new \Admin\DModel\AnythingDModel();
        $anyThing = $anyThingDM->name("at")
            ->select("at")
            ->where("at.sid=" . $this->sid . " AND at.tgId=" . $id . "AND at.status=0")
            ->order("at.id", "DESC")
            ->getArray();

        //讨论
        $taskGroupDiscussDM = new \Admin\DModel\TaskGroupDiscussDModel();
        $taskGroupCommentDM = new \Admin\DModel\TaskGroupCommentDModel();
        $taskGroupDiscuss = $taskGroupDiscussDM->name("gd")
            ->select("gd,u.fullName as gd_userName")
            ->leftJoin("User", "u", "u.id=gd.userId")
            ->where("gd.tgId=" . $id)
            ->order("gd.id", "DESC")
            ->getArray(true);
        $comments = $taskGroupCommentDM->getComments($id);

        //文件
        $filesDM = TaskGroupFilesDModel::getInstance();
        $files = $filesDM->name("f")
            ->where("f.sid={$sid} and f.pid={$pid} and f.gid={$id}")
            ->order("f.sort", "desc")
            ->order("f.id", "desc")
            ->setMax(6)
            ->getArray();

        //文档
        $docDM = TaskGroupDocDModel::getInstance();
        $docs = $docDM->name("d")
            ->where("d.sid={$sid} and d.gid={$id}")
            ->order("d.id", "desc")
            ->setMax(3)
            ->getArray();

        $this->assign("taskGroupDiscuss", $taskGroupDiscuss);
        $this->assign("comments", $comments);
        $this->assign("files", $files);
        $this->assign("docs", $docs);
        $this->assign("cdnBase", $this->cdnBase);
        $this->assign("anyThing", $anyThing);
        $this->assign("lists", $lists);


        $this->assign("active", $active);
        $this->assign("id", $id);

        return $this->display();
    }

    /**
     * 进度
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function progress($id) {
        $active = 2;
        $totalSql = "SELECT `names`,`deadline`,`add_time`, `accept_time` FROM  `jee_task` WHERE `sid` ={$this->sid} AND `pid`={$id} AND `status` IN (0,1)";
        DM()->execute($totalSql);
        $stat = DM()->getLastStatement();
        $total = $stat->fetchAll();

        $startTime = date("Y-01-01");//一年的开始时间
        $endTime = date('Y-01-01', strtotime('+1 year'));//结束时间

        $lists = array();//任务名称，开始时间，结束时间
        $names = array();//任务名称
        $dateDiff = array();//任务时长
        foreach ($total as $key => $value) {
            $names[$key] = $value['names'];
            $item = array(
                "names" => $value['names'],
                "startTime" => date("Y-m-d", strtotime($value['add_time'])),
                "latestTime" => date("Y-m-d", strtotime($value['deadline'])),
            );
            $lists[$key] = $item;
        }
        $this->assign("startTime", $startTime);
        $this->assign("endTime", $endTime);
        $this->assign("lists", $lists);
        $this->assign("dateDiff", $dateDiff);
        $this->assign("names", $names);
        $this->assign("active", $active);
        return $this->display("progress");
    }


    /**
     * 成员
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function member($id) {
        $active = 3;
        $groupDM = new \Admin\DModel\TaskGroupDModel();
        $userDM = new \Admin\DModel\UserDModel();

        $where = "g.sid=" . $this->sid . "AND g.id=" . $id;
        $lists = $groupDM->name("g")
            ->leftJoin("User", "u", "u.id=g.responsible")
            ->select("g")
            ->where($where)
            ->order("g.id", "DESC")
            ->getOneArray(true);

        $responsible = $lists['g_responsible'];
        if ($responsible) {
            $members = $responsible . "," . $lists['g_members'];
        } else {
            $members = $lists['g_members'] ?: 0;
        }

        $search = $this->search();
        $search->labelType("placeholder");
        $search->addKeyword("u.fullName", "姓名");
        $search->bindData(Q()->get->all());
        $where = "u.id in({$members})";

        $this->search()->build($where, $searchForm, $parameter); //构建$where和$searchForm
        $members = $userDM->name("u")
            ->select("u")
            ->where($where)
            ->setParameter($parameter)
            ->order("u.id", "DESC")
            ->setPage()
            ->getArray();
        $this->assign("lists", $members);
        $this->assign("id", $id);
        $this->assign("active", $active);
        return $this->display("member");
    }

    /**
     * 发起讨论
     * @return \phpex\Foundation\Response
     */
    public function createDiscuss($id) {
        $taskGroupDiscussDM = new \Admin\DModel\TaskGroupDiscussDModel();
        $taskGroupDM = new \Admin\DModel\TaskGroupDModel();
        if (Q()->isGet()) {
            $this->assign("id", $id);
            return $this->display();
        }
        $post = Q()->post->all();
        $taskGroup = $taskGroupDM->name("tg")->select("tg")->where("tg.id=" . $id . " AND tg.sid=" . $this->sid)->getOneArray();

        if (!$taskGroup) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $userId = $this->getUser("id");
        $url = url("consoles_detail", "con=taskGroup&id=" . $id);

        if ($post["types"] == "dynamic") {
            $dynamic = $taskGroupDiscussDM->newEntity();
            $dynamic->setContent($post["dynamicMemo"]);
            $dynamic->setTgId($id);
            $dynamic->setUserId($userId);
            $dynamic->setRuserId(0);
            $dynamic->setTypes(1);
            $dynamic->setAddTime(nowTime());
            $taskGroupDiscussDM->add($dynamic)->flush();

            return $this->ajaxReturn(array(
                "status" => "y",
                "info" => "操作成功",
                "url" => $url
            ));
        }
        return $this->ajaxReturn(array("status" => "n", "info" => "非法操作"));
    }

    /**
     * 更多讨论
     * @return \phpex\Foundation\Response
     */
    public function groupDiscuss($id) {
        $taskGroupDiscussDM = new \Admin\DModel\TaskGroupDiscussDModel();
        $taskGroupCommentDM = new \Admin\DModel\TaskGroupCommentDModel();
        $lists = $taskGroupDiscussDM->name("gd")
            ->select("gd,u.fullName as gd_userName")
            ->leftJoin("User", "u", "u.id=gd.userId")
            ->where("gd.tgId=" . $id)
            ->getArray(true);
        $comments = $taskGroupCommentDM->getComments($id);
        $this->assign("lists", $lists);
        $this->assign("comments", $comments);

        $this->assign("id", $id);
        return $this->display();

    }

    /**
     * 回復
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function discuss_reply($id) {
        $taskGroupDiscussDM = TaskGroupDiscussDModel::getInstance();
        $taskGroupCommentDM = TaskGroupCommentDModel::getInstance();

        $dynamic = $taskGroupDiscussDM->find($id);

        if (!$dynamic) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }
        $post = Q()->post->all();
        if (trim($post["content"]) == "") return $this->ajaxReturn(array("status" => "n", "info" => "请输入评论/回复内容"));

        $rdynamic = $taskGroupDiscussDM->newEntity();

        $rdynamic->setContent($post["content"]);
        $rdynamic->setTgId($dynamic->getTgId());
        $rdynamic->setUserId($this->getUser("id"));
        $rdynamic->setRuserId($dynamic->getUserId() == $this->getUser("id") ? 0 : $dynamic->getUserId());
        $rdynamic->setTypes(1);
        $rdynamic->setAddTime(nowTime());
        $taskGroupCommentDM->add($rdynamic)->flush();

        $url = url("consoles_detail", "con=taskGroup&id=" . $id);

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "回复成功",
            "url" => $url
        ));
    }


    /**
     * 评论
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function discuss_comment($id) {
        $taskGroupDiscussDM = TaskGroupDiscussDModel::getInstance();
        $taskGroupCommentDM = TaskGroupCommentDModel::getInstance();

        $discuss = $taskGroupDiscussDM->find($id);
        if (!$discuss) {
            return $this->ajaxReturn(array("status" => "n", "info" => "获取记录信息失败，请刷新页面重试"));
        }

        $post = Q()->post->all();
        if (trim($post["content"]) == "") return $this->ajaxReturn(array("status" => "n", "info" => "请输入评论/回复内容"));

        $comment = $taskGroupCommentDM->newEntity();
        $comment->setTgId($discuss->getTgId());
        $comment->setAid($id);
        $comment->setUserId($this->getUser("id"));
        $comment->setReplyId($post["relId"]);
        $comment->setContent($post["content"]);
        $taskGroupCommentDM->add($comment)->flush();
        $url = url("consoles_detail", "con=taskGroup&id=" . $id);

        return $this->ajaxReturn(array(
            "status" => "y",
            "info" => "评论/回复成功",
            "url" => $url
        ));
    }

    /**
     * 文档
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function groupDocs($id) {
        $this->assign("active", 4);
        $this->assign("id", $id);

        $docDM = TaskGroupDocDModel::getInstance();
        $sid = $this->getUser("sid");

        $lists = $docDM->name("d")
            ->where("d.sid={$sid} and d.gid={$id}")
            ->setPage($this->page())
            ->data_sort()
            ->getArray();

        $this->assign("lists", $lists);

        $this->assign("gid", $id);

        $this->assign("beforeUrl", Q()->server->get("REQUEST_URI"));

        return $this->display("groupDocs");
    }

    public function createDoc($id) {


        $sid = $this->getUser("sid");

        $this->assign("sid", $sid);

        if (Q()->isGet()) {
            return $this->display();
        }

        $userId = $this->getUser("id");

        $post = Q()->post->all();

        $docDM = TaskGroupDocDModel::getInstance();
        $docVersionsDM = TaskGroupDocVersionsDModel::getInstance();

        $doc = $docDM->newEntity();
        $docVersions = $docVersionsDM->newEntity();

        $doc->setSid($sid);
        $doc->setGid($id);
        $doc->setUserId($userId);
        $doc->setTitle($post["title"]);
        $doc->setContent($post["content"]);
        $doc->setAddTime(nowTime());
        $doc->setLastTime(nowTime());
        $doc->setLastUserId($userId);
        $doc->setStatus(1);

        $docDM->add($doc)->flush($doc);

        $docVersions->setSid($sid);
        $docVersions->setGid($id);
        $docVersions->setDocId($doc->getId());
        $docVersions->setUserId($userId);
        $docVersions->setTitle($post["title"]);
        $docVersions->setContent($post["content"]);
        $docVersions->setAddTime(nowTime());

        $docVersionsDM->add($docVersions)->flush($docVersions);

        return $this->success("添加成功");
    }


    public function modifyDoc($id) {
        $docDM = TaskGroupDocDModel::getInstance();
        $sid = $this->getUser("sid");
        /** @var TaskGroupDoc $doc */
        $doc = $docDM->find($id);

        if (!$doc || $doc->getSid() != $sid) {
            return $this->display("docError");
        }

        if (Q()->isGet()) {
            $this->assign("doc", $doc);
            return $this->display();
        }

        $userId = $this->getUser("id");

        $post = Q()->post->all();

        $doc->setTitle($post["title"]);
        $doc->setContent($post["content"]);
        $doc->setLastTime(nowTime());
        $doc->setLastUserId($userId);
        $doc->setStatus(1);
        $docDM->save($doc)->flush($doc);

        $docVersionsDM = TaskGroupDocVersionsDModel::getInstance();
        $docVersions = $docVersionsDM->newEntity();
        $docVersions->setSid($sid);
        $docVersions->setGid($doc->getGid());
        $docVersions->setDocId($doc->getId());
        $docVersions->setUserId($userId);
        $docVersions->setTitle($post["title"]);
        $docVersions->setContent($post["content"]);
        $docVersions->setAddTime(nowTime());

        $docVersionsDM->add($docVersions)->flush($docVersions);
        return $this->success("修改成功");
    }

    public function deleteDoc($id) {
        $docDM = TaskGroupDocDModel::getInstance();
        $url = Q()->get->get("beforeUrl");
        /** @var TaskGroupFiles $doc */

        $doc = $docDM->find($id);
        $sid = $this->getUser("sid");
        if (!$doc || $doc->getSid() != $sid) {
            return $this->error("文档信息获取错误");
        }

        $docDM->remove($doc)->flush();

        return $this->error("删除成功", $url);
    }

    /**
     * 文档
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function groupFiles($id) {
        $this->assign("active", 5);
        $this->assign("id", $id);
        $this->assign("cdnBase", $this->cdnBase);

        $this->assign("beforeUrl", Q()->server->get("REQUEST_URI"));

        $pid = Q()->get->get("pid") ?: 0;

        $filesDM = TaskGroupFilesDModel::getInstance();

        $sid = $this->getUser("sid");

        if ($pid) {
            /** @var TaskGroupFiles $folder */
            $folder = $filesDM->find($pid);
            if (!$folder) return $this->error("读取文件夹失败");
            if ($folder->getTypes() != 0 || $folder->getSid() != $sid) {
                return $this->error("读取文件夹错误");
            }
            $this->assign("isRoot", false);
            $this->assign("fid", $folder->getId());
            $this->assign("folder", $folder->getNames());
            $this->assign("folderPid", $folder->getPid() ?: 0);
            $this->assign("memo", $folder->getMemo());
            $this->assign("alterTime", $folder->getAlterTime() ? totime($folder->getAlterTime()) : "");
        } else {
            $this->assign("isRoot", true);
            $this->assign("folder", "..");
            $this->assign("alterTime", "");
            $this->assign("memo", "");
        }
        $this->assign("gid", $id);

        $lists = $filesDM->name("f")
            ->where("f.sid={$sid} and f.pid={$pid}")
            ->data_sort()
            ->setPage($this->page())
            ->order("f.sort", "desc")
            ->getArray();

        $this->assign("lists", $lists);

        $this->assign("pid", $pid);

        return $this->display("groupFiles");
    }

    public function createFolder($gid, $pid) {

        $pid = $pid ?: 0;

        $filesDM = TaskGroupFilesDModel::getInstance();

        $sid = $this->getUser("sid");

        $taskGroupDM = TaskGroupDModel::getInstance();
        /** @var TaskGroup $group */

        $group = $taskGroupDM->find($gid);

        if (!$group || $group->getSid() != $sid) return $this->error("项目信息获取失败");

        if ($pid) {
            /** @var TaskGroupFiles $folder */
            $folder = $filesDM->find($pid);
            if (!$folder) return $this->error("读取文件夹失败");
            if ($folder->getTypes() != 0 || $folder->getSid() != $sid) {
                return $this->error("读取文件夹错误");
            }
        }
        if (Q()->isGet()) {
            return $this->display();
        }

        $names = Q()->post->get("names");
        $memo = Q()->post->get("memo");

        if (!trim($names)) {
            return $this->error("请输入文件夹名称");
        }

        $old = $filesDM->findOneBy(array("sid" => $sid, "pid" => $pid, "names" => $names));

        if ($old) return $this->error("存在同名文件夹");

        $newFolder = $filesDM->newEntity();

        $newFolder->setSid($sid);
        $newFolder->setPid($pid);
        $newFolder->setGid($gid);
        $newFolder->setNames($names);
        $newFolder->setUserId($this->getUser("id"));
        $newFolder->setMemo($memo);
        $newFolder->setTypes(0);
        $newFolder->setAlterTime(nowTime());

        $filesDM->add($newFolder)->flush($newFolder);

        return $this->success("创建成功");

    }

    public function modifyFolder($gid, $fid, $pid) {

        $pid = $pid ?: 0;

        $filesDM = TaskGroupFilesDModel::getInstance();

        $sid = $this->getUser("sid");


        $taskGroupDM = TaskGroupDModel::getInstance();
        /** @var TaskGroup $group */

        $group = $taskGroupDM->find($gid);

        if (!$group || $group->getSid() != $sid) return $this->error("项目信息获取失败");

        $folder = $filesDM->find($fid);
        if (!$folder || $folder->getTypes() != 0 || $folder->getSid() != $sid) {
            return $this->error("读取文件夹错误");
        }
        if (Q()->isGet()) {

            $this->assign("folder", $folder);

            return $this->display();
        }

        $names = Q()->post->get("names");
        $memo = Q()->post->get("memo");

        if (!trim($names)) {
            return $this->error("请输入文件夹名称");
        }

        $old = $filesDM->findOneBy(array("sid" => $sid, "pid" => $pid, "names" => $names));

        if ($old && $old->getId() != $folder->getId()) return $this->error("存在同名文件夹");
        $folder->setSid($sid);
        $folder->setPid($pid);
        $folder->setGid($gid);
        $folder->setNames($names);
        $folder->setUserId($this->getUser("id"));
        $folder->setMemo($memo);
        $folder->setTypes(0);
        $folder->setAlterTime(nowTime());

        $filesDM->save($folder)->flush($folder);

        return $this->success("创建成功");

    }

    public function uploadFiles($gid, $fid) {
        $filesDM = TaskGroupFilesDModel::getInstance();

        $sid = $this->getUser("sid");

        $taskGroupDM = TaskGroupDModel::getInstance();
        /** @var TaskGroup $group */

        $group = $taskGroupDM->find($gid);

        if (!$group || $group->getSid() != $sid) return $this->error("项目信息获取失败");

        $this->assign("group", $group);

        if ($fid) {
            /** @var TaskGroupFiles $folder */
            $folder = $filesDM->find($fid);
            if (!$folder) return $this->error("读取文件夹失败");
            if ($folder->getTypes() != 0 || $folder->getSid() != $sid) {
                return $this->error("读取文件夹错误");
            }
            $this->assign("folder", $folder->getNames());
        } else {
            $this->assign("isRoot", true);
            $this->assign("folder", "..");
            $this->assign("alterTime", "");
        }

        if (Q()->isGet()) {
            return $this->display();
        }
        $post = Q()->post->all();

        $thumbs = $post["thumbs"];
        $thumbs_show = $post["thumbs_show"];
        if (!$thumbs) return $this->error("请至少上一个图片/附件");

        $memo = $post["memo"];


        foreach ($thumbs as $key => $thumb) {
            $show = $thumbs_show[$key];

            $files = $filesDM->newEntity();

            $files->setSid($sid);
            $files->setPid($fid);
            $files->setGid($gid);
            $files->setNames($show);
            $files->setUserId($this->getUser("id"));
            $files->setMemo($memo);
            $files->setTypes(1);
            $files->setFilePath($thumb);
            $files->setAlterTime(nowTime());
            $files->setSuffix(substr($thumb, strrpos($thumb, '.') + 1));

            $filesDM->save($files)->flush($files);

        }
        return $this->success("保存成功");
    }

    public function deleteFiles($id) {

        $filesDM = TaskGroupFilesDModel::getInstance();
        $url = Q()->get->get("beforeUrl");
        /** @var TaskGroupFiles $files */

        $files = $filesDM->find($id);
        $sid = $this->getUser("sid");
        if (!$files || $files->getSid() != $sid || $files->getTypes() != 1) {
            return $this->error("文件信息获取错误");
        }

        $filesDM->remove($files)->flush();

        return $this->error("删除成功", $url);

    }

}