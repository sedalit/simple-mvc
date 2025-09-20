<?php

namespace PHPFramework;

class View {
    protected string $layout;
    protected string $content = '';

    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    public function render(string $viewName, array $data = [], string $layout = '') : bool|string
    {
        $viewFile = VIEWS . "/{$viewName}.php";
        if (is_file($viewFile)) {
            extract($data);
            ob_start();
            require_once $viewFile;
            return ob_get_clean();
        } else {
            throw new \Exception("View file not found: {$viewFile}");
        }

    }
}