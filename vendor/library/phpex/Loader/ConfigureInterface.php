<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Loader;

/**
 * Description of ConfigInterface
 *
 * @author Administrator
 */
interface ConfigureInterface {

    /**
     *
     * @param string $path 加载的路径
     * @param boolean|string $reference 索引方式 null:与原有的配置合并且覆盖原有的值，true与原有的合并但不覆盖原有的， 其它:新建的个索引
     */
    public function load($path, $reference = null);

    /**
     * 根据索引获取配置值
     * @param string $key
     * @return mixed
     */
    public function get($key = '');

    public function set($key, $value);

    /**
     * 检查指定的索引值是否存在
     * @param string $key
     * @return bollean
     */
    public function has($key);
}
