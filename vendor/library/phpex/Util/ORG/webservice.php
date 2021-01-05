<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

/**
 * Description of webservice
 *
 * @author Administrator
 */
class webservice {

    /**
     *  获取客户端实例
     * @param type $wsdl wsdl地址
     * @param type $options 选项
     * @return \SoapClient 返回实例
     */
    public function getClient($wsdl, $wsdl_mode = false) {   
        include_once main()->getMainRoot() . '/Util/ORG/lib/nusoap.php';
        $client = new \nusoap_client($wsdl,$wsdl_mode);
        $client->soap_defencoding = "UTF-8";
        $client->xml_encoding = "UTF-8";
        $client->decode_utf8 = FALSE;
        return $client;
    }

}
