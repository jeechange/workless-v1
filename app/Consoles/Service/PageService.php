<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Consoles\Service;

use Consoles\Controller\CommonController;
use phpex\Util\ORG\Page;

/**
 * 分页设置
 */
class PageService {


    protected $sizeList = array(3, 5, 8, 10, 12, 15, 20, 30, 50, 100, 120, 150, 300);

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


    public $var_page = "p";

    public $url = "";

    public $currentPage = 1;


    public $total = 0;

    public $pageTotal = 0;

    /**
     * @var CommonController
     */

    private $controller;

    public function __construct($controller, $var = "p", $currentPage = null, $pSize = null) {
        if ($pSize === null) {
            $pSize = Q()->cookies->get("page_size_" . $var);
        }
        $pSize = in_array($pSize, $this->sizeList) ? intval($pSize) : 50;
        if ($currentPage === null) {
            $currentPage = Q()->get->get($var) > 0 ? intval(Q()->get->get($var)) : 1;
        } else {
            $currentPage = intval($currentPage) > 0 ? intval($currentPage) : 1;
        }
        $this->listRows = $pSize;

        $this->var_page = $var;

        $this->url = Q()->getRequestUri();

        $this->currentPage = $currentPage;
        $this->controller = $controller;
        return $this;
    }

    public function setPageSize($pSize) {
        $this->listRows = in_array($pSize, $this->sizeList) ? intval($pSize) : $this->listRows;
        return $this;
    }

    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage > 0 ? intval($currentPage) : $this->currentPage;
        return $this;
    }

    public function setVar($var) {
        $this->var_page = $var;
        return $this;
    }


    public function setTotal($total) {
        $this->total = $total;
        return $this;
    }

    public function showEvent() {
        $total = $this->total;
        $pSize = $this->listRows;
        $currentPage = $this->currentPage;
        $var = $this->var_page;
        $sizeList = $this->sizeList;

        $pageTotal = ceil($total / $pSize);


        if ($currentPage < 1) $currentPage = 1;
        if ($currentPage > $pageTotal) $currentPage = $pageTotal;

        $this->pageTotal = $pageTotal;

        if ($pageTotal == 0) {
            $this->controller->assign('pageBar.' . $var, "");
            return false;
        }
        $this->firstRow = ($currentPage - 1) * $pSize;


        $pageDescribe = sprintf('<div class="page-describe">共%d条</div>', $total);


        $pagePrev = sprintf('<div class="page-prev%s" onclick="jumpToPage(this,\'%s\',%d)">上一页</div>',
            $currentPage <= 1 ? " page-disabled" : "",
            $var,
            $currentPage - 1 > 0 ? $currentPage - 1 : 1
        );

        $pageNext = sprintf('<div class="page-next%s" onclick="jumpToPage(this,\'%s\',%d)">下一页</div>',
            $currentPage >= $pageTotal ? " page-disabled" : "",
            $var,
            $currentPage + 1 > $pageTotal ? $pageTotal : $currentPage + 1
        );

        $pageSize = '<div class="page-size">每页 <select onchange="changePageSize(this,\'' . $var . '\')">';
        foreach ($sizeList as $item) {
            $pageSize .= sprintf('<option value="%d"%s>%d</option>', $item, $item == $pSize ? ' selected="selected"' : "", $item);
        }
        $pageSize .= '</select> 条 </div>';


        $pageItemsSelectOptions = "";
        for ($i = 1; $i <= $pageTotal; $i++) {
            $pageItemsSelectOptions .= sprintf('<option value="%d"%s>%d/%d</option>', $i, $i == $currentPage ? ' selected="selected"' : "", $i, $pageTotal);
        }


        $pageItemsSelect = '<select class="page-item" onchange="jumpToPage(this,\'' . $var . '\')">' . $pageItemsSelectOptions . '</select>';

        $pageBar = '<div class="page-bar" data-url="' . Q()->getRequestUri() . '">' . $pageDescribe . $pageItemsSelect . $pagePrev . $pageNext . $pageSize . "</div>";
        $this->controller->assign('pageBar.' . $var, $pageBar);
        return true;
    }

}
