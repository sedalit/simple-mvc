<?php

namespace PHPFramework;

class Factory {
    protected string $class;
    protected array $arguments;

    public function __construct(string $class, array $arguments = [])
    {
        $this->class = $class;
        $this->arguments = $arguments;
    }

    public function __invoke(string $class = '', array $arguments = []) : object
    {
        if (empty($class)) $class = $this->class;
        if (empty($arguments)) $arguments = $this->arguments;

        return new $class(...$arguments);
    }
}