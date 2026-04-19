<?php

namespace PHPFramework\Interfaces;

interface MigrationInterface {
    /**
     * Применение миграции
     */
    public function up() : void;

    /**
     * Откат миграции
     */
    public function down() : void;
}