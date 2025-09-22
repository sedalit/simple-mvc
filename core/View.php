<?php

namespace PHPFramework;

class View {
    protected string $layout;
    protected string $content = '';

    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    public function render(string $viewName, array $data = [], mixed $layout = '') : bool|string
    {
        $viewFile = VIEWS . "/{$viewName}.php";
        $this->content = $this->renderFile($viewFile, $data);

        if ($layout === false) {
            return $this->content;
        }

        $layoutFileName = $layout ?: $this->layout;
        $layoutFile = VIEWS . "/layouts/{$layoutFileName}.php";

        return $this->renderFile($layoutFile, $data);
    }

    public function renderPartial(string $viewName, array $data = []) : string
    {
        $viewFile = VIEWS . "/{$viewName}.php";
        if (is_file($viewFile)) {
            extract($data);
            require $viewFile;
            return "";
        } else {
            return "File {$viewFile} not found";
        }
    }

    private function renderFile(string $fileName, array $data = []) : bool|string|View
    {
        if (is_file($fileName)) {
            extract($data);
            ob_start();
            require_once $fileName;
            return ob_get_clean();
        } else {
            return abort('Internal server error', 500);
        }
    }
}