<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/20
 * Time: 12:02
 */

namespace Node\Service;


use phpex\Access\Access;

class NodeMenuService implements IMenuService {
    protected $access;

    public function __construct(Access $access) {
        $this->access = $access;
    }

    public function getAccess() {
        return $this->access;
    }

    public function getUser($key = null) {
        return $this->access->getUser($key);
    }

    public function _enable($status) {
        if (in_array($status, array(0, 1), true)) {
            return $status;
        }
        if (method_exists($this, $status . "Enable")) {
            return $this->{$status . "Enable"}() ? 1 : 0;
        }
        return 0;
    }

    public function _names($names) {
        return preg_replace_callback("/%([a-zA-Z]+)%/", array(&$this, 'callConvertNames'), $names);
    }

    public function callConvertNames($names) {
        if ($names[1] == "callConvert") {
            return "*callConvert*";
        }
        if (method_exists($this, $names[1] . "Names")) {
            return $this->{$names[1] . "Names"}();
        }
        return "&{$names[1]}&";
    }
}
