<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2016/12/19
 * Time: 9:56
 */

namespace phpex\Cache;


class Cache {

    /** @var  ICache */

    private static $cache;

    public static function setHeader(ICache $cache) {
        self::$cache = $cache;
    }


    public static function get($key, $callback = null) {
        self::buildHeader();

        if (self::$cache->isValid($key)) {
            return self::$cache->get($key);
        }
        if (is_callable($callback)) {
            $cacheEntity = call_user_func($callback);
            self::set($key, $cacheEntity);
        }
        return self::$cache->get($key);
    }


    public static function set($key, $caches) {
        self::buildHeader();
        if (!$key) {
            return false;
        }
        if (is_object($caches) && get_class($caches) == 'phpex\Cache\CacheEntity') {
            self::$cache->set($key, $caches);
        } else {
            $entity = new CacheEntity($caches);
            self::$cache->set($key, $entity);
        }
        return true;
    }

    public static function clear() {
        self::buildHeader();
        return self::$cache->clear();
    }

    public static function remove($key) {
        self::buildHeader();
        return self::$cache->remove($key);
    }

    private static function buildHeader() {
        if (self::$cache) return;
        self::$cache = new FileCache();
    }


}
