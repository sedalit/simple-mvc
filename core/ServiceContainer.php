<?php

namespace PHPFramework;

class ServiceContainer {
    protected array $definitions = [];
    protected array $singletons = [];

    public function setSingleton(string $id, object $factory) : void
    {
        $this->set($id, $factory, true);
    }

    public function setFactory(string $id, callable $factory) : void
    {
        $this->set($id, $factory);
    }

    protected function set(string $id, callable $factory, bool $isSingleton = false) : void
    {
        $this->definitions[$id] = ['factory' => $factory, 'singleton' => $isSingleton];
    }

    public function get(string $id) : mixed
    {
        if (!isset($this->definitions[$id])) {
            $explodedId = explode("\\",$id);
            $className = end($explodedId);
            $className = strtolower($className);

            if (!isset($this->definitions[$className])) {
                throw new \Exception("Service {$id} not found");
            }
            
            $id = $className;
        }

        $definition = $this->definitions[$id];

        if ($definition['singleton']) {
            if (!isset($this->singletons[$id])) {
                $this->singletons[$id] = ($definition['factory'])();
            }

            return $this->singletons[$id];
        }

        return ($definition['factory'])();
    }
}