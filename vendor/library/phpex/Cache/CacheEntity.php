<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2016/12/19
 * Time: 10:59
 */

namespace phpex\Cache;


class CacheEntity {

    public $value, $timeout = 0;


    public function __construct($value, $timeout = 0, $namespace = "root") {
        $this->timeout = $timeout;
        $this->value = $value;
    }



    public function setTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

}
