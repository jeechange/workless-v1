<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

/**
 * Description of Page
 *
 * @author Administrator
 */
class Page {

    protected $options = array(
        'totalRows' => 0,
        'listRows' => 20,
        'current' => 1,
        'parameter' => 1,
        'var_page' => "p",
        'url' => "",
    );
    protected $callback;

    /**
     * 分页栏每页显示的页数
     * @var type 
     */
    public $rollPage = 5;

    /**
     * 默认列表每页显示行数
     * @var int
     */
    public $listRows = 20;

    /**
     * 起始行数
     * @var int 
     */
    public $firstRow = 0;

    /**
     * 分页总页面数
     * @var int  
     */
    protected $totalPages;

    /**
     *  总行数
     *  @var int  
     */
    protected $totalRows;

    /**
     * 当前页数
     * @var int  
     */
    protected $nowPage;

    /**
     * 分页的栏的总页数
     * @var int 
     */
    protected $coolPages;

    /**
     * 默认分页变量名
     * @var string 
     */
    protected $varPage;

    /**
     * 地址栏参数
     * @var string 
     */
    protected $parameter;

    /**
     * 包裹分页标签样式
     * @var string
     */
    public $wrapperTag = "<ul>__STRING__</ul>";

    /**
     * 分页选项样式
     * @var string
     */
    public $listTag = "<li>__STRING__</li>";

    /**
     * 分页信息样式
     * @var string
     */
    public $pageInfo = '<div class="pageInfo">共__TOTAL__条记录，每页显示__PAGESIZE__条，当前__CURRENTPAGE__/__PAGETOTAL__页</div>';

    /**
     * 选中状态分页选项样式
     * @var string 
     */
    public $listActiveTag = "<li class='active'>__STRING__</li>";

    /**
     * 空值时隐藏的部件
     * @var array 
     */
    public $emptyHide = array('prevRoll', 'nextRoll');

    /**
     * 显示分页的部件
     * @var string
     */
    public $pagination = 'firstPage|prevRoll|prevPage|linkPage|nextPage|nextRoll|endPage';

    /**
     * 变量替换
     * @var array
     */
    public $replaces = array(
        '__first__' => '第一页',
        '__end__' => '最后一页',
        '__prev__' => '上一页',
        '__next__' => '下一页',
        '__prevRoll__' => '前__ROLL__页',
        '__nextRoll__' => '后__ROLL__页',
    );

    public function clear() {
        
    }

    public function callback($callback) {
        is_callable($callback) and $this->callback = $callback;
    }

    public function showEvent() {
        if (is_callable($this->callback)) {
            return call_user_func($this->callback, $this);
        }
        return false;
    }

    public function options(array $options = array()) {
        $this->options = array_merge($this->options, $options);
    }

    public function setTotal($total) {
        $this->options["totalRows"] = $total;
    }

