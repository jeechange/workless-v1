<?php

namespace Admin\DModel;

use Admin\Entity\User;
use Doctrine\ORM\EntityManager;
use phpex\DModel\DModel;

class StaffDModel extends DModel {

    public $statusMemo = array(
        1 => "正式员工",
        2 => "试用期员工",
        3 => "离职员工",
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
        return new \Admin\Entity\Staff();
    }


    public function addStaff($sid, $userId, $module = "Consoles", $roleName = "staff", $post = array()) {

        $staff = $this->findOneBy(array("sid" => $sid, "userId" => $userId));
        if ($staff) return false;
        /** @var User $user */
        $user = UserDModel::getInstance()->find($userId);

        if ($staff || !$user) return false;

        $roleDM = RbacRoleDModel::getInstance();

        $roleFind = preg_match("/^[0-9]+$/", $roleName) ?
            array("sid" => $sid, "module" => $module, "id" => $roleName) :
            array("sid" => $sid, "module" => $module, "roleName" => $roleName);

        $role = $roleDM->findOneBy($roleFind);

        if (!$role) {
            $role = $roleDM->newEntity();
            $role->setSid($sid);
            $role->setNames("员工");
            $role->setModule($module);
            $role->setRoleName($roleName);
            $role->setStatus(1);
            $role->setWeight(3);
            $role->setSort(0);
            $roleDM->add($role)->flush($role);
        }

        $staff = $this->newEntity();
        $staff->setSid($sid);
        $staff->setFullName($user->getFullName());
        $staff->setUserId($userId);
        $staff->setUserName($user->getUserName());
        $staff->setRoleName($roleName);
        $staff->setEffect(0);
        $staff->setPoint(0);
        $staff->setDepartment($post["department"] ?: 0);
        $staff->setBonus($post["bonus"] ?: 0);
        $staff->setStation($post["station"] ?: 0);
        $staff->setPhone($user->getPhone());
        $staff->setAddTime(nowTime());
        $staff->setStatus($post["status"] ?: 1);

        $staff->setWx($post["wx"]);
        $staff->setQq($post["qq"]);
        $staff->setEmail($post["email"]);
        $staff->setMemo($post["memo"]);


        $this->add($staff)->flush($staff);
        return true;

    }

    public function workers($sid, $selectedIds = array(), $max = 20, $readonly = false) {
        $depDM = DepartmentDModel::getInstance();

        if (is_scalar($selectedIds)) $selectedIds = explode(",", $selectedIds);
        $selectedIds = array_filter($selectedIds);

        $deps = $depDM->name("d")->where("d.sid=$sid and d.parentid=0")->getArray();

        $html = <<<work
               <div class="searchbar">
                   <div class="search-input">
                     <label class="icon icon-search"></label>
                     <input type="search"  placeholder='输入关键字...'/>
                   </div>
               </div>
               <div class="list-block media-list worker-added-list-box">
                   <div class="worker-added-find-list list-block media-list"><ul>__find_lists__</ul></div>
                   <div class="worker-added-list list-block media-list">__lists__</div>                    
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
//        if (!$deps) return str_replace(array_keys($replace), $replace, $html);

        $result = $this->getDepWorkerH5($sid, 0, $selectedIds, $replace);
        $replace["__lists__"] = $result["html"];

        $submitString = <<<submit
                   <div class="worker-selected">
                       <span class="worker-selected-memo">已选择：<span class="worker-selected-num">__num__</span>人</span>           
                       <span class="worker-confirm" data-max="__total__">确定<span class="worker-selected-num">__num__</span>/__total__</span>
                   </div>
submit;


        return array(
            "confirm" => str_replace(array_keys($replace), $replace, $submitString),
            "selected" => $replace["__selected__"],
            "body" => str_replace(array_keys($replace), $replace, $html)
        );
    }

    public function workHtml($PopupId, $PopupTitle, $accept) {
        $html = <<<work
<div class="popup {$PopupId}-popup worker-added-box" data-relate-id="{$PopupId}">
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-right close-popup">
            关闭
        </a>
        <h1 class="title">{$PopupTitle}</h1>
    </header>
    <div class="bar bar-standard bar-footer list-staff-block" style="background: #fff;">
        {$accept["confirm"]}    
    </div>
    <div class="content">
        <div class="content-inner">
            <div class="content-block">
               {$accept["body"]}   
            </div>
        </div>
    </div>
</div>
work;

        return $html;
    }


