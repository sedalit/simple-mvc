<?php

namespace PHPFramework;

use PDOStatement;

class Database {
    protected \PDO $connection;
    protected \PDOStatement $statement;
    protected array $queries = [];

    public function __construct()
    {
        $dsn = "mysql:host=" . DB['host'] . ";dbname=" . DB['dbname'] . ";charset=" . DB['charset'];
        try {
            $this->connection = new \PDO($dsn, DB['username'], DB['password'], DB['options']);
        } catch (\PDOException $e) {
            abort($e->getMessage(), 500);
        }
    }

    public function query(string $query, array $params = []) : Database
    {
        try {
            $this->statement = $this->connection->prepare($query);
            $this->statement->execute($params);

            if (DEBUG) {
                ob_start();
                $this->statement->debugDumpParams();
                $this->queries[] = ob_get_clean();
            }
        } catch (\PDOException $e) {
            abort($e->getMessage(), 500);
        }

        return $this;
    }

    public function getOne() : mixed
    {
        return $this->statement->fetch();
    }

    public function getAll() : mixed
    {
        return $this->statement->fetchAll();
    }

    public function getColumn() : mixed
    {
        return $this->statement->fetchColumn();
    }

    public function findAll(string $table) : array
    {
        return $this->tryExecute("SELECT * FROM {$table}", $table)->fetchAll();
    }

    public function findOne(string $table, int $id) : array
    {
        return $this->tryExecute("SELECT * FROM {$table} WHERE id = ? LIMIT 1", $table, [$id])->fetch();
    }

    public function findOrFail(string $table, int $id) : mixed
    {
        $result = $this->findOne($table, $id);
        
        if (!$result) {
            abort();
        }

        return $result;
    }

    public function getInsertedId() : bool|string
    {
        return $this->connection->lastInsertId();
    }

    public function rowCount() : int
    {
        return $this->statement->rowCount();
    }

    public function getQueries() : array
    {
        $result = [];
        foreach ($this->queries as $key => $value) {
            $line = strtok($value, PHP_EOL);

            while ($line !== false) {
                if (str_contains($line, 'SQL:') || str_contains($line, 'Sent SQL:')) {
                    $result[$key][] = $line;
                }
                $line = strtok(PHP_EOL);
            }
        }
        return $result;
    }

    public function count(string $tableName) : int
    {
        $this->query("SELECT COUNT(*) FROM {$tableName}");
        return $this->getColumn();
    }

    protected function tryExecute(string $query, string $tableName, array $parameters = []) : ?PDOStatement
    {
        $tablesWhiteList = [];
        if (defined('TABLES_WHITELIST')) {
            $tablesWhiteList = TABLES_WHITELIST ?? [];
        }

        if (count($tablesWhiteList) == 0 || in_array($tableName, $tablesWhiteList)) {
            return $this->query($query, $parameters)->statement;
        }

        abort('Access denied', 403);
        return null;
    }
}