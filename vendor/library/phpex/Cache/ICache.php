<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2016/12/19
 * Time: 9:51
 */

namespace phpex\Cache;


interface ICache {

    public function get($key);

    public function set($key, CacheEntity $cacheEntity);

    public function clear();

    public function remove($key);

    public function isValid($key);
}
