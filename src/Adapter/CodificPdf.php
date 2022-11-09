<?php
declare(strict_types=1);


namespace App\Adapter;

class CodificPdf extends \TCPDF
{

    /**
     * @var callable
     */
    private $headerFunction;
    /**
     * @var callable
     */
    private $footerFunction;

    public function setHeaderFunction(callable $function)
    {
        $this->headerFunction = $function;
    }

    public function setFooterFunction(callable $function)
    {
        $this->footerFunction = $function;
    }

    public function Header()
    {
        if (is_callable($this->headerFunction)) {
            $func = $this->headerFunction;
            $func($this);
        }
    }

    public function Footer()
    {
        if (is_callable($this->footerFunction)) {
            $func = $this->footerFunction;
            $func($this);
        }
    }

    /**
     * Current page orientation (P = Portrait, L = Landscape).
     */
    public function getPageOrientation()
    {
        return $this->CurOrientation;
    }

    /**
     * Language templates.
     */
    public function getL()
    {
        return $this->l;
    }

    /**
     * Current width of page in user unit.
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Array that contains the number of pages in each page group.
     */
    public function getPageGroups()
    {
        return $this->pagegroups;
    }

    /**
     * Boolean flag true when we are on TOC (Table Of Content) page.
     */
    public function isTocPage()
    {
        return $this->tocpage;
    }

    public function getPageNumber(): string
    {
        $w_page = isset($this->getL()['w_page']) ? $this->getL()['w_page'].' ' : '';
        if (empty($this->getPageGroups())) {
            $pageNumTxt = $w_page.$this->getAliasNumPage();
        } else {
            $pageNumTxt = $w_page.$this->getPageNumGroupAlias();
        }
        return $pageNumTxt;
    }

}