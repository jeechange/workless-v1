<?php

//使用前请先配置yunSDK/config.php


include __DIR__ . "/jeeSDK/jeeSDK.php";

$sdk = new \jeeSDK();


$post = array("section" => "testsection"); //这里是请求附加的参数,这里的键名必须跟api要求一致
$isCaches = true; //是否启用缓存，true/false;默认true
$cacheName = "test";//缓存名称,当$isCaches为true时有效


$lists = $sdk->getPage($cacheName, $post, $isCaches); //三个参数的顺序不分先后，只要注意参数的类型即可，如$post必须为数组，$isCaches必须为布尔型，$cacheName必须是字符串


if ($sdk->dataCode !== "SUCCESS") {
    echo $sdk->error;
    exit;
} else {

    //$lists
    //使用数据
}


//api 列表

//  getPage($post);       //获取指定栏目内容  $post = array("section"=>""); section: 栏目标识
//  getOnePage($post);   //获取指定栏目排列最前内容  $post =array("section"=>"");  section: 栏目标识
//  getPageById($post);   //获取指定id内容  $post =array("id"=>"");  id: 内容的id


//  getSectionList($post);  //获取指定栏目节点树  $post =array("module"=>"article");
//                          module: 可选值（page,article,questions,product）

//  getPrevAndNext($post);   //获取指定栏目记录条数  $post =array("module"=>"","id"=>"","section"=>"","showSub"=>"");
//                          module:可选值（page,article,questions,product）
//                          id: 当前ID
//                          section: 栏目标识
//                          showSub：是否统计子栏目，可选 true/flase

//  getListCount($post);   //获取指定栏目记录条数  $post =array("module"=>""，"section"=>"","countSub"=>"");
//                          module:可选值（page,article,questions,product）
//                          section: 栏目标识
//                          countSub：是否统计子栏目，可选 true/flase
//  getHits($post);        //获取指定id的点击量  $post =array("module"=>"","id"=>"");
//                           module:可选值（page,article,product）
//                           id: 内容的id
//  setHits($post);        //添加指定id的点击量  $post =array("module"=>"","id"=>"","hits"=>"");
//                           module:可选值（page,article,product）
//                           id: 内容的id

//  getArticle($post);      //获取指定栏目新闻文章  $post =array("section"=>"","page"=>"","size"=>"","showSub"=>"");
//                            section: 栏目标识
//                            page: 分页页码
//                            size: 每页条数
//                            showSub: 是否读取子栏目内容
//  getArticleById($post);    //获取指定id新闻文章 $post =array("id"=>"");  id: 文章的id


//  getQuestions($post);      //获取指定栏目的常见问题  $post =array("section"=>"","page"=>"","size"=>"",""=>"showSub");
//                            section: 栏目标识
//                            page: 分页页码
//                            size: 每页条数
//                            showSub: 是否读取子栏目内容
//  getQuestionById($post);    //获取指定id的常见问题 $post =array("id"=>"");  id: 常见问题的id


//  getAdvert($post);      //获取广告  $post =array("callName"=>""); callName: 调用标识

//  pushMessage($post);    //发送消息 $post = array("types"=>"","title"=>"","content"=>,"email"=>"","ip"=>"","address"=>"","tel"=>"","qq"=>"","code"=>"");
//                          types:  1:产品咨询 2: 问题反馈  3:BUG反馈  4:加盟合作
//                          title:  消息标题
//                          content:  消息内容
//                          email:  发送者邮箱
//                          ip:  发送者IP
//                          address:  (选填)发送者地址
//                          tel:  (选填)发送者电话
//                          qq:  (选填)发送者QQ
//                          code:  (选填)消息编码，回复时必填，回复指定的编码的消息
//  pullMessage($post);     //获取消息  $post =array("email"=>"","size"=>""); email: 发送消息时使用的邮箱,size:查看条数;当size==0时,显示全部;
//  pullMessageByCode($post);  //按编码获取消息  $post =array("code"=>""); code: 消息编码

//  getProductList($post);    //获取指定栏目产品  $post =array("section"=>"","page"=>"","size"=>"","showSub"=>"");
//                            section: 栏目标识
//                            page: 分页页码
//                            size: 每页条数
//                            showSub: 是否读取子栏目内容
//  getProductById($post);    //获取指定id产品 $post =array("id"=>"");  id: 产品的id
//  getProductListFindBy($post);    //获取指定id产品 $post =array("section"=>"","page"=>"","size"=>"","showSub"=>"","field"=>"","parameter"=>"");
//                            section: 栏目标识
//                            page: 分页页码
//                            size: 每页条数
//                            showSub: 是否读取子栏目内容
//                            field: 表的键值（注意：要这个格式的->【s.keywords】，中间的符号不是【_】是【.】）
//                            parameter: 查询的值
//                            types: like就是like查询，不写或者留空是绝对查询

//getCustomerService($post);  //获取指定客户记录条数 $post =array("size"=>"");
//                             size: 条数

//  getVideo($post);      //获取指定栏目视频  $post =array("section"=>"","page"=>"","size"=>"","showSub"=>"");
//                            section: 栏目标识
//                            page: 分页页码
//                            size: 每页条数
//                            showSub: 是否读取子栏目内容
//  getVideoById($post);    //获取指定id视频 $post =array("id"=>"");  id: 文章的id

//  getOneVideo($post);   //获取指定栏目排列最前视频  $post =array("section"=>"");  section: 栏目标识






