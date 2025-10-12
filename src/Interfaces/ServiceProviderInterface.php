<?php

namespace PHPFramework\Interfaces;

use PHPFramework\ServiceContainer;

interface ServiceProviderInterface {
    public function register(ServiceContainer $c) : void;
}