    /**
     * 分页参数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    private function parameter($totalRows, $listRows = '', $current = "", $parameter = '', $var_page = 'p') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage = $var_page;
        if (!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows / $this->listRows);  //总页数
        $this->coolPages = ceil($this->totalPages / $this->rollPage);
        $this->nowPage = !empty($current) ? intval($current) : 1;
        if ($this->nowPage < 1) {
            $this->nowPage = 1;
        } elseif (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }       
        $this->firstRow = $this->listRows * ($this->nowPage - 1);
    }

    public function getTotal() {
        return $this->totalPages;
    }

    /**
     * 分页显示输出
     * @access public
     */
    public function show() {
        $this->parameter($this->options['totalRows'], $this->options['listRows'], $this->options['current'], $this->options['parameter'], $this->options['var_page']);
        if (0 == $this->totalRows)
            return '';
        $p = $this->varPage;
        $nowCoolPage = ceil($this->nowPage / $this->rollPage);
        $url = $this->options["url"]? : $_SERVER['REQUEST_URI'];
        if (preg_match("/([\?&]$p\=)\d+/", $url)) {
            $url = preg_replace("/([\?&]$p\=)\d+/", '\\1__PAGE__', $url);
        } elseif (preg_match("/\?/", $url)) {
            $url.="&$p=__PAGE__";
        } else {
            $url.="?$p=__PAGE__";
        }
        $urls = parse_url($url);
        
        parse_str($urls['query'], $query);
       
        $query = array_merge((array) $query, (array) $this->parameter);
        $query = array_filter($query, function($v) {
            return trim($v) !== "";
        });
        $url = isset($urls["scheme"]) ? $urls["scheme"] . "://" : "";
        $url .= isset($urls["host"]) ? $urls["host"] : "";
        $url .= isset($urls["user"]) ? $urls["user"] : "";
        $url .= isset($urls["pass"]) ? $urls["pass"] : "";
        $url .= isset($urls["path"]) ? $urls["path"] : "";
        $url .= "?" . http_build_query($query);

        $pagination = array();
        if ($this->nowPage == 0) {
            $pagination['firstPage'] = in_array('firstPage', $this->emptyHide) ? '' : '<span>__first__</span>';
        } else {
            $pagination['firstPage'] = '<a href="' . str_replace('__PAGE__', 1, $url) . '">__first__</a>';
        }
        if ($this->nowPage >= $this->totalPages) {
            $pagination['endPage'] = in_array('endPage', $this->emptyHide) ? '' : '<span>__end__</span>';
        } else {
            $pagination['endPage'] = '<a href="' . str_replace('__PAGE__', $this->totalPages, $url) . '">__end__</a>';
        }

        $upRow = $this->nowPage - 1;
        $downRow = $this->nowPage + 1;
        if ($upRow > 0) {
            $pagination['prevPage'] = '<a href="' . str_replace('__PAGE__', $upRow, $url) . '">__prev__</a>';
        } else {
            $pagination['prevPage'] = in_array('prevPage', $this->emptyHide) ? '' : '<span>__prev__</span>';
        }

        if ($downRow <= $this->totalPages) {
            $pagination['nextPage'] = '<a href="' . str_replace('__PAGE__', $downRow, $url) . '">__next__</a>';
        } else {
            $pagination['nextPage'] = in_array('nextPage', $this->emptyHide) ? '' : '<span>__next__</span>';
        }
        if ($nowCoolPage > 1) {
            $preRow = $this->nowPage - $this->rollPage;
            $pagination['prevRoll'] = "<a href='" . str_replace('__PAGE__', $preRow, $url) . "' >__prevRoll__</a>";
        } else {
            $pagination['prevRoll'] = in_array('prevRoll', $this->emptyHide) ? '' : '<span>__prevRoll__</span>';
        }
        if ($nowCoolPage < $this->coolPages) {
            $nextRow = $this->nowPage + $this->rollPage;
            $pagination['nextRoll'] = "<a href='" . str_replace('__PAGE__', $nextRow, $url) . "' >__nextRoll__</a>";
        } else {
            $pagination['nextRoll'] = in_array('nextRoll', $this->emptyHide) ? '' : '<span>__nextRoll__</span>';
        }
        $pagination['linkPage'] = "";
        for ($i = 1; $i <= $this->rollPage; $i++) {
            $page = ($nowCoolPage - 1) * $this->rollPage + $i;
            if ($page != $this->nowPage) {
                if ($page <= $this->totalPages) {
                    $links = "<a href='" . str_replace('__PAGE__', $page, $url) . "'>" . $page . "</a>";
                    $pagination['linkPage'] .=str_replace("__STRING__", $links, $this->listTag);
                } else {
                    break;
                }
            } else {
                $pagination['linkPage'] .= str_replace("__STRING__", "<span>$page</span>", $this->listActiveTag);
            }
        }
        $widgets = explode("|", $this->pagination);
        $pageStr = "";
        foreach ($widgets as $widget) {
            if ($pagination[$widget])
                $pageStr.=$widget == "linkPage" ? $pagination[$widget] : str_replace('__STRING__', $pagination[$widget], $this->listTag);
        }
        $search = array_keys($this->replaces);
        $pageStr = str_replace($search, $this->replaces, $pageStr);
        $pageStr = str_replace("__ROLL__", $this->rollPage, $pageStr);
        return str_replace('__STRING__', $pageStr, $this->wrapperTag);
    }

    public function pageInfo() {
        $searchs = array('__TOTAL__', '__PAGESIZE__', '__CURRENTPAGE__', '__PAGETOTAL__');
        $replaces = array($this->totalRows, $this->listRows, $this->nowPage, $this->totalPages);
        return str_replace($searchs, $replaces, $this->pageInfo);
    }

    public function pageLimit($autourl, $currentSize, $tpl = "每页显示__STRING__条", $limitlists = array(3, 5, 8, 10, 12, 15, 20, 30, 50, 100, 120, 150, 300)) {
        $pagestr = "<select style='width:50px' onchange=\"location.href='" . $autourl . "?pagesize='+this.value\">";
        foreach ($limitlists as $v) {
            if ($v == $currentSize) {
                $pagestr .= "<option value=\"$v\" selected=\"selected\">$v</option>";
            } else {
                $pagestr .= "<option value=\"$v\">$v</option>";
            }
        }
        $pagestr .= "</select>";
        return str_replace("__STRING__", $pagestr, $tpl);
    }

}
