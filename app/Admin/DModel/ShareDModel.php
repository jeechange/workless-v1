<?php

namespace Admin\DModel;

use Admin\Entity\Share;
use phpex\DModel\DModel;

class ShareDModel extends DModel {


    const APPLY_INFLUENCE = 'APPLY_INFLUENCE';
    const APPLY_INFLUENCE_MINUS = 'APPLY_INFLUENCE_MINUS';
    const APPLY_STANDARD = 'APPLY_STANDARD';
    const GET_GIFT = 'GET_GIFT';
    const GET_LUCKY = 'GET_LUCKY';
    const EXCHANGE_SNACK = 'EXCHANGE_SNACK';
    const RELEASE_TASK = 'RELEASE_TASK';

    private $templateVar = array();

    private $lastShare;

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
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Share();
    }

    private $template = array(
        'APPLY_INFLUENCE' => array('{#PROPOSER#}为[{#RESPONDENT#}]申请积分！', '申请事项：[{#STANDARD#}]', '{#MEMO#}', "我也要申请积分"),
        'APPLY_INFLUENCE_MINUS' => array('{#PROPOSER#}为[{#RESPONDENT#}]申请扣积分！', '申请事项：[{#STANDARD#}]', '{#MEMO#}', "我也要申请积分"),
        'APPLY_STANDARD' => array('我分享了一个标准！', '[{#STANDARD#}]', '{#MEMO#}', "我也要申请标准"),
        'GET_GIFT' => array('我分享了一个福利！', '[{#GIFT#}]', '{#MEMO#}', "我也要领取福利"),
        'GET_LUCKY' => array('恭喜[{#WINNER#}]获得了奖品！', '奖品：[{#LUCKY#}]', '抽奖活动：{#TITLE#}', "我也要抽奖"),
        'EXCHANGE_SNACK' => array('我在小吃柜兑换了一个小吃！', '小吃：[{#SNACK#}]', '', "我也要兑换小吃"),
        'RELEASE_TASK' => array('{#FROMUSER#}分享了一个任务{#TOUSER#}：', '{#CODENO#}[{#TASK#}]', '{#CONTENT#}', "任务详情"),
        'RELEASE_TASK_REWARD' => array('{#FROMUSER#}分享了一个悬赏任务，欢迎大家争先恐后领取任务{#TOUSER#}：', '{#CODENO#}[{#TASK#}]', '{#CONTENT#}', "任务详情"),
    );

    private $templateName = array(
        'APPLY_INFLUENCE' => '申请积分',
        'APPLY_INFLUENCE_MINUS' => '申请积分',
        'APPLY_STANDARD' => '申请标准',
        'GET_GIFT' => '领取福利',
        'GET_LUCKY' => '抽奖',
        'EXCHANGE_SNACK' => '兑换小吃',
        'RELEASE_TASK' => '任务详情',
        'RELEASE_TASK_REWARD' => '任务详情',
    );

    public function templateReplace($match) {
        return isset($this->templateVar[$match[1]]) ? $this->templateVar[$match[1]] : "";
    }

    /**
     * @return Share;
     */

    public function getLastShare() {
        return $this->lastShare;

    }

    public function chooseTemplate($template, DModel $table, $eventId, $sid, $userId) {
        $userDM = UserDModel::getInstance();
        $standardDM = StandardDModel::getInstance();
        $shareDM = ShareDModel::getInstance();
        $comDM = CompanyDModel::getInstance();
        $comEN = $comDM->find($sid);
        $codeNo = $comEN->getCodeNo();


        $data = $table->name("a")->where("a.id={$eventId}")->getOneArray();

        if (!$data) {
            return $this->error("未找到分享数据");
        }
        if ($template == "APPLY_INFLUENCE" || $template == "APPLY_INFLUENCE_MINUS") {

            $proposerEN = $userDM->name("u")->where("u.id={$data['fromUser']}")->getOneArray();//申请人
            $proposer = $proposerEN['fullName'];
            $touserArr = explode(",", $data['toUser']);
            $touser = "";
            foreach ($touserArr as $k => $v) {
                $a = $userDM->name("u")->where("u.id={$v}")->getOneArray();
                if ($a['fullName'] == $proposer) {
                    $touser .= "自己,";
                } else {
                    $touser .= $a['fullName'] . ",";
                }
            }
            $touser = rtrim($touser, ",");

            $standardEN = $standardDM->name("s")->where("s.id={$data['names']}")->getOneArray();
            $standard = $standardEN['names'];
            $memo = $data['memo'];
            $vars = array(
                "PROPOSER" => $proposer,
                "RESPONDENT" => $touser,
                "STANDARD" => $standard,
                "MEMO" => $memo,
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);

            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            $shareData['toUser'] = $data['toUser'];
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_acorn_submitApply', array('company' => $codeNo, 'user' => 'root')) . "#41," . url('~mobileConsoles_index_index', array('company' => $codeNo, 'user' => 'root'));;
            $shareData['shareUrl'] = url("~consoles_share_sharePage") . "," . url("~mobileConsoles_share_sharePage");
            $shareData['operate'] = '我也要申请积分';
            $shareData['template'] = $template;
            $shareData['templateName'] = $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();

            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        if ($template == "APPLY_STANDARD") {
            $vars = array(
                "STANDARD" => $data['names'],
                "MEMO" => $data['memo']
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);

            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            //有手机版的分享链接
//            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_lists', array('con' => 'Standard', 'types' => 0, 'active' => 'All', 'company' => $codeNo, 'user' => 'root')) . "#210," . url("~mobileConsoles_standard_lists", array('company' => $codeNo, 'user' => 'root'));
//            $shareData['shareUrl'] = url("~consoles_share_sharePage") . "," . url("~mobileConsoles_share_sharePage");
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_lists', array('con' => 'Standard', 'types' => 0, 'active' => 'All', 'company' => $codeNo, 'user' => 'root')) . "#210";
            $shareData['shareUrl'] = url("~consoles_share_sharePage");
            $shareData['operate'] = '我也要申请标准';
            $shareData['template'] = $template;
            $shareData['templateName'] = $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();
            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        if ($template == "GET_GIFT") {
            $vars = array(
                "GIFT" => $data['names'],
                "MEMO" => $data['memo']
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);


            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_welfare_material', array('company' => $codeNo, 'user' => 'root')) . "#41," . url("~mobileConsoles_welfare", array('company' => $codeNo, 'user' => 'root'));
            $shareData['shareUrl'] = url("~consoles_share_sharePage") . "," . url("~mobileConsoles_share_sharePage");
            $shareData['operate'] = '我也要领取福利';
            $shareData['template'] = $template;
            $shareData['templateName'] = $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();

            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        if ($template == "GET_LUCKY") {
            $winner = $userDM->name("u")->where("u.id={$data['userId']}")->getOneArray();

            if (!$winner) {
                return $this->error("未找到分享数据");
            }
            $vars = array(
                "WINNER" => $winner['fullName'],
                "LUCKY" => $data['lucky'],
                "TITLE" => $data['title']
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);

            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_lists', array('con' => 'Welfare', 'company' => $codeNo, 'user' => 'root')) . "#42," . url("~mobileConsoles_welfare_lucky", array('company' => $codeNo, 'user' => 'root'));;
            $shareData['shareUrl'] = url("~consoles_share_sharePage") . "," . url("~mobileConsoles_share_sharePage");
            $shareData['operate'] = '我也要抽奖';
            $shareData['template'] = $template;
            $shareData['templateName'] = $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();

            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        if ($template == "EXCHANGE_SNACK") {
            $snackDM = WelfareSnackDModel::getInstance();
            $snack = $snackDM->name("s")->where("s.id={$data['snackId']}")->getOneArray();

            if (!$snack) {
                return $this->error("未找到分享数据");
            }
            $vars = array(
                "SNACK" => $snack['names']
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);

            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url('consoles_lists', array('con' => 'Snack', 'company' => $codeNo, 'user' => 'root')) . "#44," . url("~mobileConsoles_welfare_snack", array('company' => $codeNo, 'user' => 'root'));
            $shareData['shareUrl'] = url("~consoles_share_sharePage") . "," . url("~mobileConsoles_share_sharePage");
            $shareData['operate'] = '我也要兑换小吃';
            $shareData['template'] = $template;
            $shareData['templateName'] = $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();

            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        if ($template == "RELEASE_TASK" || $template == "RELEASE_TASK_REWARD") {
            $fromUser = $userDM->name('u')->where("u.id = {$data['issueId']}")->getOneArray();
            if ($fromUser) {
                $fromUser = $fromUser['fullName'];
            } else {
                $fromUser = "somebody";
            }

            $executors = explode(",", $data['executors']);
            $toUser = "";
            foreach ($executors as $k => $v) {
                $v = $v ?: 0;
                $findUser = $userDM->name('u')->where("u.id = {$v}")->getOneArray();
                if ($findUser) {
                    $toUser .= "@" . $findUser['fullName'] . "、";
                }
            }
            $toUser = rtrim($toUser, "、");

            $vars = array(
                "FROMUSER" => $fromUser,
                "TOUSER" => $toUser,
                "CODENO" => "#" . $data['codeNo'],
                "TASK" => $data['names'],
                "CONTENT" => $data['content']
            );
            $this->templateVar = $vars;// array_merge($this->templateVar, $vars);
            $content1 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][0]);
            $content2 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][1]);
            $content3 = preg_replace_callback('/\{#([a-z_0-9]*)#\}/i', array($this, "templateReplace"), $this->template[$template][2]);

            $shareData = array();
            $shareData['createTime'] = nowTime();
            $shareData['status'] = 1;
            $shareData['eventId'] = $eventId;
            $shareData['sid'] = $sid;
            $shareData['userId'] = $userId;
            $shareData['content1'] = $content1;
            $shareData['content2'] = $content2;
            $shareData['content3'] = $content3;
            $shareData['gobackUrl'] = url("~consoles_index_index") . "#" . url("consoles_task_details", array('id' => $eventId, 'company' => $codeNo, 'user' => 'root')) . "#313," . url('~mobileConsoles_task_details', array('id' => $eventId, 'company' => $codeNo, 'user' => 'root'));
            $shareData['shareUrl'] = url('~consoles_share_sharePageTask') . "," . url('~mobileConsoles_share_sharePageTask');
            $shareData['operate'] = '任务大厅';
            $shareData['template'] = $template;
            $shareData['templateName'] = "#" . $data['codeNo'] . " " . $this->templateName[$template];

            $shareDM->create($shareData, $shareEN = $shareDM->newEntity());
            $shareDM->add($shareEN)->flush();

            $this->lastShare = $shareEN;

            return $shareEN->getId();
        }

        return 0;
    }


}