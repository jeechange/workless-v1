<?php

namespace Home\Controller;

include __DIR__ . "/jeeSDK/jeeSDK.php";

class IndexController extends CommonController {

    public function index() {

        $xml = '<xml><appid><![CDATA[wx72dc73a055ad06e2]]></appid>\n<bank_type><![CDATA[CFT]]></bank_type>\n<cash_fee><![CDATA[1]]></cash_fee>\n<fee_type><![CDATA[CNY]]></fee_type>\n<is_subscribe><![CDATA[Y]]></is_subscribe>\n<mch_id><![CDATA[1528752191]]></mch_id>\n<nonce_str><![CDATA[pivjlacfws0oymjz3u1xc87oh39mqrr1]]></nonce_str>\n<openid><![CDATA[o2JJi0XRVhxLhv_7jtZFYUIuxTrg]]></openid>\n<out_trade_no><![CDATA[2019032311022539750154]]></out_trade_no>\n<result_code><![CDATA[SUCCESS]]></result_code>\n<return_code><![CDATA[SUCCESS]]></return_code>\n<sign><![CDATA[A6FC0AE0DBBC96C0088434A3DC0588A8]]></sign>\n<time_end><![CDATA[20190323110239]]></time_end>\n<total_fee>1</total_fee>\n<trade_type><![CDATA[NATIVE]]></trade_type>\n<transaction_id><![CDATA[4200000297201903237320615150]]></transaction_id>\n</xml>';


        $res = curl_post("http://workless.itmakes.com/home/wxNotify", $xml);

        //控制器示例代码
        $this->assign("title", "首页");
        return $this->display();
    }

    public function price() {
        $this->assign("title", "价格");
        return $this->display();
    }

    public function helps() {
        $this->assign("curPage", "help");
        $this->assign("title", "帮助中心");

        $section = "Help center";
        $page = $this->page();
        $p = $page->currentPage ? $page->currentPage : "1";
        $pageSize = $page->listRows = "20";
        $sdk = new \jeeSDK();

        $get = Q()->get->all();
        //帮助中心
        $posts = array("section" => $section, "countSub" => "false", "module" => "article");
        $isCachess = false; //是否启用缓存，true/false;默认true
        $cacheNames = "test";//缓存名称,当$isCaches为true时有效
        $count = $sdk->getListCount($cacheNames, $posts, $isCachess);

        $post = array("section" => $section, "page" => $p, "size" => $pageSize, "showSub" => "false");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
        $lists = $sdk->getArticle($cacheName, $post, $isCaches);
        $page->setTotal($count['list_count']);

        $this->assign('lists', $lists);
        $this->assign("keyword", $get['keyword'] ? $get['keyword'] : '');

        $page->showEvent();
        $this->assign("cdnThumbBase", $this->cdnThumbBase);
        $this->assign("section", $section);

        return $this->display();
    }

    public function helps_detail($id) {
//        dump($id);die;
        $sdk = new \jeeSDK();
        $post = array("id" => "$id");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
//        $post = array("section" => "theQuestion", "page" => "1", "size" => "20", "showSub" => "false");
        $lists = $sdk->getQuestionById($post, $isCaches, $cacheName);
//        $lists = $sdk->getQuestions($cacheName, $post, $isCaches);
        $this->assign("data", $lists);
//        dump($lists);die;
        $this->assign("title", "帮助中心");

        return $this->display();
    }

    public function logs() {

        $page = $this->page();
        $p = $page->currentPage ? $page->currentPage : "1";
        $pageSize = $page->listRows = "20";
        $sdk = new \jeeSDK();
        $posts = array("section" => "Product log", "countSub" => "false", "module" => "article");
        $isCachess = false; //是否启用缓存，true/false;默认true
        $cacheNames = "test";//缓存名称,当$isCaches为true时有效
        $count = $sdk->getListCount($cacheNames, $posts, $isCachess);
//        dump($count);
        $post = array("section" => "Product log", "page" => "$p", "size" => "$pageSize", "showSub" => "false");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
        $lists = $sdk->getArticle($cacheName, $post, $isCaches);
//        dump($lists);die;
        $page->setTotal($count['list_count']);
        $page->showEvent();
        $this->assign('lists', $lists);
        $this->assign("title", "产品日志");

        return $this->display();
    }

    public function download() {

        return $this->display();
    }

    public function skill() {
        $page = $this->page();
        $p = $page->currentPage ? $page->currentPage : "1";
        $pageSize = $page->listRows = "20";
        $sdk = new \jeeSDK();
        $posts = array("section" => "skill", "countSub" => "false", "module" => "article");
        $isCachess = false; //是否启用缓存，true/false;默认true
        $cacheNames = "test";//缓存名称,当$isCaches为true时有效
        $count = $sdk->getListCount($cacheNames, $posts, $isCachess);
//        dump($count);
        $post = array("section" => "skill", "page" => "$p", "size" => "$pageSize", "showSub" => "false");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
        $lists = $sdk->getArticle($cacheName, $post, $isCaches);
//        dump($lists);die;
        $page->setTotal($count['list_count']);
        $page->showEvent();
        $this->assign('lists', $lists);
        $this->assign("title", "协作技巧");

        return $this->display();
    }

    public function skill_detail($id) {
//        dump($id);die;
        $sdk = new \jeeSDK();
        $post = array("id" => "$id");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
//        $post = array("section" => "theQuestion", "page" => "1", "size" => "20", "showSub" => "false");
        $lists = $sdk->getArticleById($post, $isCaches, $cacheName);
//        $lists = $sdk->getQuestions($cacheName, $post, $isCaches);
        $this->assign("data", $lists);
//        dump($lists);die;
        return $this->display();
    }

    public function articleDetail($id) {
        $sdk = new \jeeSDK();
        $post = array("id" => "$id");
        $isCaches = false; //是否启用缓存，true/false;默认true
        $cacheName = "test";//缓存名称,当$isCaches为true时有效
        $lists = $sdk->getArticleById($post, $isCaches, $cacheName);
        $title = $lists['s_title'];
//        dump($lists);die;
        $this->assign("title", $title);
        $this->assign("data", $lists);
        return $this->display();
    }

}