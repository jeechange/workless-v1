<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

use phpex\Foundation\Request;

/**
 * 表单助手
 *
 * @author Administrator
 */
class Form extends Container {

    protected $methods = array("get", "post", "put", "delete", "head");
    protected $error = "";
    protected $data = array();
    protected $request;
    protected $attributes = array(
        "id" => "",
        "name" => "",
        "action" => "",
        "method" => "post",
    );
    protected $extraAttributes = array();

    public function __construct($name, $action = "", $method = "post") {
        parent::__construct();
        $this->attributes["name"] = StrFilter::word($name);
        $this->attributes["id"] = $this->wrap->generateKey($this->attributes["name"]);
        $this->attributes["action"] = $action;
        if (in_array(strtolower($method), $this->methods)) {
            $this->attributes["method"] = $method;
        }
        $this->rule->setFormId($this->attributes["id"]);
    }

    public function setFormAttr($name, $attr) {
        if (array_key_exists($name, $this->attributes)) {
            return $this;
        }
        $this->extraAttributes[$name] = $attr;
        return $this;
    }

    public function bindRequest(Request $request) {
        $this->request = $request;
        $get = $request->get->all();
        $post = $request->post->all();
        $file = $request->files->all();
        $this->bindData(array_replace($get, $post, $file));
    }

    public function bindData(array $data) {
        foreach ($this->controls as $name => $control) {
            if (isset($data[$name])) {
                $control->setDefaultValue($data[$name]);
                $this->data[$name] = $data[$name];
            } elseif (!isset($this->data[$name])) {
                $this->data[$name] = $control->getDefaultValue();
            }
        }
    }

    public function isValid($scrf = true) {
        if (!$this->csrf->isValid($this->$this->attributes["id"], $scrf)) {
            $this->error = "非法的请求";
            return false;
        }
        $valid = $this->rule->isValid($this->data);
        if ($valid)
            return true;
        $this->error = $this->rule->getError();
        return false;
    }

    public function renderBegin() {
        $action = $this->attributes['action'] ? StrFilter::url($this->attributes['action']) : "";
        $begin = "<form action=\"$action\" id=\"{$this->attributes['id']}\" name=\"{$this->attributes['name']}\"";
        $begin.=StrFilter::attributes($this->extraAttributes);
        if (in_array($this->attributes['method'], array('get', 'post'))) {
            $begin.=" method=\"{$this->attributes['method']}\">\n";
        } else {
            $begin.=" method=\"post\"><input type=\"hidden\" name=\"_method\" value=\"{$this->attributes['method']}\">\n";
        }
        return $begin;
    }

    public function renderEnd() {
        return $this->csrf . "</form>\n" . $this->rule;
    }

    public function __toString() {
        $html = $this->renderBegin();
        $html.=$this->wrap->begin("controls", $this->attributes["name"]);
        foreach ($this->controls as $control) {
            $html.=$control->render();
        }
        $html.=$this->wrap->end("controls");
        if ($this->buttons) {
            foreach ($this->buttons as $button) {
                $html.=$button->render();
            }
        }
        $html.=$this->renderEnd();
        return $html;
    }

    public function getError() {
        return $this->error;
    }

    public function getValue($name) {
        return $this->data[$name];
    }

    public function getVaules() {
        return $this->data;
    }

}
