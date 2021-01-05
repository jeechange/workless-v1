<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\DModel;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use phpex\Util\ORG\Page;
use Exception;

/**
 * Description of Model
 *
 * @author Administrator
 */
abstract class DModel {

    private static $instance = array();


    public static function getInstance() {
        $class = get_called_class();
        if (isset(self::$instance[$class])) return self::$instance[$class];
        return self::$instance[$class] = new static();
    }

    /**
     * @var \Doctrine\ORM\Query
     */
    protected $query;

    CONST RULE_REQUIRE = '/\S+/'; //必填规则
    CONST RULE_EMAIL = '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/';  //邮件规则
    CONST RULE_URL = '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/'; //URL地址规则
    CONST RULE_CURRENCY = '/^\d+(\.\d+)?$/'; //货币规则
    CONST RULE_NUMBER = '/^\d+$/'; //正整数
    CONST RULE_INTEGER = '/^[-\+]?\d+$/'; //整数规则
    CONST RULE_DECIMAL = '/^[-\+]?\d+(\.\d{1,2})?$/'; //两位小数 
    CONST RULE_ENGLISH = '/^[A-Za-z]+$/'; //英文
    CONST RULE_FUNCTION = 1; //函数验证
    CONST RULE_CALLBACK = 2; //回调验证
    CONST RULE_CONFIRM = 4; //两个字值是否相同
    CONST RULE_EQUAL = 8; //验证是否等于某个值
    CONST RULE_IN = 16; //验证是否在某个范围内
    CONST RULE_LENGTH = 32; //验证长度如 3,12指长度从3到12的字符串为合法
    CONST RULE_BETWEEN = 64; //验证范围
    CONST RULE_UNIQUE = 128; //验证是否唯一
    CONST RULE_REGEX = 8; //正则表达式
    //验证触发类型
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_BOTH = 3;
    const TYPE_EMPTY = 4;
    const TYPE_EXIST = 8;
    const TYPE_VALUE = 16;
    const TYPE_ALL = 28;
    //验证方式
    const CHECK_EXIST = 1;
    const CHECK_NEED = 2;
    const CHECK_NOT_NULL = 4;
    //填充方式
    CONST FILL_STRING = 1;
    CONST FILL_FIELD = 2;
    CONST FILL_FUNCTION = 4;
    CONST FILL_CALLBACK = 8;
    CONST FILL_OBJECT = 16;
    const FILL_DATATIME = 32;

    protected $entity;
    protected $buildQuery;
    protected $alias;
    protected $error;
    protected $entities = array();
    protected $savelist = array();
    protected $form;
    protected $formElement = array();
    protected $selectField = "";
    protected $clearQueryCache = false;
    protected $rules = array();
    protected $srcData = array();
    protected $srcEntity = array();
    protected $checkData = array();
    protected $create = array();
    protected $fills = array();
    protected $metas;
    protected $scalar = false;
    protected $dateFormat = array(
        "date" => "Y-m-d",
        "time" => "H:i:s",
        "datetime" => "Y-m-d H:i:s"
    );
    protected $pagesize = 20;

    /**
     * @var EntityManager
     */
    protected $manager;

