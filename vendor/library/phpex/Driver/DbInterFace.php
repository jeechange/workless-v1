<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Driver;

use Doctrine\ORM\EntityManager;

/**
 * Description of DbInterFace
 *
 * @author Administrator
 */
interface DbInterFace {
    /**
     * 设置数据库配置    
     * @param array|string $options 选项数组或选项名
     * @param string $value 选项值
     */
    public function setOptions($options = null, $value = "");

   

    /**
     * 获取实体管理对象
     * @param string $name 配置名
     * @return EntityManager|null
     */
    public function getManager();

    /**
     * 添加命名空间的路径映射
     * @param strin $name 配置名
     * @param string $namespace 命名空间名
     * @param string $path 路径
     */
    public function addPath($namespace, $path);
}
