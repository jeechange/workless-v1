<?php
/**
 * Created by PhpStorm.
 * User: 28613
 * Date: 2016/1/22
 * Time: 11:45
 */

namespace phpex\DModel;


class EntityAccess implements \Iterator, \ArrayAccess, \Countable {
    private $entity = null;
    /**
     * @var \ReflectionProperty[]
     */
    private $pro = array();

    public function __construct($entity) {
        $this->entity = $entity;
        if ($entity && is_object($entity)) {
            $ref = new \ReflectionObject($entity);
            $properties = $ref->getProperties(\ReflectionProperty::IS_PRIVATE);
            foreach ($properties as $pro) {
                $pro->setAccessible(true);
                $this->pro[$pro->name] = $pro;
            }
        }
    }

    public function __call($method, $arg) {
        if ($this->entity && is_object($this->entity) && method_exists($this->entity, $method)) {
            return call_user_func_array(array($this->entity, $method), $arg);
        }
        return null;
    }

    /**
     * ArrayAccess::offsetExists 标识一个元素是否定义
     * @param type $offset
     * @return type
     */
    public function offsetExists($offset) {
        return isset($this->pro[$offset]);
    }

    /**
     * ArrayAccess::offsetGet 返回一个元素的值
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset) {
        if ($this->entity && is_object($this->entity) && isset($this->pro[$offset])) {
            return $this->pro[$offset]->getValue($this->entity);
        }
        return null;
    }

    /**
     * ArrayAccess::offsetSet 为一个元素的赋值
     * @param type $offset
     * @param type $value
     */
    public function offsetSet($offset, $value) {
        if ($this->entity && is_object($this->entity) && isset($this->pro[$offset])) {
            $this->pro[$offset]->setValue($this->entity, $value);
        }
    }

    /**
     * ArrayAccess::offsetUnset 删除一个元素
     * @param type $offset
     * @return type
     */
    public function offsetUnset($offset) {
        throw new \Exception('Do not allow the deleted from the Result');
    }

    /**
     * Countable::count
     * @param type $mode
     * @return type
     */
    public function count($mode = 'COUNT_NORMAL') {
        return count($this->pro);
    }

    /**
     * Iterator::key  返回当前元素的键值
     * @return type
     */
    public function key() {
        return key($this->pro);
    }

    /**
     * Iterator::rewind  移到首元素
     * @return type
     */
    public function rewind() {
        reset($this->pro);
    }

    /**
     * Iterator::valid  判定是否还有后续元素, 如果有, 返回true
     * @return type
     */
    public function valid() {
        return key($this->pro) !== null;
    }

    /**
     * Iterator::current 返回当前元素值
     * @return type
     */
    public function current() {
        $key = key($this->pro);
        if ($key !== null) {
            return $this->offsetGet($key);
        }
        return null;
    }

    /**
     * Iterator::current 下移一个元素
     * @return type
     */
    public function next() {
        next($this->pro);
    }

}
