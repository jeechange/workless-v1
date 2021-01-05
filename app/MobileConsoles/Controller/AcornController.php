<?php

namespace MobileConsoles\Controller;


use Admin\DModel\AcornAuditDetailDModel;
use Admin\DModel\AcornAuditDModel;
use Admin\DModel\AcornDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\RedDotDModel;
use Admin\DModel\ShareDModel;
use Admin\DModel\StaffDModel;
use Admin\DModel\StandardDModel;
use Admin\DModel\TaskDModel;
use Admin\DModel\TodoDModel;
use Admin\DModel\UserDModel;
use Admin\Entity\CompanyOpenapi;
use Jeechange\SDK\DingSDK;

class AcornController extends CommonController {

//    申请
    public function index() {
        $types = Q()->get->get("types") ?: 'submit';
        $tabs_two = Q()->get->get("tabs_two") ?: 'Standard';
        $this->assign("active", $types);
        $this->assign("tabs_two", $tabs_two);
        $lists = $this->indexLists($tabs_two);
        $this->assign('lists', $lists);
        return $this->display();
    }

    public function indexLists($tabs_two) {
        $standardDM = StandardDModel::getInstance();
        $where = "sc.namesEn='" . $tabs_two . "' AND s.sid=" . $this->sid . " AND s.status=1 AND s.types=0";
        $lists = $standardDM->name("s")
            ->leftJoin("StandardClassify", "sc", "sc.id=s.classify")
            ->select("s")
            ->where($where)
            ->order("s.acorn", "DESC")
            ->getArray(true);
        return $lists;
    }

