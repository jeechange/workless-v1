<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class StudyDModel extends DModel {

    public $basePath = "https://cdn.itmakes.com/thumbs";

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
        if ($this->scalar) {
            $result["auditUsers"] = $this->auditUsers($result["s_auditUser"]);
            $result["recHtmlMemo"] = str_repeat("<i class='icon-star'></i>", $result["s_rec"]);
            $result["recHtmlMemoH5"] = str_repeat("<i class='icon icon-star'></i>", $result["s_rec"]);
            $result["applyMemo"] = $this->applyMemo($result["s_apply"]);

        } else {
            $result["auditUsers"] = $this->auditUsers($result["auditUser"]);
        }
    }

    protected function resolveObject($result = null) {


    }

    public function newEntity() {
        return new \Admin\Entity\Study();
    }

    public function auditUsers($auditUsers) {
        if (!$auditUsers) return "";

        $executorIds = explode(",", $auditUsers);
        $userDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $userDM->find($id);
            if ($user) $users[] = $user->getFullName();
        }

        return join(",", $users);
    }

    public function applyMemo($apply) {
        if (!$apply) return "";

        $applyIds = explode(",", $apply);

        $workTypeDM = WorkTypeDModel::getInstance();

        $applys = array();
        foreach ($applyIds as $id) {
            $workType = $workTypeDM->find($id);
            if ($workType) $applys[] = $workType->getNames();
        }

        return join(",", $applys);
    }

    public function getList($sid, $selectedIds = array(), $max = 20) {

        $lists = $this->name("s")->where("s.sid=$sid")->getArray();

        $submitString = <<<submit
                   <div class="study-selected">
                       <span class="study-selected-memo">已选择：<span class="study-selected-num">__num__</span>条</span>           
                       <span class="study-confirm" data-max="__total__">确定<span class="study-selected-num">__num__</span>/__total__</span>
                   </div>
submit;

        $body = <<<body
                <div class="searchbar">
                    <div class="search-input">
                      <label class="icon icon-search"></label>
                      <input type="search"  placeholder='输入关键字...'/>
                    </div>
                </div>
body;


        if (!$lists) return array(
            "confirm" => str_replace(array("__num__", "__total__"), array(0, $max), $submitString),
            "selected" => "",
            "body" => $body
        );

        $body .= <<<body
<div class="study-find-list list-block media-list"><ul>__find_lists__</ul></div>
body;

        $html = "";

        $num = 0;

        $selected = "";
        $basePath = $this->basePath;

        foreach ($lists as $item) {
            $isCheck = in_array($item["id"], $selectedIds) ? " checked" : "";

            if ($isCheck) {
                $num++;
                $selected .= <<<selected
                        <div class="study-item">
                            <span class="study-item-result"><img src="{$basePath}{$item["icon"]}" alt="">{$item["names"]}</span>
                            <span class="study-item-remove" onclick="removeStudyItem.call(this,'{$item["id"]}')">&times;</span>
                        </div>
selected;
            }
            $html = <<<html
                         <li>
                            <label class="label-checkbox item-content study-item-label{$isCheck}" data-value="{$item["id"]}" data-name="{$item["names"]}" data-icon="{$basePath}{$item["icon"]}">                               
                                <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title">
                                        <img src="{$basePath}{$item["icon"]}" alt="">
                                        <span class="study-keyword">{$item["names"]}</span>
                                        </div> 
                                         <div class="item-after">{$item["auditUsers"]}</div>                                      
                                    </div>
                                    <div class="item-subtitle">{$item["content"]}</div>
                                </div>
                            </label>
                        </li>
html;
        }

        return array(
            "confirm" => str_replace(array("__num__", "__total__"), array($num, $max), $submitString),
            "selected" => $selected,
            "body" => str_replace("__find_lists__", $html, $body)
        );

    }

    public function getStudies($ids) {
        if (!$ids) return "";

        if (!is_array($ids)) $ids = explode(",", $ids);

        $lists = $this->name("s")->where("s.id in (:ids)")->setParameter(array("ids" => $ids))->getArray();

        if (!$lists) return "";

        $html = "";

        foreach ($lists as $item) {

            $html .= <<<html
<div class="study-item"><span class="study-item-result"><img src="{$this->basePath}{$item["icon"]}">{$item["names"]}</span></div>
html;
        }


        return $html;
    }

}