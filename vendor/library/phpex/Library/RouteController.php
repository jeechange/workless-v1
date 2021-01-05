<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/6/9
 * Time: 9:59
 */

namespace phpex\Library;


class RouteController {
    private $relateClass;

    private $relateAction;

    private $relateArgs = array();

    public function __construct($class) {
        $this->relateClass = $class;
    }

    public function __call($action, $args) {
        if ($this->relateAction) return $this;
        $this->relateAction = $action;
        $this->relateArgs = $args;
        return $this;
    }

    public function invoke() {

    }


}
