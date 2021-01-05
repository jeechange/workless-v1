<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

/**
 * Description of Seo
 *
 * @author Administrator
 */
class Seo {

    private $title,$subTitle, $keywords, $description;
    private $defaultTitle,$defaultSubTitle, $defaultKeywords, $defaultDescription;

    /**
     *
     * @var SeoParseInterface
     */
    private $seoParse;

    public function setParse(SeoParseInterface $seoparse) {
        $this->seoParse = $seoparse;
    }

    public function getTitle() {
        if ($this->title)
            return $this->title;
        if ($this->seoParse && $this->seoParse->getTitle()) {
            $this->title = $this->seoParse->getTitle();
            return $this->title;
        }
        if (!$this->defaultTitle) {
            $this->defaultTitle = C("seo.title");
        }
        return $this->defaultTitle;
    }
    
    public function getSubTitle() {
        if ($this->subTitle)
            return $this->subTitle;
        if ($this->seoParse && $this->seoParse->getSubTitle()) {
            $this->subTitle = $this->seoParse->getSubTitle();
            return $this->subTitle;
        }
        if (!$this->defaultSubTitle) {
            $this->defaultSubTitle = C("seo.subTitle");
        }
        return $this->defaultSubTitle;
    }

    public function getKeywords() {
        if ($this->keywords)
            return $this->keywords;
        if ($this->seoParse && $this->seoParse->getKeywords()) {
            $this->keywords = $this->seoParse->getKeywords();
            return $this->keywords;
        }
        if (!$this->defaultKeywords) {
            $this->defaultKeywords = C("seo.keywords");
        }
        return $this->defaultKeywords;
    }

    public function getDescription() {
        if ($this->description)
            return $this->description;
        if ($this->seoParse && $this->seoParse->getKeywords()) {
            $this->description = $this->seoParse->getDescription();
            return $this->description;
        }
        if (!$this->defaultDescription) {
            $this->defaultDescription = C("seo.description");
        }
        return $this->defaultDescription;
    }

}