    public function getDepWorkerH5($sid, $depId, $selectedIds, &$replace, $isRoot = true) {
        $lists = $this->name("s")->where("s.department=$depId and s.sid=$sid")->getArray();
        $depDM = DepartmentDModel::getInstance();
        $dep = $depDM->name("s")->where("s.id=$depId and s.sid=$sid")->setMax(1)->getOneArray();
        $subs = $depDM->name("d")->where("d.parentid=$depId and d.sid=$sid")->getArray();
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
                $isCheck = in_array($item["userId"], $selectedIds) ? "checked" : "";
                $replace["__find_lists__"] .= <<<find_lists
                       <li>
                            <label class="label-checkbox item-content worker-member-label" data-value="{$item["userId"]}" data-show="{$item["fullName"]}">                               
                                <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title worker-keyword">{$item["fullName"]}</div>
                                    </div>
                                    <div class="item-subtitle">{$dep["names"]}</div>
                                </div>
                            </label>
                        </li>
find_lists;

                if ($isCheck) {
                    $replace["__selected__"] .= <<<selected
                        <div class="worker-added-item">
                            <span class="worker-added-item-result">{$item["fullName"]}</span>
                            <span class="worker-added-item-remove" onclick="removeWorkerItem.call(this,'{$item["userId"]}')">&times;</span>
                        </div>
selected;
                    $replace["__num__"]++;
                }

                $listsHtml .= <<<listHtml
                <li>
                    <label class="label-checkbox item-content worker-member-label" data-value="{$item["userId"]}">                      
                        <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title">{$item["fullName"]}</div>
                            </div>
                        </div>
                    </label>
                </li>               
listHtml;
            };
        }

        if ($subs) {
            foreach ($subs as $sub) {
                $depNum++;

                $result = $this->getDepWorkerH5($sid, $sub["id"], $selectedIds, $replace, false);
                $depNum += $result["depNum"];
                $num += $result["num"];

                $listsHtml .= <<<listsHtml
                         <li>
                            <label class="label-checkbox item-content worker-department-label">
                                <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
                                <div class="item-inner">
                                    <div class="item-title-row">
                                        <div class="item-title">{$sub["names"]}({$result["num"]}人)</div>
                                        <div class="item-after">展开</div>
                                    </div>
                                </div>
                            </label>
                            {$result["html"]}
                        </li>

listsHtml;
            }
        }

        return array("num" => $num, "depNum" => $depNum, "html" => $listsHtml . $subHtml . "</ul>");
    }


    public function workerList($sid, $name, $selectedIds = array(), $max = 20, $readonly = false) {
        $depDM = DepartmentDModel::getInstance();

        if (is_scalar($selectedIds)) $selectedIds = explode(",", $selectedIds);

        $selectedIds = array_filter($selectedIds);


        $deps = $depDM->name("d")->where("d.sid=$sid and d.parentid=0")->getArray();

        $box1 = $name . "Box";
        $html = <<<work
        <div class="{$box1} worker-box__readonly__">
            __selected__
            <span class="worker-added">+</span>
            <div class="worker-added-box">
               <input type="text" class="worker-finder" placeholder="请输入关键词">
               <div class="worker-added-find-list">__find_lists__</div>
               <div class="worker-added-list">__lists__</div>
               <div class="worker-selected">
                   <span class="worker-selected-memo">已选择：<span class="worker-selected-num">__num__</span>人</span>           
                   <span class="worker-confirm" data-id="__listsId__" data-max="__total__">确定<span class="worker-selected-num">__num__</span>/<span id="__listsId__Max">__total__</span></span>
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
            "__listsId__" => $name,
            "__userids__" => array()
        );
