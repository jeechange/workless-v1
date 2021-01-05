<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

/**
 * Description of Csrf
 *
 * @author Administrator
 */
class Csrf {

    private $formId;
    private $sessionName = "__CSRF__";
    private $open = true;
    private $validity = 0;
    private $csrfs = array();

    public function __construct($formId = null) {
        $this->formId = $formId;
    }

    public function open($open = true) {
        $this->open = (bool) $open;
        return $this;
    }

    public function __toString() {
        if (!$this)
            return "";
        if (function_exists('Q')) {
            $this->csrfs = Q()->getSession()->get($this->sessionName);
        } else {
            $this->csrfs = $_SESSION[$this->sessionName];
        }
        $key = $this->getKey();
        $token = $this->buildToken();
        $this->csrfs[$key][$this->validity] = array('time' => time(), 'token' => $token);
        function_exists('Q') ?
                        Q()->getSession()->set($this->sessionName, $this->csrfs)->save() :
                        $_SESSION[$this->sessionName] = $this->csrfs;
        return sprintf('<input type="hidden" name="%s" value="%s">', $key, $token)."\n";
    }

    public function setFormId($formId) {
        $this->formId = $formId;
        return $this;
    }

    public function setSessionName($sessionName) {
        $this->sessionName = $sessionName;
        return $this;
    }

    public function setValidity($second) {
        $this->validity = (int) $second;
    }

    public function isValid($isOn = true, $reset = false) {
        if (!$isOn)
            return true;
        if (function_exists('Q')) {
            $this->csrfs = Q()->getSession()->get($this->sessionName);
            $post = Q()->post->all();
        } else {
            $this->csrfs = $_SESSION[$this->sessionName];
            $post = $_POST;
        }
        $key = $this->getKey();
        if (!isset($post[$key]))
            return false;
        if (!isset($this->csrfs[$key]) || !isset($this->csrfs[$key][$this->validity])) {
            return false;
        }
        $time = $this->csrfs[$key][$this->validity]["time"];
        if ($this->validity > 0 && (time() < $time + $this->validity))
            return false;
        if ($post[$key] != $this->csrfs[$key][$this->validity]['token'])
            return false;
        if ($reset) {
            unset($this->csrfs[$key][$this->validity]);
            function_exists('Q') ?
                            Q()->getSession()->set($this->sessionName, $this->csrfs)->save() :
                            $_SESSION[$this->sessionName] = $this->csrfs;
        }
        return true;
    }

    public function getKey() {
        return md5($this->formId);
    }

    public function buildToken() {
        return md5(rand(10000, 99999));
    }

}
