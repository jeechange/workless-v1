<?php

namespace {
    $loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
    ins()->addInstance("core.autoload", $loader, array("autoload"));
    $loader->set("", array(__DIR__));
    return $loader;
}
