<?php

namespace Consoles\Controller;

use Admin\DModel\DynamicDModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9
 * Time: 11:00
 */
class DynamicController extends CommonController {


    public function lists() {
        $dynamicDM = DynamicDModel::getInstance();

        $sid = $this->getUser("sid");

        $lists = $dynamicDM->name("d")->where("d.did=0 and d.sid= $sid")->limit("0", 20)->order("d.addTime", "DESC")->getArray();

        $this->assign("lists", $lists);

        return $this->display();

    }

}