    /**
     * 申请v1.0版
     * @return \phpex\Foundation\Response
     */
    public function applyAdd() {
        $types = Q()->get->get("types") ?: 'submit';
        $this->assign("active", $types);
        $id = Q()->get->get("s_id");

        $standardDM = StandardDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $acorndetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $standard = $standardDM->name('s')->where("s.id = '{$id}' AND s.status = 1 and s.sid in (0," . $this->sid . ") AND s.types=0")->getOneArray();

        if ($id) {
            if ($standard) {
                return $this->ajaxReturn(array("status" => "y", "data" => $standard));
            } else {
                return $this->ajaxReturn(array("status" => "n", "info" => "选择积分事项失败"));
            }
        }

        if (Q()->isGet()) {
            $types = Q()->get->get("types") ?: 'submit';
            $tabs_two = Q()->get->get("tabs_two") ?: 'Standard';
            $this->assign("active", $types);
            $this->assign("tabs_two", $tabs_two);
            $lists = $this->indexLists();
            $this->assign('lists', $lists);

            $toUser = $staffDM->workers($this->sid, array());
            $accept = $staffDM->workers($this->sid, array(), 1);

            $session = Q()->getSession();
            $session->set("__token__", rand_string(32));
            $session->save();
            $this->assign("__token__", $session->get("__token__"));
            $this->assign("accept", $accept);
            $this->assign("executors", $toUser);
            $this->assign('standard', $standard);
            return $this->display();
        }
        $session = Q()->getSession();
        $post = Q()->post->all();
        $token = $session->get("__token__");
        if ($post["__token__"] != $token) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请不要重复提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));
        }
        $session->remove("__token__");
        $session->save();
        if (utf8_encode($post['acorn']) == '不预设') {
            $post['acorn'] = 0;
        }
        if (!$post['s_id']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择积分事项"));
        if (!$post['acorn']) return $this->ajaxReturn(array("status" => "n", "info" => "请填写积分分数"));
        if (!$post['toUser']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择奖扣对象"));
        if (!$post['auditor']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择影审核人"));

        $toUsers = explode(",", $post["toUser"]);
        if (in_array($post['auditor'], $toUsers)) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "奖扣对象不能是影审核人"));
        }
        $newStandard = $standardDM->name('s')->where("s.id = '{$post['s_id']}' AND s.status = 1 and s.sid in (0," . $this->sid . ")")->getOneArray();
        $post['sid'] = $this->sid;
        $post['userId'] = $this->getUser('id');
        $post['fromUser'] = $this->getUser('id');
        $post['scId'] = $newStandard['classify'];
        $post['names'] = $newStandard['id'];
        $post['addTime'] = nowTime();
        $post['status'] = 0;
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDM->create($post, $acornAudit = $acornAuditDM->newEntity());
        $acornAuditDM->add($acornAudit)->flush();

        //添加到每个执行人的详情
        $acorndetailDM->adds($acornAudit, $this->getUser('id'));

        //热门
        $addHot = $standardDM->addHot($newStandard['id'], $this->sid);
        if (!$addHot) {
            return $this->error($standardDM->getError());
        }
        $shareDM = ShareDModel::getInstance();
        if ($post['acorn'] < 0) {
            $shareid = $shareDM->chooseTemplate("APPLY_INFLUENCE_MINUS", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        } else {
            $shareid = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        }
        if ($shareid > 0) {
            return $this->ajaxReturn(array("status" => "y", "info" => "已提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my"), "shareUrl" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "已提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));

    }

    public function applyLists() {
        $this->flushUser();
        $types = Q()->get->get("types") ?: 'my';
        $this->assign("active", $types);
        $acornAuditDM = AcornAuditDModel::getInstance();
        $where = "aa.userId = " . $this->getUser('id') . " and aa.sid = " . $this->sid;
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u2", "u2.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u2.fullName as fullNames")
            ->where($where)
            ->order("aa.id", "DESC")
            ->order("aa.status", "ASC")
            ->getArray(true);
        $taskDM = TaskDModel::getInstance();
        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
        }
        $this->assign("one", 0);
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function acornShare($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $acornA = $acornAuditDM->name('aa')->where("aa.id =" . $id)->getOneArray();
        $user = $userDM->name("u")->where("u.id = " . $acornA['userId'])->getOneArray();
        $taskDM = TaskDModel::getInstance();
        if ($acornA['userId'] == $acornA['toUser']) {
            $lists = '自己';
        } else {
            $lists = $taskDM->executorMemo($acornA['toUser']);
        }
        $standardDM = StandardDModel::getInstance();
        $sNames = $standardDM->name('s')->where("s.id = " . $acornA['names'])->getOneArray();
        $content = $user['fullName'] . '为[' . $lists . ']申请了积分，申请事项：[' . $sNames['names'] . ']，点击链接查看详情。';
        $url_host = 'https://' . $_SERVER['HTTP_HOST'] . '/acornShare?aa=' . $id;
        $this->assign("content", $content);
        $this->assign("url", $url_host);
        $this->assign("url_host", '申请积分：' . $content . $url_host);
        $this->initSDK();//初始化SDK
        return $this->display();
    }

    public function applyDetail($id) {
        $types = Q()->get->get("types") ?: 'my';
        $this->assign("active", $types);
        $acornAuditDM = AcornAuditDModel::getInstance();
        $where = "aa.id = " . $id . "and aa.sid =" . $this->sid;
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u2", "u2.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u2.fullName as fullNames")
            ->where($where)
            ->getArray(true);
        if (!$lists) {
            return $this->error("该记录不存在", url("mobileConsoles_acorn_applyLists", "types=my"));
        }
        $taskDM = TaskDModel::getInstance();
        $parseThumbs = array();

        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
            $lists[$k]['cPerson'] = $taskDM->executorMemo($v['aa_cPerson']);
            $thumbs = json_decode($v["aa_thumbs"], true);
            if ($thumbs) {
                foreach ($thumbs as $thumb) {
                    if (!$thumb[0]) continue;
                    $parseThumbs[] = array(
                        "name" => $thumb[1] ?: basename($thumb[0]),
                        "src" => $this->cdnBase . $thumb[0],
                        "val" => $thumb[0],
                        "type" => $this->getThumbType($thumb[0])
                    );
                }
            }
        }

        $this->assign("thumbs", $parseThumbs);
        $this->assign("cdnThumbBase", $this->cdnThumbBase);

        $shareDM = ShareDModel::getInstance();
        $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $lists[0]['aa_id'], $this->sid, $this->getUser('id'));
        $this->assign('shareId', $shareId);
        $this->assign('lists', $lists);
        return $this->display();
    }

    /**
     * 取消和删除
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function cancel($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDetailDM = AcornAuditDetailDModel::getInstance();
        $todoDM = TodoDModel::getInstance();
        $lists = $acornAuditDM->name("aa")->select("aa")->where("aa.id=" . $id . " and aa.sid =" . $this->sid)->getOneObject();

        DM()->getManager()->beginTransaction();

        if (!$lists) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该申请事项不存在", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));
        }
        //审核人的todo更新
        $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $lists->getSid(), "types" => 4, "userId" => $this->getUser("id"), "status" => 0));
        if ($item1) {
            $item1->setStatus(1);
            $todoDM->save($item1)->flush();
        }
        $acornAuditDetail = $acornAuditDetailDM->findOneBy(array("userId" => $lists->getAuditor(), "auditId" => $id));
        if ($acornAuditDetail) {
            $acornAuditDetailDM->remove($acornAuditDetail)->flush();
        }
        //抄送人的todo更新
        $cPerson = explode(",", $lists->getCPerson());
        foreach ($cPerson as $userId) {
            $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $lists->getSid(), "types" => 5, "userId" => $userId, "status" => 0));
            if ($item1) {
                $item1->setStatus(1);
                $todoDM->save($item1)->flush();
            }
            $acornAuditDetail = $acornAuditDetailDM->findOneBy(array("userId" => $userId, "auditId" => $id));
            if ($acornAuditDetail) {
                $acornAuditDetailDM->remove($acornAuditDetail)->flush();
            }
        }
        $acornAuditDM->remove($lists)->flush();
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));
    }


    //审核列表
    public function auditLists() {
        $types = Q()->get->get("types") ?: 'audit';
        $taskDM = TaskDModel::getInstance();
        $auditDetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $where = "ad.sid=" . $this->sid . "AND ad.userId=" . $this->getUser("id");
        $lists = $auditDetailDM->name("ad")
            ->select("ad,aa.id as aa_id,aa.toUser as aa_toUser")
            ->leftJoin("AcornAudit", "aa", "aa.id=ad.auditId")
            ->where($where)
            ->order("aa.status", "ASC")
            ->getArray(true);

        foreach ($lists as $k => $v) {
            $lists[$k]['toUser'] = $taskDM->executorMemo($v['aa_toUser']);
        }
        $this->assign("lists", $lists);
        $this->assign("active", $types);
        return $this->display();
    }

    /**
     * 审核详情
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function auditDetail($id) {
        RedDotDModel::getInstance()->NewAdd($this->getUser("id"), $this->getUser("sid"), $id, 'AcornAuditDetail');

        $types = Q()->get->get("types") ?: 'audit';
        $this->assign("active", $types);
        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDetailDM = AcornAuditDetailDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $acornAuditDetail = $acornAuditDetailDM->find($id);
        if (!$acornAuditDetail) {
            return $this->error("该记录不存在", url("mobileConsoles_acorn_applyLists", "types=my"));
        }
        $where = "aa.id = " . $acornAuditDetail->getAuditId() . " and aa.sid = " . $this->sid;
        $lists = $acornAuditDM->name("aa")
            ->leftJoin("StandardClassify", "sc", "sc.id=aa.scId")
            ->leftJoin("Standard", "s", "aa.names = s.id")
            ->leftJoin("User", "u", "u.id = aa.userId")
            ->leftJoin("User", "u2", "u2.id = aa.auditor")
            ->select("aa,sc.names,s.names as sNames,u.fullName,u2.fullName as fullNames")
            ->where($where)
            ->getOneArray(true);

        if (!$lists) {
            return $this->error("该记录不存在", url("mobileConsoles_acorn_applyLists", "types=my"));
        }
        $taskDM = TaskDModel::getInstance();

        $parseThumbs = array();

        $lists['toUser'] = $taskDM->executorMemo($lists['aa_toUser']);
        $thumbs = json_decode($lists["aa_thumbs"], true);
        if ($thumbs) {
            foreach ($thumbs as $thumb) {
                if (!$thumb[0]) continue;
                $parseThumbs[] = array(
                    "name" => $thumb[1] ?: basename($thumb[0]),
                    "src" => $this->cdnBase . $thumb[0],
                    "val" => $thumb[0],
                    "type" => $this->getThumbType($thumb[0])
                );
            }
        }
        $this->assign("thumbs", $parseThumbs);
        $this->assign("cdnThumbBase", $this->cdnThumbBase);

        $shareDM = ShareDModel::getInstance();
        $shareId = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $lists['aa_id'], $this->sid, $this->getUser('id'));
        $session = Q()->getSession();
        $session->set("__token__", rand_string(32));
        $session->save();
        $this->assign("__token__", $session->get("__token__"));
        $this->assign('lists', $lists);
        $this->assign('id', $id);
        $this->assign('shareId', $shareId);
        $this->assign('user', $userDM->toArray($this->getUser()));
        $this->assign('acornAuditDetail', $acornAuditDetailDM->toArray($acornAuditDetail));
        $this->assign('superior', $staffDM->workers($this->sid, array(), 1));
        return $this->display();
    }

    /**
     * 文件后缀
     * @param $name
     * @return string
     */
    private function getThumbType($name) {
        $ext = strtolower(substr(strrchr($name, "."), 1));

        if (in_array($ext, array("png", "jpg", "gif", "jpeg", "bmp"))) return "img";

        return "file";
    }

    /**
     * 审核通过
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function adopt($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $standardDM = StandardDModel::getInstance();
        $todoDM = new \Admin\DModel\TodoDModel();
        $acornAuditDetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $userDM = UserDModel::getInstance();
        $userId = $this->getUser("id") ?: 0;
        $sid = $this->getUser("sid") ?: 0;

        $acornAuditDetail = $acornAuditDetailDM->find($id);
        if (!$acornAuditDetail) {
            return $this->ajaxReturn(array("status" => "n", "info" => "记录不存在", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        $acornAuditEN = $acornAuditDM->findOneBy(array("id" => $acornAuditDetail->getAuditId()));
        if (!$acornAuditEN) {
            return $this->ajaxReturn(array("status" => "n", "info" => "记录不存在", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        $posts = Q()->post->all();
        DM()->getManager()->beginTransaction();
        $post['status'] = 1;
        $post['auditTime'] = nowTime();
        $post['types'] = $posts['acorn'];
        //抄送人处理
        if (in_array($this->getUser("id"), explode(",", $acornAuditEN->getCperson()))) {
            $item = $todoDM->findOneBy(array("relateId" => $id, "sid" => $this->sid, "types" => 5, "userId" => $userId, "status" => 0));
            if ($item) {
                $item->setStatus(1);
                $todoDM->save($item)->flush();
            }
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                $acornAuditDetail->setStatus(1);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            DM()->getManager()->commit();
            return $this->ajaxReturn(array("status" => "y", "info" => "已经阅读", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        //最高审核分
        $staffDM = StaffDModel::getInstance();
        $staffEN = $staffDM->name("s")->select("s,ss")
            ->leftJoin("StaffStation", "ss", "s . station = ss . id")
            ->where("s . userId = {$userId} and s . sid = {$sid}")
            ->limit(0, 1)
            ->order("ss . riseAcorn", "asc")
            ->getOneArray(true);

        $riseAcorn = $staffEN['ss_riseAcorn'];
        $limitAcorn = $staffEN['ss_limitAcorn'];

        //提交给上一级审核人
        if ($posts["superior"] > 0 && $posts['acorn'] > $riseAcorn && $limitAcorn == 1) {
            $acornAuditEN = $acornAuditDM->name("aa")->where("aa . id = " . $acornAuditDetail->getAuditId())->getOneObject();
            if (!$acornAuditEN) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "该审核记录有误"));
            }
            $users = $acornAuditEN->getAuditor() . "," . $acornAuditEN->getToUser();
            if ($acornAuditEN->getCPerson()) {
                $users .= "," . $acornAuditEN->getCPerson();
            }
            if (in_array($posts["superior"], explode(",", $users))) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "上级审核人不能是被申请人、抄送人、原审核人"));
            }
            //审核人的todo更新
            $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $sid, "types" => 4, "userId" => $userId, "status" => 0));
            if ($item1) {
                $item1->setStatus(1);
                $todoDM->save($item1)->flush();
            }
            if ($acornAuditEN) {
                $acornAuditEN->setSuperior($posts["superior"]);
                $acornAuditEN->setAcorn($posts['acorn']);
                $acornAuditEN->setTags($acornAuditEN->getTags() . ',' . $userDM->getUserName($posts["superior"]));
                $acornAuditDM->save($acornAuditEN)->flush();
            }
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                $acornAuditDetail->setStatus(1);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            //添加到每个执行人的详情
            $acorndetailDM = new \Admin\DModel\AcornAuditDetailDModel();
            $acorndetailDM->adds($acornAuditEN, $this->getUser('id'));

            DM()->getManager()->commit();
            return $this->ajaxReturn(array("status" => "y", "info" => "提交上一级审核成功", "url" => url("mobileConsoles_acorn_auditLists")));
        }

        if ($posts['acorn'] > $riseAcorn && $limitAcorn == 1) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "验收失败(本次任务已经超出您的最高审核分：" . $riseAcorn . "),请提交上级", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        //审核人的todo更新
        $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $this->sid, "types" => 4, "userId" => $this->getUser("id"), "status" => 0));
        if ($item1) {
            $item1->setStatus(1);
            $todoDM->save($item1)->flush();
        }
        if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
            if ($posts['acorn'] != $acornAuditDetail->getAcorn()) {
                $acornAuditDetail->setAcorn($posts['acorn']);
            }
            $acornAuditDetail->setStatus(1);
            $acornAuditDetail->setAuditTime(nowTime());
            $acornAuditDetailDM->save($acornAuditDetail)->flush();
        }

        $lists = $acornAuditDM->name("aa")->where("aa.id =" . $acornAuditDetail->getAuditId())->getOneArray();
        if ($lists['status'] == 1 || $lists['status'] == 2) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "该事项已审核！", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        if (!$posts['acorn']) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => "请填写积分分数"));
        }
        $acornAuditDM->create($post, $acornAuditEN);
        if (!$acornAuditDM->check($post, $acornAuditEN)) {
            DM()->getManager()->rollback();
            return $this->ajaxReturn(array("status" => "n", "info" => $acornAuditDM->getError(), "url" => url("mobileConsoles_acorn_auditLists")));
        }
        $acornAuditDM->save($acornAuditEN)->flush($acornAuditEN);

        $acornDM = AcornDModel::getInstance();
        $s_id = $standardDM->find($acornAuditEN->getNames());
        $toUser = $acornDM->toUserId($acornAuditEN->getToUser());
        foreach ($toUser as $k => $v) {
            if ($acornAuditEN->getSuperior()) {
                $auditor = $acornAuditEN->getSuperior();
            } else {
                $auditor = $acornAuditEN->getAuditor();
            }
            $result = $acornDM->addAcorn($this->sid, $v['id'], $acornAuditEN->getFromUser(), $auditor, $acornAuditEN->getScId(), $acornAuditEN->getNames(), $posts['acorn'], "完成[" . $s_id->getNames() . "]标准", $acornAuditEN->getMemo(), $acornAuditEN->getAddTime());
            if (!$result) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => $acornDM->getError()));
            }
        }
        //下一個要執行的任務
        $where = "ad.userId = '{$this->getUser('id')}' and ad.sid = " . $this->sid . " AND ad.status=0";
        $lists = $acornAuditDetailDM->name("ad")->select("ad")
            ->where($where)
            ->setMax(1)
            ->getOneArray();
        if ($lists) {
            $url = url("mobileConsoles_acorn_auditDetail", array("id" => $lists['id']));
        } else {
            $url = url("mobileConsoles_acorn_auditLists", "types=audit");
        }
        DM()->getManager()->commit();
        return $this->ajaxReturn(array("status" => "y", "info" => "操作成功", "url" => $url));
    }

    /**
     * 审核不通过
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function notAdopt($id) {
        $acornAuditDM = AcornAuditDModel::getInstance();
        $todoDM = new \Admin\DModel\TodoDModel();
        $acornAuditDetailDM = new \Admin\DModel\AcornAuditDetailDModel();

        $acornAuditDetail = $acornAuditDetailDM->find($id);
        if (!$acornAuditDetail) {
            return $this->ajaxReturn(array("status" => "n", "info" => "记录不存在", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        $acornAuditEN = $acornAuditDM->findOneBy(array("id" => $acornAuditDetail->getAuditId()));
        if (!$acornAuditEN) {
            return $this->ajaxReturn(array("status" => "n", "info" => "记录不存在", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        $post = Q()->post->all();
        DM()->getManager()->beginTransaction();

        $post['status'] = 2;
        $post['auditTime'] = nowTime();
        //抄送人处理
        if (in_array($this->getUser("id"), explode(",", $acornAuditEN->getCperson()))) {
            $item = $todoDM->findOneBy(array("relateId" => $id, "sid" => $this->sid, "types" => 5, "userId" => $this->getUser("id"), "status" => 0));
            if ($item) {
                $item->setStatus(1);
                $todoDM->save($item)->flush();
            }
            if ($acornAuditDetail && $acornAuditDetail->getStatus() == 0) {
                $acornAuditDetail->setStatus(1);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            DM()->getManager()->commit();
            return $this->ajaxReturn(array("status" => "y", "info" => "已经阅读", "url" => url("mobileConsoles_acorn_auditLists")));
        }
        if (($this->getUser("id") == $acornAuditEN->getAuditor()) || ($this->getUser("id") == $acornAuditEN->getSuperior())) {
            //审核人的todo更新
            $item1 = $todoDM->findOneBy(array("relateId" => $id, "sid" => $this->sid, "types" => 4, "userId" => $this->getUser("id"), "status" => 0));
            if ($item1) {
                $item1->setStatus(1);
                $todoDM->save($item1)->flush();
            }
            if ($acornAuditDetail) {
                $acornAuditDetail->setStatus($post['status']);
                $acornAuditDetail->setAuditTime(nowTime());
                $acornAuditDetailDM->save($acornAuditDetail)->flush();
            }
            $lists = $acornAuditDM->name("aa")->where("aa.id =" . $acornAuditDetail->getAuditId())->getOneArray();
            if ($lists['status'] == 1 || $lists['status'] == 2) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => "该事项已审核！", "url" => url("mobileConsoles_acorn_auditLists")));
            }
            $acornAuditDM->create($post, $acornAuditEN);
            if (!$acornAuditDM->check($post, $acornAuditEN)) {
                DM()->getManager()->rollback();
                return $this->ajaxReturn(array("status" => "n", "info" => $acornAuditDM->getError()));
            }
            $acornAuditDM->save($acornAuditEN)->flush($acornAuditEN);

            //下一個要執行的任務
            $acornAuditDM = AcornAuditDModel::getInstance();
            $where = "aa.auditor = '{$this->getUser('id')}' and aa.sid = " . $this->sid . " AND aa.status=0";
            $lists = $acornAuditDM->name("aa")->select("aa")
                ->where($where)
                ->setMax(1)
                ->getOneArray();
            if ($lists) {
                $url = url("mobileConsoles_acorn_auditDetail", array("id" => $lists['id']));
            } else {
                $url = url("mobileConsoles_acorn_auditLists", "types=audit");
            }
            DM()->getManager()->commit();
            return $this->ajaxReturn(array("status" => "y", "info" => "申请项审核不通过", "url" => $url));
        }
    }

    public function acornShareDing() {
        $post = Q()->post->all();

        $dingSDK = new DingSDK();
        $apiDM = CompanyOpenapiDModel::getInstance();
        /** @var CompanyOpenapi $api */
        $api = $apiDM->findOneBy(array("sid" => $this->sid, "namesEn" => "dingtalk"));
        if ($api) $dingSDK->initConfig($api);

        $dingInfo = array(
            "cid" => $post["cid"],
            "msgtype" => "link",
            "sender" => $dingSDK->getUserId($post["code"]),
            "link" => array(
                "title" => $post["title"],
                "text" => $post["content"],
                "picUrl" => 'https://m.console.xiangshuyun.com/public/mobileConsoles/default/img/share-logo.png',
                "messageUrl" => $post["url"],
            )
        );
        $url = 'https://oapi.dingtalk.com/message/send_to_conversation?access_token=' . $dingSDK->getAccessToken();

        $post = str_replace(array("\r\n", "\r", "\n"), "", json_encode($dingInfo));
        $headers = array(
            "Content-type:application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );

        $res = curl_post($url, $post, $headers);

        return $this->success($res);

    }

    /**
     * 申请v2.0
     * @return \phpex\Foundation\Response
     */
    public function apply() {
        $types = Q()->get->get("types") ?: 'submit';
        $this->assign("active", $types);
        $id = Q()->get->get("s_id");

        $standardDM = StandardDModel::getInstance();
        $staffDM = StaffDModel::getInstance();
        $standard = $standardDM->name('s')->where("s.id = '{$id}' AND s.status = 1 and s.sid in (0," . $this->sid . ")")->getOneArray();

        if ($id) {
            if ($standard) {
                return $this->ajaxReturn(array("status" => "y", "data" => $standard));
            } else {
                return $this->ajaxReturn(array("status" => "n", "info" => "选择积分事项失败"));
            }
        }

        if (Q()->isGet()) {
            $types = Q()->get->get("types") ?: 'submit';
            $tabs_two = Q()->get->get("tabs_two") ?: 'Standard';
            $this->assign("active", $types);
            $this->assign("tabs_two", $tabs_two);
            $lists = $this->indexLists($tabs_two);
            $this->assign('lists', $lists);

            $toUser = $staffDM->workers($this->sid, array());
            $accept = $staffDM->workers($this->sid, array(), 1);
            $cPerson = $staffDM->workers($this->sid, array());

            $session = Q()->getSession();
            $session->set("__token__", rand_string(32));
            $session->save();
            $this->assign("__token__", $session->get("__token__"));
            $this->assign("accept", $accept);
            $this->assign("executors", $toUser);
            $this->assign("cPerson", $cPerson);
            $this->assign('standard', $standard);
            return $this->display();
        }
        $session = Q()->getSession();
        $post = Q()->post->all();
        $token = $session->get("__token__");
        if ($post["__token__"] != $token) {
            return $this->ajaxReturn(array("status" => "n", "info" => "请不要重复提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));
        }
        $session->remove("__token__");
        $session->save();
        if (utf8_encode($post['acorn']) == '不预设') {
            $post['acorn'] = 0;
        }

        if (!$post['s_id']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择积分事项"));
        if (!$post['acorn']) return $this->ajaxReturn(array("status" => "n", "info" => "请填写积分分数"));
        if (!$post['toUser']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择奖扣对象"));
        if (!$post['auditor']) return $this->ajaxReturn(array("status" => "n", "info" => "请选择审核人"));

        if (in_array($post['auditor'], explode(",", $post['toUser']))) {
            return $this->ajaxReturn(array("status" => "n", "info" => "奖扣对象不能是审核人"));
        }
        if ($post['cPerson']) {
            if (in_array($post['auditor'], explode(",", $post['cPerson']))) {
                return $this->ajaxReturn(array("status" => "n", "info" => "抄送人不能是审核人"));
            }
        }
        $newStandard = $standardDM->name('s')->where("s.id = '{$post['s_id']}' AND s.status = 1 and s.sid in (0," . $this->sid . ")")->getOneArray();
        $thumbs = array();
        foreach ($post["thumbs"] as $index => $thumb) {
            $thumbs[] = array($thumb, $post["thumbs_show"][$index]);
        }
        $post["thumbs"] = json_encode($thumbs);
        $array = explode(",", $post['toUser']);
        $post['sid'] = $this->sid;
        $post['userId'] = $this->getUser('id');
        $post['fromUser'] = $this->getUser('id');
        $post['scId'] = $newStandard['classify'];
        $post['names'] = $newStandard['id'];
        $post['addTime'] = nowTime();
        $post['status'] = 0;
        //tags字段处理
        if (!in_array($post["auditor"], $array)) {
            $array[] = $post["auditor"];
        }
        foreach ($post["cPerson"] as $id) {
            if (!in_array($id, $array)) {
                $array[] = $post["cPerson"];
            }
        }
        $userDM = new \Admin\DModel\UserDModel();
        foreach ($array as $v) {
            $userEN = $userDM->find($v);
            if ($userEN) $post['tags'][] = $userEN->getFullname();
        }
        if (!in_array($this->getUser('fullName'), $post['tags'])) {
            $post['tags'][] = $this->getUser('fullName');
        }
        $post['tags'] = array_unique($post['tags']);
        $post['tags'] = join(",", $post['tags']);
        $post['tags'] .= ',' . $newStandard['names'];

        $acornAuditDM = AcornAuditDModel::getInstance();
        $acornAuditDM->create($post, $acornAudit = $acornAuditDM->newEntity());
        $acornAuditDM->add($acornAudit)->flush();
        $addHot = $standardDM->addHot($newStandard['id'], $this->sid);
        if (!$addHot) {
            return $this->error($standardDM->getError());
        }
        //添加到每个执行人的详情
        $acorndetailDM = new \Admin\DModel\AcornAuditDetailDModel();
        $acorndetailDM->adds($acornAudit, $this->getUser('id'));

        $shareDM = ShareDModel::getInstance();
        if ($post['acorn'] < 0) {
            $shareid = $shareDM->chooseTemplate("APPLY_INFLUENCE_MINUS", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        } else {
            $shareid = $shareDM->chooseTemplate("APPLY_INFLUENCE", $acornAuditDM, $acornAudit->getId(), $this->sid, $this->getUser('id'));
        }
        if ($shareid > 0) {
            $share = $shareDM->getLastShare();
            $shareUrl = explode(",", $share->getShareUrl());
            $message = sprintf("%s%s%s,点击链接查看详情%s?share=%d",
                $share->getContent1(),
                $share->getContent2(),
                $share->getContent3() ? sprintf("，附言：%s", $share->getContent3()) : "",
                $shareUrl[1] ?: $shareUrl[0],
                $share->getId()
            );
            $users = explode(",", $acornAudit->getToUser());

            $users[] = $acornAudit->getUserId();
            $users[] = $acornAudit->getAuditor();
            CompanyOpenapiDModel::sendAcornMessage($acornAudit->getSid(), $message, $users);

            return $this->ajaxReturn(array("status" => "y", "info" => "已提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my"), "shareUrl" => url("mobileConsoles_index_sharePage", array("share" => $shareid))));
        }
        return $this->ajaxReturn(array("status" => "y", "info" => "已提交申请", "url" => url("mobileConsoles_acorn_applyLists", "types=my")));

    }

}