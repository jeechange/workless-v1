<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/3/17
 * Time: 10:13
 */

namespace phpex\Helper\Search;


use phpex\Helper\Search\RichElement\BaseElement;
use phpex\Helper\Search\RichElement\Choice;
use phpex\Helper\Search\RichElement\Coin;
use phpex\Helper\Search\RichElement\Date;
use phpex\Helper\Search\RichElement\DateTime;
use phpex\Helper\Search\RichElement\Multi;
use phpex\Helper\Search\RichElement\Time;
use phpex\DModel\DModel;
use phpex\Library\Controller;

class RichSearch {

    /** @var BaseElement[] */
    private $fields = array();

    private $data = array();

    private $controller;


    private $header = "";

    private $footer = "";

    private $autoButton = true;
    private $prefix = "";


    public function __construct(Controller $controller, $prefix = "") {
        $this->controller = $controller;
        $this->prefix = $prefix;
    }


    public function addCoin($key, $label) {
        return $this->fields[$key] = new Coin($key, $label, $this->prefix);
    }


    public function addChoice($key, $label) {
        return $this->fields[$key] = new Choice($key, $label, $this->prefix);
    }


    public function addMulti($key, $label) {
        return $this->fields[$key] = new Multi($key, $label, $this->prefix);
    }

    public function addDate($key, $label) {
        return $this->fields[$key] = new Date($key, $label, $this->prefix);
    }


    public function addTime($key, $label) {
        return $this->fields[$key] = new Time($key, $label, $this->prefix);
    }

    public function addDateTime($key, $label) {
        return $this->fields[$key] = new DateTime($key, $label, $this->prefix);
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function bindData($data) {
        return $this->setData($data);
    }

    private function queryData() {
        if (!isset($_SERVER['QUERY_STRING'])) return array();
        parse_str($_SERVER['QUERY_STRING'], $data);
        return $data;
    }

    public function build(&$where = "", &$searchForm = "", &$parameters = array()) {
        if (!$this->data) {
            $this->data = $this->queryData();
        }
        foreach ($this->fields as $field) {
            $field->setData($this->data);
            $filesWhere = $field->where();
            $where .= $filesWhere ? $filesWhere . ' AND ' : '';
            $searchForm .= $field->form() . "\n";
            $tempParameters = $field->parameter();
            foreach ($tempParameters as $pKey => $pVal) {
                $parameters[$pKey] = $pVal;
            }
        }
        $searchForm = $this->header . '<table class="rich-search">' . $searchForm . $this->submit() . '</table>' . $this->footer;
        $where = substr($where, 0, -5);
        $this->controller->assign($this->prefix . "searchForm", $searchForm);
    }


    private function submit() {
        if ($this->autoButton === true)
            return "";

    }

    public function autoSubmit($submit = true) {
        $this->autoButton = $submit;
        return $this;
    }

    public function setHeader($header) {
        $this->header = $header;
        return $this;
    }

    public function setFooter($footer) {
        $this->footer = $footer;
        return $this;
    }

    public function fetch(DModel $dModel, $scalar = false) {
        $this->build($where, $searchForm, $parameters);
        $dModel->andWhere($where)->setParameter($parameters, null, false);
        return $dModel->getArray($scalar);
    }

    public function getValue($key) {
        if (!isset($this->fields[$key])) return null;
        $this->fields[$key]->setData($this->data);
        return $this->fields[$key]->getValues();
    }

}
