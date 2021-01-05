<?php
//Consoles function


function highlight($strings, $keywords, $count = 1, $className = "highlight") {
    $targetStr = sprintf('<span class="%s">%s</span>', $className, $keywords);
    if ($count) {
        $expStr = explode($keywords, $strings, $count + 1);
    } else {
        $expStr = explode($keywords, $strings);
    }
    $res = "";
    $len = count($expStr) - 1;
    foreach ($expStr as $item) {
        $res .= $item;
        if ($len > 0) $res .= $targetStr;
        $len--;
    }
    return $res;
}

function dumps($vars) {

    $string = Latte\Runtime\Filters::escapeJs(dump($vars, false));
    $htmls = <<<htmls
<script>
 layer.open({
                        title: "打印内容", type: 1, closeBtn: 1, shadeClose: true, shade: 0.3,
                        skin: 'layui-layer-rim', 
                        area: ["800px", "600px"], 
                        content: $string 
                    });
 </script>
htmls;
    echo $htmls;
}