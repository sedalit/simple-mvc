<?php

namespace PHPFramework;

use PHPFramework\Interfaces\MigrationInterface;

abstract class Migration implements MigrationInterface
{
    protected \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Выполнить SQL-запрос
     */
    protected function execute(string $sql): void
    {
        $this->pdo->exec($sql);
    }

    /**
     * Проверить, существует ли таблица
     */
    protected function tableExists(string $table): bool
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM `{$table}` LIMIT 1");
            return $result !== false;
        } catch (\PDOException) {
            return false;
        }
    }
}