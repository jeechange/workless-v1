<?php

namespace Node\Controller;

use phpex\Library\Controller;

abstract class CommonController extends Controller {

    protected function _initialize() {

    }

    public function success($message = '', $jumpUrl = '', $time = 3, $tpl = "Public:success") {
        $this->assign(array("msg" => $message, 'jumpUrl' => $jumpUrl, 'time' => $time));
        $this->assign("ajaxextramethod", "SideForm");
        return $this->display($tpl);
    }

    public function handleReturn($handle, $parameter = array(), $tpl = 'Public:handleReturn') {
        $assign = array("handle" => $handle, "parameter" => $parameter);
        return $this->display($tpl, $assign, false);
    }

    public function error($message = '', $jumpUrl = '', $time = 5, $tpl = "Public:error") {
        $this->assign(array("msg" => $message, 'jumpUrl' => $jumpUrl, 'time' => $time));
        $this->assign("ajaxextramethod", "SideForm");
        return $this->display($tpl);
    }
}