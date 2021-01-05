<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2018/6/20
 * Time: 11:52
 */

namespace Node\Service;


use phpex\Access\Access;

interface IMenuService {

    public function __construct(Access $access);


    /**
     *@return Access
     */
    public function getAccess();

    public function getUser($key = null);

    public function _enable($status);

    public function _names($names);
}
