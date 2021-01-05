<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class StandardClassifyDModel extends DModel {
    private $typesMemo = array(
        "1" => "社会",
        "2" => "稀缺",
        "3" => "好感",
        "4" => "任务",
        "5" => "内容",
        "6" => "学习",
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
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\StandardClassify();
    }

    /**获取类型
     * @param null $key
     * @return array|mixed
     */
    public function getTypes($key = null) {
        return null !== $key ? $this->typesMemo[$key] : $this->typesMemo;
    }

    public function addClass($names, $namesEn, $sid, $pid = 0) {
        $StandardClassify = $this->newEntity();
        $StandardClassify->setSid($sid);
        $StandardClassify->setNames($names);
        $StandardClassify->setNamesEn($namesEn);
        $StandardClassify->setPid($pid);
        $this->add($StandardClassify)->flush();
        return $this->toArray($StandardClassify);
    }

    public function getOptions($sid, $selectId, $pid = 0, $depth = 0) {
        $where = "d.pid = $pid and d.sid=$sid";
        $section = $this->name('d')->where($where)->getArray(false, false);
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= "<option value=\"{$sec["id"]}\"{$selected}>{$depthstr}{$sec["names"]}</option>";
            $options .= $this->getOptions($sid, $selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }

    private function depth($i, $count, $depth) {
        if ($depth == 0) {
            return "";
        }
        $nbsp = '&nbsp;';
        $return = "";
        for ($j = 0; $j < $depth; $j++) {
            $return .= $nbsp;
        }
        return ($i + 1) < $count ? $return . "├" : $return . "└";
    }


    public function workerList($sid, $name, $selectedIds = array(), $max = 20, $readonly = false) {
        $depDM = StandardClassifyDModel::getInstance();

        if (is_scalar($selectedIds)) $selectedIds = explode(",", $selectedIds);

        $selectedIds = array_filter($selectedIds);

        $deps = $depDM->name("d")->where("d.sid=$sid and d.pid=0")->getArray();

        $html = <<<work
        <div class="worker-box__readonly__">
            __selected__
            <span class="worker-added">+</span>
            <div class="worker-added-box">
               <input type="text" class="worker-finder">
               <div class="worker-added-find-list">__find_lists__</div>
               <div class="worker-added-list">__lists__</div>
               <div class="worker-selected">
                   <span class="worker-selected-memo">已选择：<span class="worker-selected-num">__num__</span></span>           
                   <span class="worker-confirm">确定<span class="worker-selected-num">__num__</span>/__total__</span>
               </div>
            </div>
        </div>
work;
        $replace = array(
            "__readonly__" => $readonly ? " worker-box-readonly" : "",
            "__selected__" => "",
            "__find_lists__" => "",
            "__lists__" => "",
            "__num__" => 0,
            "__total__" => $max,
        );
        if (!$deps) return str_replace(array_keys($replace), $replace, $html);

        $result = $this->getDepWorker($sid, 0, $name, $selectedIds, $replace);
        $replace["__lists__"] = $result["html"];
        return str_replace(array_keys($replace), $replace, $html);


    }

    private function getDepWorker($sid, $depId, $name, $selectedIds, &$replace, $isRoot = true) {
        $standardDM = StandardClassifyDModel::getInstance();

        $lists = $this->name("s")->where("s.pid=$depId and s.sid=$sid")->getArray();
        $subs = $standardDM->name("d")->where("d.pid=$depId and d.sid=$sid")->getArray();
        $listsHtml = $isRoot ? '<ul class="worker-added-list-root">' : '<ul class="worker-fold-off">';
        if (!$lists && !$subs) {
            return array("num" => 0, "depNum" => 0, "html" => "");
        }
        $subHtml = "";
        $num = 0;
        $depNum = 0;
        if ($lists) {
            foreach ($lists as $item) {
                $num++;
                $isCheck = in_array($item["id"], $selectedIds) ? "checked" : "";
                $replace["__find_lists__"] .= <<<find_lists
                   <dl>
                    <dt><input type="checkbox" name="{$name}[]" class="worker-member" value="{$item["id"]}" {$isCheck} data-show="{$item["names"]}"></dt>
                    <dd>
                        <div class="worker-keyword">{$item["names"]}</div>
               
                    </dd>
                  </dl>
find_lists;
                if ($isCheck) {
                    $replace["__selected__"] .= <<<selected
                        <div class="worker-added-item">
                            <span class="worker-added-item-result">{$item["fullName"]}</span>
                            <span class="worker-added-item-remove" onclick="removeWorkerItem.call(this,'{$item["id"]}')">&times;</span>
                        </div>
selected;
                    $replace["__num__"]++;
                }
                $listsHtml .= <<<listHtml
                <li><input type="checkbox" class="worker-member" value="{$item["id"]}">{$item["names"]}</li>
listHtml;
            };
        }
//        if ($subs) {//子级
//            foreach ($subs as $sub) {
//                $depNum++;
//                $result = $this->getDepWorker($sid, $sub["id"], $name, $selectedIds, $replace, false);
//                $depNum += $result["depNum"];
//                $num += $result["num"];
//                $listsHtml .= sprintf('<li><input type="checkbox" class="worker-department">%s(%d)<span class="worker-fold">展开</span>%s</li>',
//                    $sub["names"],
//                    $result["num"],
//                    $result["html"]
//                );
//            }
//        }

        return array("num" => $num, "depNum" => $depNum, "html" => $listsHtml . $subHtml . "</ul>");
    }

    public function getStdClassify($ids) {
        $ids = explode(",", $ids);
        $names = array();
        foreach ($ids as $key => $id) {
            $lists = $this->find($id);
            $names[$key] = $lists->getNames() ?: "";
        }
        return $names;
    }

    /**
     * 根据英文标识查找标准分类id
     * @param $nameEn
     * @return int
     */
    public function getStdId($nameEn) {
        $lists = $this->name("s")->select("s")->where("s.namesEn='".$nameEn."'")->getOneArray();
        return $lists['id'] ?: 0;
    }

    public function getlists($id) {
        $lists = $this->name("sc")->select("sc")->where("sc.id=" . $id)->setMax(1)->getOneArray();
        return $lists;
    }
}