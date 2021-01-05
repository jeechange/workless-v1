<?php


include __DIR__ . "/Des3.php";

class jeeSDK {

    private $config = array();

    public $error = "";

    public $httpCode = 200;

    public $dataCode = "";

    public function __construct($config = null) {
        $this->config = $config ?: include "config.php";
    }

    public function __call($name, $params) {
        $cacheKey = "cache_" . $name;
        $post = array();
        $userCache = true;
        $i = 0;
        foreach ($params as $val) {
            if ($i == 3) break;
            if (is_string($val)) $cacheKey = "cache_" . $val;
            if (is_bool($val)) $userCache = $val;
            if (is_array($val)) $post = $val;
            $i++;
        }
        if ($userCache) {
            return $this->readCache($name, $post, $cacheKey);
        }

        $cachePath = $this->config["cache_path"];
        $cacheFile = $cachePath . "/" . $cacheKey . ".php";
        return $this->request($name, $post, $cacheFile);
    }

    private function readCache($name, $post, $cacheKey) {
        $cachePath = $this->config["cache_path"];
        $cacheFile = $cachePath . "/" . $cacheKey . ".php";
        if (!is_file($cacheFile)) {
            return $this->request($name, $post, $cacheFile);
        }

        $cacheData = include $cacheFile;

        if ($cacheData["timeout"] == 0) return $cacheData["value"];

        if (time() - $cacheData["addTime"] > $cacheData["timeout"]) return $this->request($name, $post, $cacheFile);

        $this->dataCode = "SUCCESS";
        return $cacheData["value"];
    }


    private function request($name, $post, $cacheFile) {

        $url = $this->config["api"] . "?action=" . $name;

        $des = new \Des3($this->config["des_key"], $this->config["des_iv"]);

        $jeedata = $des->encrypt(json_encode($post));

        $postdata = array(
            "code_id" => $this->config['code_id'],
            "jeedata" => $jeedata,
            "jeesign" => md5($this->config['app_secret'] . $jeedata)
        );
        $encodePost = urlencode(json_encode($postdata));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //跳过证书验证
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodePost);
        $output = curl_exec($ch); //执行并获取HTML文档内容
        $this->error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == "200") {
            $this->error = "";
            return $this->parseOutput($output, $cacheFile);
        }
        $this->httpCode = intval($httpCode);

        $this->dataCode = "HTTP_ERR";

        $this->error = $output;

        return false;

    }

    private function parseOutput($output, $cacheFile) {
        $res = json_decode($output, true);
        if (!$res) {
            $this->dataCode = "RES_PARSE_ERR";
            $this->error = "回调数据解析失败:" . json_last_error_msg();
            return false;
        }

        $this->dataCode = $res['code'];
        if ($res['code'] != "SUCCESS") {
            $this->error = $res["code"] . ":" . $res["message"];
            return false;
        }
        $des = new \Des3($this->config["des_key"], $this->config["des_iv"]);
        $data = json_decode($des->decrypt($res['jeedata']),true);
        if (!$data) {
            return $data;
        }

        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 777, true);
        }

        $values = array(
            "addTime" => time(),
            "value" => $data,
            "timeout" => $this->config["timeout"]
        );

        $cacheStr = $cahceStr = "<?php return " . var_export($values, true) . ";\n";

        file_put_contents($cacheFile, $cacheStr);
        return $data;
    }


}