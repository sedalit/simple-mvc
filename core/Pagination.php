<?php

namespace PHPFramework;

use PHPFramework\Request;

class Pagination {
    protected const DEFAULT_MIDDLE_SIZE = 3;

    protected int $pagesCount = 1;
    protected int $currentPage = 1;
    protected int $middleSize = self::DEFAULT_MIDDLE_SIZE;
    protected int $maxPages = 7;
    protected int $perPage = 1;
    protected int $total = 1;
    protected string $uri = '';
    protected int $page = 1;

    public function __construct(int $page, int $perPage, int $total)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->total = $total;
        $this->pagesCount = $this->getPagesCount();
        $this->currentPage = $this->getCurrentPage();
        $this->uri = $this->getUri();
        $this->middleSize = $this->getMiddleSize();
    }

    public function getPagesCount() : int
    {
        return (int)ceil($this->total / $this->perPage);
    }

    public function getCurrentPage() : int
    {
        if ($this->page < 1 || $this->page > $this->pagesCount) {
            $this->page = 1;
        }

        return $this->page;
    }

    protected function getUri() : string
    {
        $url = explode('?', Request::requestUrl());
        $uri = $url[0];

        if (isset($url[1]) && !in_array($url[1], ['', '&'])) {
            $uri .= '?';
            $params = explode('&', $url[1]);

            foreach ($params as $param) {
                if (!str_contains($param, 'page=')) {
                    $uri .= "{$param}&";
                }
            }
        }

        return $uri;
    }

    public function getMiddleSize() : int
    {
        return ($this->pagesCount <= $this->maxPages) ? $this->pagesCount : ($this->middleSize ?? self::DEFAULT_MIDDLE_SIZE);
    }

    public function getOffset() : int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function getPages() : array
    {
        $pages = [];

        if ($this->currentPage > $this->middleSize + 1) {
            $page = 1;
            $pages[] = ['label' => '&laquo;', 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
        }

        if ($this->currentPage > 1) {
            $page = $this->currentPage - 1;
            $pages[] = ['label' => '&lt;', 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
        }

        for ($i = $this->middleSize; $i > 0; $i--) {
            $page = $this->currentPage - $i;
            if ($page > 0) {
                $pages[] = ['label' => (string)$page, 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
            }
        }

        $pages[] = ['label' => (string)$this->currentPage, 'page' => $this->currentPage, 'link' => $this->getLink($this->currentPage), 'active' => true];
        
        for ($i = 1; $i <= $this->middleSize; $i++) {
            $page = $this->currentPage + $i;
            if ($page <= $this->pagesCount) {
                $pages[] = ['label' => (string)$page, 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
            }
        }

        if ($this->currentPage < $this->pagesCount - 1) {
            $page = $this->currentPage + 1;
            $pages[] = ['label' => '&gt;', 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
        }

        if ($this->currentPage < ($this->pagesCount - $this->middleSize)) {
            $page = $this->pagesCount;
            $pages[] = ['label' => '&raquo;', 'page' => $page, 'link' => $this->getLink($page), 'active' => false];
        }

        return $pages;
    }

    protected function getLink(int $page) : string
    {
        if ($page == 1) {
            return rtrim($this->uri, '?&');
        } else if (str_contains($this->uri, '&') || str_contains($this->uri, '?')) {
            return "{$this->uri}page={$page}";
        } else {
            return "{$this->uri}?page={$page}";
        }
    }

    public function setMiddleSize(int $size) : void
    {
        $this->middleSize = $size;
    }

    public function render() : string
    {
        return PaginationRenderer::render($this);
    }

    public function __tostring() : string
    {
        return $this->render();
    }
}