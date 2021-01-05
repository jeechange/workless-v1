<?php

namespace MobileConsoles\Controller;


use Admin\DModel\ContentCategoryDModel;
use Admin\DModel\ContentDModel;
use Admin\DModel\StaffDModel;

class ContentController extends CommonController {
    public function lists() {
        $contentCategoryDM = ContentCategoryDModel::getInstance();
        $contentCategory = $contentCategoryDM->name("cc")->where("cc.status = 1 and cc.sid = " . $this->sid)->order("cc.sort")->getArray();
        if (!$contentCategory) return $this->error("内容分类读取失败，无法展示内容文章！");
        $this->assign("category", $contentCategory);

        $types = Q()->get->get("types") ?: 'rec';
        $this->assign("active", $types);

        foreach ($contentCategory as $k => $v) {
            if ($types == $contentCategory[$k]['thumb']) return $this->categoryLists($contentCategory[$k]['id']);
        }
        $contentDM = ContentDModel::getInstance();

        $where = "c.status = 1 and c.sid = " . $this->sid;//条件未填写

        $lists = $contentDM->name('c')
            ->leftJoin("ContentCategory", "cc", "cc.id=c.categoryId")
            ->select('c,cc')
            ->where($where)
            ->order("c.addTime", "DESC")
            ->order("c.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function categoryLists($id) {
        $contentDM = ContentDModel::getInstance();

        $where = "c.status = 1 AND c.categoryId = '{$id}' and c.sid = " . $this->sid;//条件未填写
//        $where = "";//条件未填写
        $lists = $contentDM->name('c')
            ->where($where)
            ->order("c.addTime", "DESC")
            ->order("c.id", "DESC")
            ->getArray(true);
        $this->assign("lists", $lists);

        return $this->display("categoryLists");
    }


    public function add() {
        $contentCategoryDM = ContentCategoryDModel::getInstance();
        $contentCategory = $contentCategoryDM->name("cc")->where("cc.status = 1 and c.sid = " . $this->sid)->order("cc.sort")->getArray();
        $staffDM = StaffDModel::getInstance();

        if (Q()->isGet()) {
            $this->assign("category", $contentCategory);
            $executors = $staffDM->workerList($this->sid, "auditor");
            $this->assign("executors", $executors);
            return $this->display();
        }
        $post = Q()->post->all();
        if (!$contentCategory) return $this->error("请先添加内容分类！");
        $contentDM = ContentDModel::getInstance();
        $post['sid'] = $this->sid;
        $post['issueId'] = $this->getUser('id');
        $post["auditor"] = join("-", $post["auditor"]);
        $post["addTime"] = nowTime();
        $post["code"] = $this->buildCode("", 2, 6);
        $content = $contentDM->newEntity();

        $contentDM->create($post, $content);
        $contentDM->add($content)->flush();
        return $this->success("添加成功");
    }

    public function buildCode($prefix = "", $letter = 2, $integer = 6) {
        return $prefix . rand_string($letter, 2) . rand_string($integer, 1);
    }
}