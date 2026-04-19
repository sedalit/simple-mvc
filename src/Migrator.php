<?php

namespace PHPFramework;

use PHPFramework\Interfaces\MigrationInterface;

class Migrator
{
    protected const MIGRATIONS_TABLE = 'migrations';
    protected \PDO $pdo;
    protected string $migrationsPath;

    public function __construct(\PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = rtrim($migrationsPath, '/');
        $this->ensureMigrationsTable();
    }

    /**
     * Запустить все новые миграции
     */
    public function run(): array
    {
        $pending = $this->getPending();

        if (empty($pending)) {
            return [];
        }

        $batch = $this->getNextBatch();
        $ran = [];

        foreach ($pending as $file) {
            $migration = $this->resolve($file);
            $migration->up();
            $this->log($file, $batch);
            $ran[] = $file;
        }

        return $ran;
    }

    /**
     * Откатить последний батч миграций
     */
    public function rollback(): array
    {
        $lastBatch = $this->getLastBatch();

        if ($lastBatch === null) {
            return [];
        }

        $migrations = $this->getByBatch($lastBatch);
        $rolledBack = [];

        foreach (array_reverse($migrations) as $record) {
            $file = $record['migration'];
            $migration = $this->resolve($file);
            $migration->down();
            $this->removeLlog($file);
            $rolledBack[] = $file;
        }

        return $rolledBack;
    }

    /**
     * Откатить все миграции
     */
    public function reset(): array
    {
        $all = $this->getRan();
        $rolledBack = [];

        foreach (array_reverse($all) as $record) {
            $file = $record['migration'];
            $migration = $this->resolve($file);
            $migration->down();
            $this->removeLlog($file);
            $rolledBack[] = $file;
        }

        return $rolledBack;
    }

    /**
     * Получить статус всех миграций
     */
    public function status(): array
    {
        $ran = array_column($this->getRan(), null, 'migration');
        $files = $this->getFiles();
        $status = [];

        foreach ($files as $file) {
            $name = $this->filename($file);
            $status[] = [
                'migration' => $name,
                'ran'       => isset($ran[$name]),
                'batch'     => $ran[$name]['batch'] ?? null,
            ];
        }

        return $status;
    }

    /**
     * Получить список новых (не выполненных) миграций
     */
    protected function getPending(): array
    {
        $ran = array_column($this->getRan(), 'migration');
        $pending = [];

        foreach ($this->getFiles() as $file) {
            $name = $this->filename($file);
            if (!in_array($name, $ran, true)) {
                $pending[] = $name;
            }
        }

        return $pending;
    }

    /**
     * Получить все выполненные миграции из БД
     */
    protected function getRan(): array
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM ' . self::MIGRATIONS_TABLE . ' ORDER BY batch ASC, id ASC'
        );
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Получить миграции конкретного батча
     */
    protected function getByBatch(int $batch): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM ' . self::MIGRATIONS_TABLE . ' WHERE batch = ? ORDER BY id ASC'
        );
        $stmt->execute([$batch]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Получить номер последнего батча
     */
    protected function getLastBatch(): ?int
    {
        $stmt = $this->pdo->query(
            'SELECT MAX(batch) FROM ' . self::MIGRATIONS_TABLE
        );
        $result = $stmt->fetchColumn();
        return $result !== null ? (int)$result : null;
    }

    /**
     * Получить номер следующего батча
     */
    protected function getNextBatch(): int
    {
        $last = $this->getLastBatch();
        return $last === null ? 1 : $last + 1;
    }

    /**
     * Получить список файлов миграций (отсортированных по имени)
     */
    protected function getFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        return $files;
    }

    /**
     * Получить имя файла без расширения и пути
     */
    protected function filename(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    /**
     * Загрузить и создать объект миграции
     */
    protected function resolve(string $name): MigrationInterface
    {
        $file = $this->migrationsPath . '/' . $name . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Migration file not found: {$file}");
        }

        require_once $file;

        // Преобразуем имя файла в имя класса
        // Например: 2024_01_01_000000_create_users_table → CreateUsersTable
        $className = $this->filenameToClassName($name);

        if (!class_exists($className)) {
            throw new \RuntimeException("Migration class '{$className}' not found in {$file}");
        }

        $migration = new $className($this->pdo);

        if (!$migration instanceof MigrationInterface) {
            throw new \RuntimeException("Class '{$className}' must implement MigrationInterface");
        }

        return $migration;
    }

    /**
     * Преобразовать имя файла миграции в имя класса
     * 2024_01_01_000000_create_users_table → CreateUsersTable
     */
    public static function filenameToClassName(string $filename): string
    {
        // Убираем временной префикс: YYYY_MM_DD_HHMMSS_
        $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);
        // snake_case → PascalCase
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * Записать выполненную миграцию в таблицу
     */
    protected function log(string $name, int $batch): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . self::MIGRATIONS_TABLE . ' (migration, batch) VALUES (?, ?)'
        );
        $stmt->execute([$name, $batch]);
    }

    /**
     * Удалить запись о миграции из таблицы
     */
    protected function removeLlog(string $name): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ' . self::MIGRATIONS_TABLE . ' WHERE migration = ?'
        );
        $stmt->execute([$name]);
    }

    /**
     * Создать таблицу миграций, если она не существует
     */
    protected function ensureMigrationsTable(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS ' . self::MIGRATIONS_TABLE . ' (
                id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch   INT NOT NULL
            )
        ');
    }
}