    public function __construct(EntityManager $manager = null) {
        if (null === $manager) {
            $this->manager = DM()->getManager();
        } else {
            $this->manager = $manager;
        }
        try {
            $this->metas = $this->manager->getClassMetadata($this->getMapping())->fieldMappings;
        } catch (Exception $ex) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            if ($trace[1]["class"] == "ReflectionClass")
                throw $ex;
            else
                throws($ex->getMessage(), $trace[0]["file"], $trace[0]["line"], $trace[1]["class"] . "::" . $trace[1]["function"]);
        }
        $this->_initialize();
    }

    /**
     * @param type $result
     */
    protected abstract function resolveArray(&$result);

    /**
     * @param object $result Description
     */
    protected abstract function resolveObject($result = null);

    public abstract function newEntity();

    protected function _initialize() {

    }

    public function throws(Exception $ex, $debug) {
        throws($ex->getMessage(), $debug[0]["file"], $debug[0]["line"], $ex->getCode());
    }

    public function resetDoctrine() {
        $this->getBuildQuery()->resetDQLParts();
        $this->manager->clear();
        $this->getBuildQuery()->setParameters(array());
        return $this;
    }

    public function getMetas() {
        return $this->metas;
    }

    /**
     * @return QueryBuilder
     */
    public function getBuildQuery() {
        if (null === $this->buildQuery) {
            $this->getAlias();
        }
        return $this->buildQuery;
    }

    public function groupBy($groupBy) {
        $this->getBuildQuery()->groupBy($groupBy);
        return $this;
    }

    /**
     * 设置实体的别名
     * @param string $alias
     */
    public function setAlias($alias) {
        $this->alias = strtolower($alias);
        $this->buildQuery = $this->manager->getRepository($this->getMapping())->createQueryBuilder($alias);
        $this->getBuildQuery()->resetDQLPart("from");
        $this->getBuildQuery()->from($this->getMapping(), $this->alias);
        return $this;
    }

    /**
     * 获取实体的别名
     * @return string
     */
    public function getAlias() {
        if (null === $this->alias) {
            $resolve = explode("\\", $this->getMapping());
            $this->setAlias(substr(end($resolve), 0, 1));
        }
        return $this->alias;
    }

    /**
     * 获取映射的实体类
     * @return string
     */
    public function getMapping() {
        if (null === $this->entity) {
            $namespace = get_called_class();
            $resolve = explode("\\", $namespace);
            $entity = end($resolve);
            array_pop($resolve);
            array_pop($resolve);
            $this->entity = join("\\", $resolve) . "\\Entity\\" . substr($entity, 0, -6);
        }
        return $this->entity;
    }

    /**
     * 根据id查找单条记录
     * @param integer $id
     */
    public function find($id) {
        try {
            $result = $this->manager->getRepository($this->getMapping())->find($id);
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (!$result)
            return null;
        try {
            $this->resolveObject($result);
            $this->add($result);
            return $result;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        try {
            $results = $this->manager->getRepository($this->getMapping())->findBy($criteria, $orderBy, $limit, $offset);
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        try {
            if ($results) {
                foreach ($results as $result) {
                    $this->resolveObject($result);
                    $this->add($result);
                }
            }
            return $results;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 返回所有记录
     * @param integer $type 1:返回对象 2:返回数组
     */
    public function findAll($type = 1) {
        try {
            $results = $this->manager->getRepository($this->getMapping())->findAll();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if ($results && $type == 1) {
            foreach ($results as $result) {
                $this->resolveObject($result);
                $this->add($result);
            }
            return $results;
        } else {
            $items = array();
            foreach ($results as $result) {
                $items[] = $this->toArray($result);
                $this->add($result);
            }
            return $items;
        }
        return null;
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null) {
        try {
            $result = $this->manager->getRepository($this->getMapping())->findOneBy($criteria, $orderBy);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        try {
            if ($result) {
                $this->resolveObject($result);
                $this->add($result);
            }
            return $result;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 设置实体的别名
     * @param string $alias
     * @return DModel
     */
    public function name($alias) {
        return $this->setAlias($alias);
    }

    /**
     * 设置实体的别名-原生
     * @param string $alias
     * @return DModel
     */
    public function native($alias) {

    }

    /**
     * 设置字段
     * @param string $fields
     */
    public function select($fields = null) {
        if (empty($fields)) {
            $fields = $this->getAlias();
        }
        try {
            $this->selectField = $fields;
            $this->getBuildQuery()->select($fields);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 指定查询条件 支持安全过滤
     * @access public
     * @param mixed $where 条件表达式
     * @param array|string $parameters
     * @param mixed $parameter
     * @return DModel
     */
    public function where($where, $parameters = null, $parameter = null) {
        if (empty($where)) {
            return $this;
        }
        try {
            $this->getBuildQuery()->where($where);
            if (empty($parameters)) {
                return $this;
            }
            $args = is_array($parameters) ? $parameters : array($parameters => $parameter);
            $this->getBuildQuery()->setParameters($args);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 指定查询条件 支持安全过滤
     * @access public
     * @param mixed $where 条件表达式
     * @param array|string $parameters
     * @param mixed $parameter
     * @return DModel
     */
    public function andWhere($where, $parameters = null, $parameter = null) {
        if (empty($where)) {
            return $this;
        }
        try {
            $this->getBuildQuery()->andWhere($where);
            if (empty($parameters)) {
                return $this;
            }
            $args = is_array($parameters) ? $parameters : array($parameters => $parameter);
            $this->getBuildQuery()->setParameters(array_merge($this->getBuildQuery()->getParameters(), $args));
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 指定查询条件 支持安全过滤
     * @access public
     * @param mixed $where 条件表达式
     * @return DModel
     */
    public function orWhere($where, $parameters = null, $parameter = null) {
        if (empty($where)) {
            return $this;
        }
        try {
            $this->getBuildQuery()->orWhere($where);
            if (empty($parameters)) {
                return $this;
            }
            $args = is_array($parameters) ? $parameters : array($parameters => $parameter);
            $this->getBuildQuery()->setParameters(array_merge($this->getBuildQuery()->getParameters(), $args));
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * @param array|string $parameters
     * @param mixed $parameter
     * @return DModel
     */
    public function setParameter($parameters, $parameter = null, $clear = true) {
        try {
            if (empty($parameters)) {
                return $this;
            }
            $args = is_array($parameters) ? $parameters : array($parameters => $parameter);
            if ($clear) {
                $this->getBuildQuery()->setParameters($args);
            } else {
                $this->getBuildQuery()->setParameters(array_merge($this->getBuildQuery()->getParameters(), $args));
            }
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    public function clearParameter() {
        $this->getBuildQuery()->setParameters();
        return $this;
    }

    /**
     * 左连接
     * @access public
     * @param string $entity
     * @param string $alias
     * @param string $on
     * @return DModel
     */
    public function leftJoin($entity, $alias = null, $on = null) {
        try {
            if (!$alias || !$on) {
                list($lentity, $lalias, $lon) = preg_split("\s+", $entity);
                $entity = $lentity;
                $alias = $alias ?: $lalias;
                $on = $on ?: $lon;
            }
            $this->getBuildQuery()->leftJoin($this->joinEntity($entity), $alias, "WITH", $on);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 内连接
     * @access public
     * @param string $entity
     * @param string $alias
     * @param string $on
     * @return DModel
     */
    public function innerJoin($entity, $alias = null, $on = null) {
        try {
            if (!$alias || !$on) {
                list($lentity, $lalias, $lon) = preg_split("\s+", $entity);
                $entity = $lentity;
                $alias = $alias ?: $lalias;
                $on = $on ?: $lon;
            }
            $this->getBuildQuery()->innerJoin($this->joinEntity($entity), $alias, "WITH", $on);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    protected function joinEntity($entity) {
        if (false !== strpos($entity, "\\"))
            return $entity;
        $namespace = get_called_class();
        $resolve = explode("\\", $namespace);
        $resolveentity = explode("\\", $entity);
        return $resolve[0] . "\\Entity\\" . end($resolveentity);
    }

    /**
     * 指定查询数量
     * @access public
     * @param mixed $first 起始位置
     * @param mixed $max 查询数量
     * @return DModel
     */
    public function limit($first, $max) {
        try {
            $this->getBuildQuery()->setFirstResult($first)->setMaxResults($max);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 设置最大返回记录数
     * @param integer $number Description
     */
    public function setMax($number = null) {
        if ($number === null) {
            return $this;
        }
        try {
            $this->getBuildQuery()->setMaxResults($number);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 设置返回的起始记录数
     * @param integer $offset Description
     */
    public function setFirst($offset = 0) {
        try {
            $this->getBuildQuery()->setFirstResult($offset);
            return $this;
        } catch (Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    private function buildSelect() {
        $from = $this->getBuildQuery()->getDQLPart("from");
        if (empty($from)) {
            $this->select();
        }
    }

    /**
     * 将查询结果以数组方式返回
     * @return array[] Description
     */
    public function getArray($scalar = false, $resolve = true) {
        $this->scalar = $scalar;
        $this->buildSelect();
        $this->query = $this->getBuildQuery()->getQuery();
        //$this->query->iterate(null,3);
        $this->query->expireResultCache($this->clearQueryCache);
        try {
            $results = $this->query->getResult($scalar ? 3 : 2);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (!$results)
            return null;
        try {
            if ($resolve) {
                foreach ($results as &$result) {
                    $this->resolveArray($result);
                }
            }
            return $results;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 获取一条记录作为数组返回
     * @param type $scalar
     * @return null|Array
     */
    public function getOneArray($scalar = false, $resolve = true) {
        $this->scalar = $scalar;
        $this->buildSelect();
        $this->query = $this->getBuildQuery()->getQuery();
        $this->query->expireResultCache($this->clearQueryCache);
        try {
            $results = $this->query->getOneOrNullResult($scalar ? 3 : 2);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (!$results)
            return null;
        try {
            if ($resolve) {
                $this->resolveArray($results);
            }
            return $results;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 将查询结果以对象方式返回
     * @return Object[] Description
     */
    public function getObject($resolve = true) {
        $this->buildSelect();
        $this->query = $this->getBuildQuery()->getQuery();
        $this->query->expireQueryCache($this->clearQueryCache);
        try {
            $results = $this->query->getResult();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (!$results)
            return null;
        try {
            if ($resolve) {
                foreach ($results as $result) {
                    $this->resolveObject($result);
                    $this->add($result);
                }
            }
            return $results;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 获取一条记录作为对象返回
     * @return null|Object
     */
    public function getOneObject($resolve = true) {
        $this->buildSelect();
        $this->query = $this->getBuildQuery()->getQuery();
        $this->query->expireQueryCache($this->clearQueryCache);
        try {
            $results = $this->query->setMaxResults(1)->getOneOrNullResult(1);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (!$results)
            return null;
        try {
            if ($resolve) {
                $this->resolveObject($results);
            }
            return $results;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * 总记录数
     */
    public function count() {
        $this->select("count(distinct " . $this->getAlias() . ")");
        $DQLParts = $this->getBuildQuery()->getDQLParts();
        $this->query = $this->getBuildQuery()->getQuery();
        try {
            if (count($DQLParts["groupBy"]) > 0) {
                $sql = $this->query->getSQL();

                $params = $this->query->getParameters();
                $args = array();
                foreach ($params as $p) {
                    $args[] = $p->getValue();
                }
                $sql = "select count(*) tmp from ({$sql}) as tmp_table";

                $conn = $this->manager->getConnection();

                $stat = $conn->prepare($sql);
                $stat->execute($args ?: null);

                $count = $stat->fetchColumn(0);
                return $count ?: 0;
            }
            return $this->query->getSingleScalarResult();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * @return mixed
     */
    public function min($field) {
        try {
            $result = $this->manager->getRepository($this->getMapping())->findOneBy(array(), array($field => "ASC"));
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        try {
            if ($resolve) {
                $this->resolveObject($result);
                $this->add($result);
            }
            return $result;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * @return mixed
     */
    public function max($field) {
        try {
            $result = $this->manager->getRepository($this->getMapping())->findOneBy(array(), array($field => "DESC"));
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        try {
            if ($resolve) {
                $this->resolveObject($result);
                $this->add($result);
            }
            return $result;
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
    }

    /**
     * @return mixed
     */
    public function sum($field) {
        if (!strpos($field, "."))
            $field = $this->getAlias() . "." . $field;
        $this->select("sum(" . $field . ")");
        $this->limit(null, null);
        try {
            $this->query = $this->getBuildQuery()->getQuery();
            return $this->query->getSingleScalarResult();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * @param object $entity
     */
    public function add($entity) {
        if (!is_object($entity)) {
            $ex = new Exception("Add entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        $sid = spl_object_hash($entity);
        if (isset($this->entities[$sid])) {
            return $this;
        }
        $this->entities[$sid] = $entity;
        try {
            $this->manager->persist($entity);
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * @param object $entity
     */
    public function save($entity = null) {
        if (!is_object($entity)) {
            $ex = new Exception("Save entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        $sid = spl_object_hash($entity);
        if (isset($this->savelist[$sid])) {
            return $this;
        }
        $this->entities[$sid] = $entity;
        $this->savelist[$sid] = $sid;
        try {
            $this->manager->persist($entity);
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * @param object $entity
     */
    public function remove($entity) {
        if (!is_object($entity)) {
            $ex = new Exception("Remove entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        try {
            $this->manager->remove($entity);
            if (isset($this->entities[$sid])) {
                unset($this->entities[$sid]);
            }
            if (isset($this->savelist[$sid])) {
                unset($this->savelist[$sid]);
            }
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 字段值增长
     * @param string $field 字段名
     * @param string $step 增长值
     */
    public function setInc($field, $step = 1) {
        list($alias) = explode(".", $field);
        $this->getBuildQuery()->resetDQLPart("set");
        $this->query = $this->getBuildQuery()->update($this->getMapping(), $alias)
            ->set($field, $field . "+" . $step)
            ->getQuery();
        try {
            return $this->query->execute();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 字段值减少
     * @access public
     * @param string $field 字段名
     * @param integer $step 减少值
     * @return boolean
     */
    public function setDec($field, $step = 1) {
        list($alias) = explode(".", $field);
        $this->getBuildQuery()->resetDQLPart("set");
        $this->query = $this->getBuildQuery()->update($this->getMapping(), $alias)
            ->set($field, $field . "-" . $step)
            ->getQuery();
        try {
            return $this->query->execute();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    public function update($data) {
        $this->getBuildQuery()->update();
        $this->getBuildQuery()->resetDQLPart("set");
        foreach ($data as $key => $value) {
            $this->getBuildQuery()->set($key, $value);
        }
        $this->query = $this->getBuildQuery()->getQuery();
        try {
            return $this->query->execute();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    public function delete() {
        $this->query = $this->getBuildQuery()->delete()->getQuery();
        try {
            return $this->query->execute();
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 获取一条记录的某个字段值
     * @access public
     * @param string $field 字段名
     * @param string $spea 字段数据间隔符号 NULL返回数组
     * @return mixed
     */
    public function getField($scalar = false) {
        // TODO 1 getField
    }

    /**
     * 执行SQL查询
     * @access public
     * @param string $sql SQL指令
     * @param array $params 替代参数
     * @return mixed
     */
    public function query($sql, $params = null) {
        try {
            $stmt = $this->manager->getConnection()->prepare($sql);
            if ($params) {
                foreach ($params as $key => $val) {
                    $stmt->bindValue(":{$key}", $val);
                }
            }
            return $stmt->execute($sql);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 执行DQL语句
     * @access public
     * @param string $dql SQL指令
     * @return false | integer
     */
    public function execute($dql, $parameters = null) {
        try {
            return $this->manager->createQuery($dql)->execute($parameters);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 获取最后执行的sql语句
     * @param type $isexit
     */
    public function sql($isexit = true) {
        /* @var $Logger \phpex\Driver\SQLLogger */
        $Logger = $this->manager->getConnection()->getConfiguration()->getSQLLogger();
        $sql = $Logger->lastSql();
        $str = "";
        if ($sql[0]) {
            $str .= $sql[0];
        }
        if ($sql[1]) {
            $str .= "<br> sql Params:" . http_build_query($sql[1]);
        }
        if ($sql[2]) {
            $str .= "<br> sql Params types:" . http_build_query($sql[2]);
        }
        if ($isexit) {
            echo $str;
            exit;
        } elseif ($isexit === false) {
            echo $str;
        } else {
            return $str;
        }
    }

    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans() {
        try {
            $this->manager->beginTransaction();
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 提交事务
     * @access public
     * @return boolean
     */
    public function commit() {
        try {
            $this->manager->commit();
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback() {
        try {
            $this->manager->rollback();
            return $this;
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     * 将单条对象转换成数组
     * @param type $entity
     * @return type
     */
    public function toArray($entity) {
        if (!$entity) {
            return "";
        }
        if (!is_object($entity)) {
            $ex = new Exception("toArray entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        $ref = new \ReflectionObject($entity);
        /* @var $properties \ReflectionProperty[] */
        $properties = $ref->getProperties(\ReflectionMethod::IS_PRIVATE);
        $items = array();
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $items[$property->getName()] = $property->getValue($entity);
        }
        $this->scalar = false;
        $this->resolveArray($items);
        return $items;
    }

    /**
     * 将对象方式的结果转换成数组方式的结果
     * @param array $lists
     * @return type
     */
    public function listToArray(array $lists) {
        $items = array();
        foreach ($lists as $key => $entity) {
            if (!is_object($entity)) {
                $ex = new Exception("listToArray entities item must be for the object(" . typeof($entity) . " given) ");
                $this->throws($ex, debug_backtrace());
            }
            $items[$key] = $this->toArray($entity);
        }
        return $items;
    }

    public function EntityAccess($entity) {
        return new EntityAccess($entity);
    }

    /**
     * 查询缓存
     * @access public
     * @param mixed $key
     * @param integer $expire
     * @param string $type
     * @return DModel
     */
    public function cache($key = true, $expire = null, $type = '') {
        // TODO 1 cache
    }

    /**
     * 填充实体
     * @param object $entity
     * @param array $data
     * @param boolean $format 是否格式化
     */
    public function fillEntity($entity, array &$data = array(), $format = true) {
        if (!is_object($entity)) {
            $ex = new Exception("fillEntity entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        $ref = new \ReflectionObject($entity);
        /* @var $properties \ReflectionProperty[] */
        $properties = $ref->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($properties as $property) {
            if (!isset($data[$property->getName()]) && !$format) {
                continue;
            }
            $property->setAccessible(true);
            $value = (isset($data[$property->getName()]) && (null !== $data[$property->getName()])) ? $data[$property->getName()] : $property->getValue($entity);
            if ($format)
                $value = $this->formatTransform($property->getName(), $value);
            $data[$property->getName()] = $value;
            $property->setValue($entity, $value);
        }
        return $entity;
    }

    /**
     * 转换格式
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function formatTransform($name, $value) {
        if (!$this->metas[$name])
            return $value;
        switch (strtolower($this->metas[$name]["type"])) {
            case "boolean":
                return (bool)$value;
            case "integer":
                return (int)$value;
            case "double":
                return (double)$value;
            case "decimal":
                return (double)number_format($value, $this->metas[$name]["scale"] ?: 2, ".", "");
            case "date":
            case "time":
            case "datetime":
                if ($value === null)
                    return null;
                return is_object($value) && $value instanceof \DateTime ? $value :
                    \DateTime::createFromFormat($this->dateFormat[strtolower($this->metas[$name])] ?: $this->dateFormat["datetime"], (string)$value);
            case "string":
            default:
                return (string)$value;
        }
    }

    public function clearEntity($entity) {
        $this->manager->clear($entity);
    }

    public function refresh($entity) {
        $this->manager->refresh($entity);
    }

    /**
     * 使用自动填充规则创建数据
     * @param array $data
     * @param type $entity
     * @param callback $autoFill
     */
    public function create(array &$data, $entity = null, $autoFill = "_fill") {
        if (null === $entity) {
            $entity = empty($this->entities) ? $this->newEntity() : current($this->entities);
        }
        $this->srcEntity = $entity ? clone $entity : null;
        try {
            $data = $this->autoFill($data, $entity, $autoFill);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        if (empty($data)) {
            return $this;
        }
        try {

            $this->fillEntity($entity, $data);

            $this->create = array($entity, $data);
            return $this;
        } catch (Exception $ex) {
            if ($ex instanceof \phpex\Error\Exception) {
                throw $ex;
            }
            $this->throws($ex, debug_backtrace());
        }
    }

    private function autoFill($data, $entity, $method = "_fill") {
        if (!$entity || !is_object($entity)) {
            throw new Exception("autoFill entities must be for the object(" . typeof($entity) . " given) ");
        }
        $this->srcData = $data;
        $methods = explode(",", $method);
        foreach ($methods as $m) {
            if (method_exists($this, $m))
                $this->{$m}();
        }
        $ref = new \ReflectionObject($entity);
        /* @var $idpro \ReflectionProperty */
        $idpro = $ref->hasProperty("id") ? $ref->getProperty("id") : false;
        $idpro && $idpro->setAccessible(true);
        if ($idpro && $idpro->getValue($entity)) {
            $trigger = self::TYPE_UPDATE;
        } else {
            $trigger = self::TYPE_INSERT;
        }
        $items = array();
        foreach ($this->fills as $fill) {
            $item = isset($data[$fill["name"]]) ? $data[$fill["name"]] : null;
            if (!($fill["type"] & $trigger))
                continue;

            if (null === $item && !($fill["type"] & self::TYPE_EXIST))
                continue;
            if (empty($item) && !($fill["type"] & self::TYPE_EMPTY))
                continue;
            if (!empty($item) && !($fill["type"] & self::TYPE_VALUE))
                continue;
            switch ($fill["rule"]) {
                case self::FILL_STRING;
                case self::FILL_OBJECT;
                    $items[$fill["name"]] = $fill["params"];
                    break;
                case self::FILL_FIELD:
                    if (isset($data[$fill["params"]])) {
                        $items[$fill["name"]] = $data[$fill["params"]];
                    } elseif ($ref->hasProperty($fill["params"])) {
                        $pro = $ref->getProperty($fill["params"]);
                        $pro->setAccessible(true);
                        $items[$fill["name"]] = $pro->getValue($entity);
                    }
                    break;
                case self::FILL_FUNCTION:
                    $func = new \ReflectionFunction($fill["params"]);
                    $params = $func->getParameters();
                    if (isset($params[1]) && $params[1]->getName() == "entity")
                        $items[$fill["name"]] = $func->invoke($item, $entity);
                    else
                        $items[$fill["name"]] = $func->invoke($item);
                    break;
                case self::FILL_CALLBACK:
                    $items[$fill["name"]] = call_user_func(array(&$this, $fill["params"]), $item, $entity);
                    break;
                case self::FILL_DATATIME:
                    $items[$fill["name"]] = $item instanceof \DateTime ? $item :
                        \DateTime::createFromFormat($fill["params"] ?: $this->dateFormat["datetime"], (string)$item ?: now());
                    break;
            }
        }
        foreach ($items as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * 添加一条自动填充
     * @param string $name 要填充表单名，非数据库的字段，即使数据库中不存在的字段也可以
     * @param string $params 填充参数
     * @param integer $rule 填充规则，值为本类中 FILL_ 开头的常量
     * @param integer $trigger_type 填充触发类型，值为本类中 TYPE_  开头的常量
     */
    public function addFill($name, $params, $rule = self::FILL_STRING, $trigger_type = self::TYPE_INSERT) {
        if ($trigger_type & self::TYPE_EMPTY || $trigger_type & self::TYPE_EXIST || $trigger_type & self::TYPE_VALUE) {
            $trigger = $trigger_type;
        } else {
            $trigger = $trigger_type | self::TYPE_ALL;
        }
        $this->fills[] = array(
            "name" => $name,
            "rule" => $rule,
            "params" => $params,
            "type" => $trigger
        );
        return $this;
    }

    /**
     * 使用自动校验规则校验数据
     * @param array $data
     * @param object $entity
     * @param callback $autoCheck
     */
    public function check(array $data = array(), $entity = null, $autoCheck = "_check") {
        if (isset($this->create[0]) && null === $entity)
            $entity = $this->create[0];
        if (isset($this->create[1]) && empty($data))
            $data = $this->create[1];
        if (!$entity || !is_object($entity)) {
            $ex = new Exception("check entities must be for the object(" . typeof($entity) . " given) ");
            $this->throws($ex, debug_backtrace());
        }
        $methods = explode(",", $autoCheck);
        try {
            foreach ($methods as $m) {
                if (method_exists($this, $m))
                    $this->{$m}();
            }
        } catch (Exception $ex) {
            throws($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getCode());
        }
        $this->checkData = $data;
        if (method_exists($entity, "getId") && $entity->getId()) {
            return $this->_updateCheck($data, $entity);
        } else {
            return $this->_insertCheck($data, $entity);
        }
    }

    public function getError() {
        return $this->error;
    }

    public function flush($entity = null) {
        try {
            $this->manager->flush($entity);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
    }

    /**
     *
     * @param string $sort
     * @param array $order
     * @return DModel
     */
    public function order($sort, $order = null) {
        $this->getBuildQuery()->addOrderBy($sort, $order);
        return $this;
    }

    public function data_sort($value = "", $key = "data_sort") {
        if (!$value) $value = Q()->get->get($key);
        if ($value) {
            $sorts = explode("|", $value);
            $this->getBuildQuery()->addOrderBy($sorts[0], isset($sorts[1]) && $sorts[1] != 2 ? "ASC" : "DESC");
        }
        return $this;
    }

    /**
     * 添加一条验证规则
     * @param string $name 要验证的表单名，非数据库的字段，即使数据库中不存在的字段也可以
     * @param string $rule 验证规则,值为本类中 RULE_ 开头的常量
     * @param string $msg 错误时提示的消息
     * @param string $params 规则参数
     * @param integer $condition 验证条件，值为本类中 CHECK_ 开头的常量
     * @param integer $trigger_type 触发验证类型,值为本类中 TYPE_  开头的常量
     * @param integer $data_type 数据类型，1:自动填充前的数据，2:自动填充后的数据，3:如自动填充前没有数据则使用自动填充后的数据(默认)
     */
    public function addRule($name, $rule, $msg, $params = "", $condition = self::CHECK_EXIST, $trigger_type = self::TYPE_BOTH, $data_type = 3) {
        $this->rules[$name][$rule] = array("msg" => $msg, "params" => $params, "condition" => $condition, "type" => $trigger_type, "data_type" => $data_type);
        return $this;
    }

    private function _insertCheck($datas, $entity) {
        $items = array();
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule => $params) {
                if ($params["type"] === self::TYPE_UPDATE) {
                    continue;
                }
                if ($params["data_type"] == 1) {
                    $items[$field] = $this->srcData[$field];
                } elseif ($params["data_type"] == 3 && isset($this->srcData[$field])) {
                    $items[$field] = $this->srcData[$field];
                } else {
                    $items[$field] = $datas[$field];
                }
                if (!isset($items[$field]) && $params["condition"] === self::CHECK_EXIST) {
                    continue;
                }
                if ((!isset($items[$field]) || empty($items[$field])) && $params["condition"] === self::CHECK_NOT_NULL) {
                    continue;
                }
                if (!isset($items[$field])) {
                    $this->error = $params["msg"];
                    return false;
                }
                if (empty($items[$field])) {
                    $this->error = $params["msg"];
                    return false;
                }
                if (isset($this->validate[$rule])) {
                    if (!preg_match($this->validate[$rule], $items[$field])) {
                        $this->error = $params["msg"];
                        return false;
                    }
                    continue;
                }
                $switch = $this->_switch($rule, $field, $params, $items, $entity);
                if (!$switch) {
                    $this->error = $params["msg"];
                    return FALSE;
                }
                if ($rule == self::RULE_UNIQUE) {
                    $result = $this->manager->getRepository($this->getMapping())->findOneBy(array($field => $items[$field]));
                    if ($result) {
                        $this->error = $params["msg"];
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function _updateCheck($datas, $entity) {
        $items = array();
        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule => $params) {

                if ($params["type"] === self::TYPE_INSERT) {
                    continue;
                }
                if ($params["data_type"] == 1) {
                    $items[$field] = $this->srcData[$field];
                } elseif ($params["data_type"] == 3 && isset($this->srcData[$field])) {
                    $items[$field] = $this->srcData[$field];
                } else {
                    $items[$field] = $datas[$field];
                }
                if (!isset($items[$field]) && $params["condition"] === self::CHECK_EXIST) {
                    continue;
                }
                if ((!isset($items[$field]) || empty($items[$field])) && $params["condition"] === self::CHECK_NOT_NULL) {
                    continue;
                }
                if (!isset($items[$field])) {
                    $this->error = $params["msg"];
                    return false;
                }
                if (empty($items[$field])) {
                    $this->error = $params["msg"];
                    return false;
                }
                if (isset($this->validate[$rule])) {
                    if (!preg_match($this->validate[$rule], $items[$field])) {
                        $this->error = $params["msg"];
                        return false;
                    }
                    continue;
                }
                $switch = $this->_switch($rule, $field, $params, $items, $entity);
                if (!$switch) {
                    $this->error = $params["msg"];
                    return FALSE;
                }
                if ($rule == self::RULE_UNIQUE) {
                    $result = $this->manager->getRepository($this->getMapping())->findOneBy(array($field => $items[$field]));
                    if ($result && $result->getId() != $entity->getId()) {
                        $this->error = $params["msg"];
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function _switch($rule, $field, $params, $data, $entity) {
        switch ($rule) {
            case self::RULE_REGEX:
                if (!preg_match($params["params"], $data[$field])) {
                    return false;
                }
                break;
            case self::RULE_FUNCTION:
                $validate = call_user_func($params["params"], $data[$field], $entity);
                if (!$validate) {
                    return false;
                }
                break;
            case self::RULE_CALLBACK:
                $validate = call_user_func(array(&$this, $params["params"]), $data[$field], $entity);
                if (!$validate) {
                    return false;
                }
                break;
            case self::RULE_CONFIRM:
                if ($data[$field] != $data[$params["params"]]) {
                    return false;
                }
                break;
            case self::RULE_EQUAL:
                if ($data[$field] != $params["params"]) {
                    return false;
                }
                break;
            case self::RULE_IN:
                $arrays = is_array($params["params"]) ? $params["params"] : explode(",", $params["params"]);
                if (!in_array($data[$field], $arrays)) {
                    return false;
                }
                break;
            case self::RULE_LENGTH:
                $arrays = is_array($params["params"]) ? $params["params"] : explode(",", $params["params"]);
                if (strlen($data[$field]) < $arrays[0]) {
                    return false;
                }
                if (isset($arrays[1]) && strlen($data[$field]) > $arrays[1]) {
                    return false;
                }
                break;
            case self::RULE_BETWEEN:
                $arrays = is_array($params["params"]) ? $params["params"] : explode(",", $params["params"]);
                if (($data[$field]) < $arrays[0]) {
                    return false;
                }
                if (isset($arrays[1]) && ($data[$field]) > $arrays[1]) {
                    return false;
                }
                break;
        }
        return true;
    }

    public function setPage(Page $page = null) {
        if (null === $page) {
            $page = ins()->get("app." . ucfirst(R()->getAppName()))->getRunController()->page();
        }

        $selectField = $this->selectField;
        $page->setTotal($this->count());
        if ($page->showEvent()) {
            $this->limit($page->firstRow, $page->listRows);
        }
        try {
            $this->select($selectField);
        } catch (\Exception $ex) {
            $this->throws($ex, debug_backtrace());
        }
        return $this;
    }

    /**
     * 获取一页记录
     * @param integer $pagination 页码，从0开始算
     * @param integer $size 每页记录
     */
    public function page($pagination = 0, $size = null) {
        if (is_numeric($size)) {
            $size = (int)$size;
        } else {
            $size = $this->pagesize;
        }
        $this->setFirst($pagination * $size)->setMax($size);
        return $this;
    }

}
