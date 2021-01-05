<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Driver;

use Doctrine\DBAL\Logging\SQLLogger as ISQLLogger;

/**
 * Description of SQLLogger
 *
 * @author Administrator
 */
class SQLLogger implements ISQLLogger {

    private $sqls = array();

    public function startQuery($sql, array $params = null, array $types = null) {
        static $i = 0;
        $this->sqls[$i] = array($sql, $params, $types);
    }

    public function stopQuery() {
        
    }

    public function sql() {
        return $this->sqls;
    }

    public function lastSql() {
        end($this->sqls);
        $sql = current($this->sqls);
        reset($this->sqls);
        return $sql;
    }

//put your code here
}
