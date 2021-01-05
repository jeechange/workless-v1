<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Driver;

use Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\Common\Cache\ApcCache,
    Doctrine\ORM\Configuration;
use Doctrine\Common\Persistence\Mapping\Driver\phpexFileLocator,
    Doctrine\ORM\Mapping\Driver\PHPDriver,
    Doctrine\ORM\Mapping\Driver\XmlDriver,
    Doctrine\ORM\Mapping\Driver\YamlDriver;

/**
 * Description of Doctrine
 *
 * @author Administrator
 */
class Doctrine implements DbInterFace {

    protected $configs = array();
    protected $options = array();
    protected $paths = array();
    protected $entityManager;

    /**
     * @var \Doctrine\DBAL\Statement
     */
    protected $lastStatement;

    /**
     * 构造一个数据库实例    
     * @param string $mapMethod
     * @param boolean $isDevMode
     */
    public function __construct() {
        
    }

    public function setOptions($options = null, $value = "") {
        if (empty($options)) {
            return true;
        }
        if (is_array($options)) {
            $this->options = array_merge($this->options, $options);
            return true;
        }
        $this->options[$options] = $value;
    }

    public function addPath($namespace, $path) {
        $this->paths[$namespace] = $path;
        return true;
    }

    /**
     * 添加一个数据库实例
     * @param array $configure
     * @param array $paths
     * @param string $mapType
     * @param boolean $isDevMode
     */
    public function create($configure, $paths, $mapType = "yml", $isDevMode = false) {
        foreach ($paths as $namespace => $path) {
            $this->addPath($namespace, $path);
        }
        $config = $this->getConfig($this->paths, $mapType, $isDevMode);
        $config->addCustomStringFunction("REGEXP", "phpex\Driver\Regexp" . preg_replace("/_([a-z])/", '$1', ucfirst($configure["driver"])));
        $this->entityManager = EntityManager::create($configure, $config, $this->getPrefixEvent($configure["prefix"]));
    }

    private function getPrefixEvent($prefix) {
        $evm = new \Doctrine\Common\EventManager;
        $tablePrefix = new TablePrefix($prefix);
        $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);
        return $evm;
    }

    private function getConfig($paths, $mapType = "yml", $isDevMode = false) {
        //$cache = new ArrayCache; // $isDevMode ?  : new ApcCache();
        $cache = new ArrayCache();
        //$cache->save('cache_id', 'my_data');
        $config = new Configuration();
        $config->setMetadataCacheImpl($cache);
        switch (strtolower($mapType)) {
            case "annotation":
                $locator = new phpexFileLocator($paths);
                $driver = new PHPDriver($locator);
                break;
            case "xml":
                $locator = new phpexFileLocator($paths, ".xml");
                $driver = new XmlDriver($locator);
                break;
            case "yml":
            default:
                $locator = new phpexFileLocator($paths, ".yml");
                $driver = new YamlDriver($locator);
        }
        $config->setMetadataDriverImpl($driver);
        $config->setQueryCacheImpl($cache);
        $proxies = main()->getRuntime() . "/roxies";
        if (!is_dir($proxies))
            mkdir($proxies, 777, true);
        $config->setProxyDir($proxies);
        $config->setProxyNamespace('runtime\cache\proxies');
        $config->setAutoGenerateProxyClasses($isDevMode);
        $config->setSQLLogger(new SQLLogger());
        return $config;
    }

    /**
     * 
     * @return EntityManager
     */
    public function getManager() {
        return $this->entityManager;
    }

    public function execute($sql, $params = array(), $prefix = "") {
        $this->lastStatement = $stmt = $this->prepare($sql, $prefix);
        try {
            $result = $stmt->execute($params);
        } catch (\Exception $ex) {
            debug_throws("%d:'%s'", $stmt->errorCode(), $ex->getMessage());
        }
        return $result;
    }

    public function getLastStatement() {
        return $this->lastStatement;
    }

    /**
     * 解析sql
     * @param type $sql
     * @param type $prefix
     * @return \Doctrine\DBAL\Statement
     */
    public function prepare($sql, $prefix = "") {
        if (!$prefix)
            $prefix = C("database.default.prefix");
        $sql = preg_replace("/__(\w+)__/", $prefix . '$1', $sql);
        $conn = $this->getManager()->getConnection();
        return $conn->prepare($sql);
    }

}
