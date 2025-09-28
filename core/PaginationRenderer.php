<?php

namespace PHPFramework;

class PaginationRenderer {

    protected static function li(string $label, ?int $page, string $url, bool $isActive = false) : string
    {
        $class = 'page-item' . ($isActive ? ' active' : '');

        return "<li class='{$class}'><a class='page-link' href='{$url}'>{$label}</a></li>";
    }

    public static function render(Pagination $pagination) : string
    {
        $items = [];
        foreach ($pagination->getPages() as $page) {
            $items[] = self::li($page['label'], $page['page'], $page['link'], $page['active']);
        }

        return "<nav aria-label='Page navigation'><ul class='pagination'>" . implode('', $items) . "</ul></nav>";
    }
}