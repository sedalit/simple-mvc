<?php

namespace PHPFramework;

use PDOStatement;

class Database {
    protected \PDO $connection;
    protected \PDOStatement $statement;

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
        } catch (\PDOException $e) {
            abort($e->getMessage(), 500);
        }

        return $this;
    }

    public function get() : array
    {
        return $this->statement->fetchAll();
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

    protected function tryExecute(string $query, string $tableName, array $parameters = []) : ?PDOStatement
    {
        $tablesWhiteList = TABLES_WHITELIST ?? [];

        if (count($tablesWhiteList) > 0 && in_array($tableName, $tablesWhiteList)) {
            return $this->query($query, $parameters)->statement;
        }

        abort('Access denied', 403);
        return null;
    }
}