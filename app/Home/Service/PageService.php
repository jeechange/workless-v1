<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Service;

use Home\Controller\CommonController;
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
        $pSize = in_array($pSize, $this->sizeList) ? intval($pSize) : 8;
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
        if ($currentPage > $pageTotal) $currentPage = $pageTotal;

        $this->pageTotal = $pageTotal;

        if ($pageTotal == 0) {
            $this->controller->assign('pageBar.' . $var, "");
            return false;
        }

        $this->firstRow = ($currentPage -1)*$pSize;


        $pageDescribe = sprintf('<div class="page-describe">共%d条</div>', $total);


        $pagePrev = sprintf('<div class="page-prev" onclick="jumpToPage(\'%s\',%d)">&lt;</div>',
            $var,
            $currentPage - 1 > 0 ? $currentPage - 1 : 1
        );

        $pageNext = sprintf('<div class="page-next" onclick="jumpToPage(\'%s\',%d)">&gt;</div>',
            $var,
            $currentPage + 1 > $pageTotal ? $pageTotal : $currentPage + 1
        );

        $pageSize = '<div class="page-size">每页 <select onchange="changePageSize(this,\'' . $var . '\')">';
        foreach ($sizeList as $item) {
            $pageSize .= sprintf('<option value="%d"%s>%d</option>', $item, $item == $pSize ? ' selected="selected"' : "", $item);
        }
        $pageSize .= '</select> 条';
        $pageItems = "";
        if ($pageTotal < 9) {
            for ($i = 1; $i <= $pageTotal; $i++) {
                if ($currentPage == $i)
                    $pageItems .= sprintf('<div class="page-item page-current">%d</div>', $i);
                else
                    $pageItems .= sprintf('<div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $i, $i);
            }
        } elseif ($currentPage < 5) {
            for ($i = 1; $i < 6; $i++) {
                if ($currentPage == $i)
                    $pageItems .= sprintf('<div class="page-item page-current">%d</div>', $i);
                else
                    $pageItems .= sprintf('<div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $i, $i);
            }
            $pageItems .= sprintf('<div class="page-item">....</div><div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $pageTotal, $pageTotal);

        } else {

            $pageItems .= sprintf('<div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, 1, 1);
            $pageItems .= '<div class="page-item">....</div>';
            if (($pageTotal - $currentPage) <= 3) {
                $start = $pageTotal - 4;
                for ($j = $start; $j <= $pageTotal; $j++) {
                    if ($currentPage == $j)
                        $pageItems .= sprintf('<div class="page-item page-current">%d</div>', $j);
                    else
                        $pageItems .= sprintf('<div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $j, $j);
                }
            } else {
                $start = $currentPage - 2;
                $end = $currentPage + 2;
                for ($j = $start; $j <= $end; $j++) {
                    if ($currentPage == $j)
                        $pageItems .= sprintf('<div class="page-item page-current">%d</div>', $j);
                    else
                        $pageItems .= sprintf('<div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $j, $j);
                }
                $pageItems .= sprintf('<div class="page-item">....</div><div class="page-item" onclick="jumpToPage(\'%s\',%d)">%d</div>', $var, $pageTotal, $pageTotal);
            }
        }
        $pageBar = '<div class="page-bar">' . $pageDescribe . $pagePrev . $pageItems . $pageNext . $pageSize . "</div>";
        $this->controller->assign('pageBar.' . $var, $pageBar);
        return true;
    }

}