//        if (!$deps) return str_replace(array_keys($replace), $replace, $html);

        $result = $this->getDepWorker($sid, 0, $name, $selectedIds, $replace);
        $replace["__lists__"] = $result["html"];
        return str_replace(array_keys($replace), $replace, $html);


    }

    private function getDepWorker($sid, $depId, $name, $selectedIds, &$replace, $isRoot = true) {

        $lists = $this->name("s")->where("s.department=$depId and s.sid=$sid")->getArray();
        $depDM = DepartmentDModel::getInstance();
        $dep = $depDM->name("s")->where("s.id=$depId and s.sid=$sid")->setMax(1)->getOneArray();

        $subs = $depDM->name("d")->where("d.parentid=$depId and d.sid=$sid")->getArray();
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
                $isCheck = in_array($item["userId"], $selectedIds) ? "checked" : "";
                if (!in_array($item["userId"], $replace["__userids__"])) {
                    $inputName = "name=\"{$name}[]\"";
                    array_push($replace["__userids__"], $item["userId"]);
                } else {
                    $inputName = "";
                }
                $replace["__find_lists__"] .= <<<find_lists
                   <dl>
                   <label>
                    <dt><input type="checkbox" {$inputName} class="worker-member" value="{$item["userId"]}" {$isCheck} data-show="{$item["fullName"]}"></dt>
                    <dd>
                    
                        <div class="worker-keyword">{$item["fullName"]}</div>
                        <div>{$dep["names"]}</div>                    
                    </dd>
                    </label>
                  </dl>
find_lists;
                if ($isCheck && $inputName) {
                    $replace["__selected__"] .= <<<selected
                        <div class="worker-added-item">
                            <span class="worker-added-item-result">{$item["fullName"]}</span>
                            <span class="worker-added-item-remove" onclick="removeWorkerItem.call(this,'{$item["userId"]}')">&times;</span>
                        </div>
selected;
                    $replace["__num__"]++;
                }
                $listsHtml .= <<<listHtml
                <li><label><input type="checkbox" class="worker-member" value="{$item["userId"]}" {$isCheck}>{$item["fullName"]}</label></li>
listHtml;
            };
        }


        if ($subs) {
            foreach ($subs as $sub) {
                $depNum++;

                $result = $this->getDepWorker($sid, $sub["id"], $name, $selectedIds, $replace, false);
                $depNum += $result["depNum"];
                $num += $result["num"];
                $listsHtml .= sprintf('<li><input type="checkbox" class="worker-department" id="%s"><label for="%s" style="margin-bottom:0;line-height:26px;height:26px">%s(%d人)</label><span class="worker-fold">展开</span>%s</li>',
                    $name . "_" . $sub["id"],
                    $name . "_" . $sub["id"],
                    $sub["names"],
                    $result["num"],
                    $result["html"]
                );
            }
        }

        return array("num" => $num, "depNum" => $depNum, "html" => $listsHtml . $subHtml . "</ul>");
    }

    public function getStaff($user, $sid) {
        $userId = $user["id"];

        if (!$userId || !$sid) return array();

        $staff = $this->name("s")->where("s.userId= $userId and s.sid=$sid")->setMax(1)->getOneArray();
        if ($staff) return $staff;

        $staff = $this->newEntity();
        $staff->setSid($sid);
        $staff->setFullName($user["fullName"]);
        $staff->setUserId($userId);
        $staff->setUserName($user["userName"]);
        $staff->setRoleName("staff");
        $staff->setPhoto($user["photo"]);
        $staff->setEffect(0);
        $staff->setPoint(0);
        $staff->setDepartment(0);
        $staff->setStation(0);
        $staff->setPhone($user["phone"]);
        $staff->setAddTime(nowTime());
        $staff->setStatus(1);
        $this->add($staff)->flush($staff);

        return $this->toArray($staff);
    }

    public function getList($sid, $depId = 0, $deepi = 0, $depth = 0, $count = 0) {

        $depDM = DepartmentDModel::getInstance();
        $comMemberDM = CompanyMemberDModel::getInstance();
        $userDM = UserDModel::getInstance();
        $stationDM = StaffDModel::getInstance();
        $deps = $depDM->name("s")->where("s.parentid=$depId and s.sid=$sid")->getArray();

        $listStr = "";

        if ($depId == 0) {
            $lists = $this->name("s")->where("(s.department=0 or s.department is null or s.department ='') and s.sid=$sid")->getArray();
            if ($lists) {
                $listStr .= "<tr><td>未设置部门员工</td><td colspan='9'></td></tr>";
                foreach ($lists as $item) {
                    $comMember = $comMemberDM->name("c")->select("c")->where("c.userId=" . $item['userId'] . " AND c.sid=" . $sid . "AND c.status=1")->setMax(1)->getOneArray();
                    $leaderName = "";
                    if ($comMember) {
                        $url1 = url("consoles_mod", array("con" => "MyUser", "id" => $comMember["id"]));
                        if ($comMember['leader'] > 0) {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px'>{$userDM->getUserName($comMember['leader'])}</a>";
                        } elseif ($comMember['leader'] == -1) {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px'>无</a>";
                        } else {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px' style='color: red;'>未设置</a>";
                        }
                    }
                    //职位
                    $station = $stationDM->name("s")->select("s")->where("s.id=" . $item['station'] . " AND s.sid=" . $sid . "AND s.status=1")->setMax(1)->getOneArray();
                    $stationMemo = "";
                    if ($station) {
                        $stationMemo = $station['names'];
                    }
                    $listStr .= "<tr>";
                    $listStr .= "<td>{$item["fullName"]}</td>";
                    $listStr .= "<td>{$item["phone"]}</td>";
                    $listStr .= "<td>{$leaderName}</td>";
                    $listStr .= "<td>{$item["wx"]}</td>";
                    $listStr .= "<td>{$item["qq"]}</td>";
                    $listStr .= "<td>{$item["email"]}</td>";
                    $listStr .= "<td>{$stationMemo}</td>";
                    $listStr .= "<td>{$item["memo"]}</td>";
                    $statusMemo = $this->statusMemo[$item["status"]];
                    $listStr .= "<td>{$statusMemo}</td>";

                    $listStr .= "<td>";
                    $url = url("consoles_mod", array("con" => "Staff", "id" => $item["id"]));
                    $listStr .= "<a href='{$url}' data-side-form='900px'>修改</a>";
                    $url01 = url("consoles_delete", array("con" => "Staff", "id" => $item["id"]));
                    $listStr .= "";
                    if ($item["status"] == 3) {
                        $listStr .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$url01}' data-confirm='一旦删除，不可恢复，确定吗？'>删除</a>";
                    }
                    $listStr .= "</td>";

                    $listStr .= "</tr>";
                }
            }
        }
        if (!$deps) return "";

        $subDepth = $depth + 1;
        foreach ($deps as $dep) {
            $depthstr = $this->depth($deepi, $count, $depth);
            $deepi++;

            $listStr .= "<tr><td>{$depthstr}{$dep["names"]}</td><td colspan='9'></td></tr>";
            $lists = $this->name("s")->where("s.department = {$dep["id"]} and s.sid=$sid")->getArray();
            $subDepCount = $depDM->name("s")->where("s.parentid={$dep["id"]} and s.sid=$sid")->count();
            $subDepCount += count($lists);
            $subI = 0;
            if ($lists) {

                foreach ($lists as $item) {
                    $comMember = $comMemberDM->name("c")->select("c")->where("c.userId=" . $item['userId'] . " AND c.sid=" . $sid . "AND c.status=1")->setMax(1)->getOneArray();
                    $leaderName = "";
                    if ($comMember) {
                        $url1 = url("consoles_mod", array("con" => "MyUser", "id" => $comMember["id"]));
                        if ($comMember['leader'] > 0) {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px'>{$userDM->getUserName($comMember['leader'])}</a>";
                        } elseif ($comMember['leader'] == -1) {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px'>无</a>";
                        } else {
                            $leaderName = "<a  href='{$url1}' data-side-form='900px' style='color: red;'>未设置</a>";
                        }
                    }
                    //职位
                    $station = $stationDM->name("s")->select("s")->where("s.id=" . $item['station'] . " AND s.sid=" . $sid . "AND s.status=1")->setMax(1)->getOneArray();
                    $stationMemo = "";
                    if ($station) {
                        $stationMemo = $station['names'];
                    }
                    $subdepthstr = $this->depth($subI, $subDepCount, $subDepth);
                    $subI++;
                    $listStr .= "<tr>";
                    $listStr .= "<td>{$subdepthstr}{$item["fullName"]}</td>";
                    $listStr .= "<td>{$item["phone"]}</td>";
                    $listStr .= "<td>{$leaderName}</td>";
                    $listStr .= "<td>{$item["wx"]}</td>";
                    $listStr .= "<td>{$item["qq"]}</td>";
                    $listStr .= "<td>{$item["email"]}</td>";
                    $listStr .= "<td>{$stationMemo}</td>";
                    $listStr .= "<td>{$item["memo"]}</td>";
                    $statusMemo = $this->statusMemo[$item["status"]];
                    $listStr .= "<td>{$statusMemo}</td>";
                    $listStr .= "<td>";
                    $url = url("consoles_mod", array("con" => "Staff", "id" => $item["id"]));
                    $listStr .= "<a href='{$url}' data-side-form='900px'>修改</a>";
                    $url01 = url("consoles_delete", array("con" => "Staff", "id" => $item["id"]));
                    if ($item["status"] == 3) {
                        $listStr .= "&nbsp;&nbsp;|&nbsp;&nbsp;<a href='{$url01}' data-confirm='一旦删除，不可恢复，确定吗？'>删除</a>";
                    }
                    $listStr .= "</td>";
                    $listStr .= "</tr>";
                }
            }
            $listStr .= $this->getList($sid, $dep["id"], $subI, $subDepth, $subDepCount);
        }
        return $listStr;
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


    public function getMaxAcorn($userId, $sid) {
        $staffDM = StaffDModel::getInstance();
        $staff = $staffDM->name("s")->select("s,ss")
            ->leftJoin("StaffStation", "ss", "s.station = ss.id")
            ->where("s.userId = {$userId} and s.sid = {$sid}")
            ->limit(0, 1)
            ->order("ss.limitAcorn", "asc")
            ->order("ss.riseAcorn", "desc")
            ->getOneArray(true);
        if (!$staff) {
            $stationDM = StaffStationDModel::getInstance();
            $count = $stationDM->name("s")->where("s.sid=$sid")->count();
            if (!$count) return 0;
            return -1;
        }
        if ($staff["ss_limitAcorn"] == 0) return 0;
        return intval($staff["ss_riseAcorn"]);
    }


}