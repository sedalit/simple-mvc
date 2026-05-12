<?php

namespace PHPFramework;

class QueryBuilder {
    protected Database $db;
    protected string $table = '';
    protected array $select = ['*'];
    protected array $where = [];
    protected array $joins = [];
    protected array $orderBy = [];
    protected array $groupBy = [];
    protected array $having = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected array $whereBindings = [];
    protected array $havingBindings = [];
    protected string $whereOperator = 'AND';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function table(string $table) : self
    {
        $this->assertValidIdentifier($table);
        $this->table = $table;
        return $this;
    }

    public function select(array|string $columns = ['*']) : self
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function where(string $column, mixed $operator, mixed $value = null) : self
    {
        if ($value === null && func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        $this->whereBindings[] = $value;
        $this->whereOperator = 'AND';

        return $this;
    }

    public function orWhere(string $column, mixed $operator, mixed $value = null) : self
    {
        $this->whereOperator = 'OR';
        return $this->where($column, $operator, $value);
    }

    public function whereIn(string $column, array $values) : self
    {
        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => 'IN',
            'value' => $values
        ];

        $this->whereBindings = array_merge($this->whereBindings, $values);
        $this->whereOperator = 'AND';

        return $this;
    }

    public function whereNotIn(string $column, array $values) : self
    {
        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => 'NOT IN',
            'value' => $values
        ];

        $this->whereBindings = array_merge($this->whereBindings, $values);
        $this->whereOperator = 'AND';

        return $this;
    }

    public function whereBetween(string $column, array $values) : self
    {
        if (count($values) !== 2) {
            throw new \InvalidArgumentException('whereBetween requires exactly 2 values');
        }

        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => 'BETWEEN',
            'value' => $values
        ];

        $this->whereBindings = array_merge($this->whereBindings, $values);
        $this->whereOperator = 'AND';

        return $this;
    }

    public function whereNull(string $column) : self
    {
        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => 'IS NULL',
            'value' => null
        ];

        $this->whereOperator = 'AND';
        return $this;
    }

    public function whereNotNull(string $column) : self
    {
        $this->assertValidIdentifier($column);
        $this->where[] = [
            'type' => $this->whereOperator,
            'column' => $column,
            'operator' => 'IS NOT NULL',
            'value' => null
        ];

        $this->whereOperator = 'AND';
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER') : self
    {
        $this->assertValidIdentifier($table);
        $this->assertValidIdentifier($first);
        $this->assertValidIdentifier($second);

        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];

        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second) : self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second) : self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function orderBy(string $column, string $direction = 'ASC') : self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Order direction must be ASC or DESC');
        }

        $this->assertValidIdentifier($column);
        $this->orderBy[] = $this->quoteIdentifier($column) . ' ' . $direction;
        return $this;
    }

    public function groupBy(string|array $columns) : self
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        foreach ($columns as $c) {
            $this->assertValidIdentifier($c);
        }
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    public function having(string $column, mixed $operator, mixed $value = null) : self
    {
        if ($value === null && func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->assertValidIdentifier($column);
        $this->having[] = $this->quoteIdentifier($column) . " {$operator} ?";
        $this->havingBindings[] = $value;

        return $this;
    }

    public function limit(int $limit) : self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset) : self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get() : array
    {
        $query = $this->buildSelectQuery();
        return $this->db->query($query, $this->getMergedBindings())->getAll();
    }

    public function first() : mixed
    {
        $clone = clone $this;
        $clone->limit = 1;
        $query = $clone->buildSelectQuery();
        return $this->db->query($query, $clone->getMergedBindings())->getOne();
    }

    public function find(mixed $id, string $column = 'id') : mixed
    {
        return $this->where($column, '=', $id)->first();
    }

    public function count(string $column = '*') : int
    {
        $clone = clone $this;
        $clone->select = ["COUNT({$this->quoteAggregateArgument($column)}) AS aggregate"];

        $query = $clone->buildSelectQuery();
        $result = $this->db->query($query, $clone->getMergedBindings())->getOne();

        return (int) ($result['aggregate'] ?? $result['count'] ?? 0);
    }

    public function sum(string $column) : float
    {
        return (float) $this->aggregate('SUM', $column);
    }

    public function avg(string $column) : float
    {
        return (float) $this->aggregate('AVG', $column);
    }

    public function min(string $column) : mixed
    {
        return $this->aggregate('MIN', $column);
    }

    public function max(string $column) : mixed
    {
        return $this->aggregate('MAX', $column);
    }

    public function exists() : bool
    {
        $clone = clone $this;
        $clone->select = ['1'];
        $clone->limit = 1;
        $query = $clone->buildSelectQuery();
        $result = $this->db->query($query, $clone->getMergedBindings())->getOne();
        return !empty($result);
    }

    public function insert(array $data) : bool|string
    {
        $columns = array_keys($data);
        foreach ($columns as $c) {
            $this->assertValidIdentifier($c);
        }
        $values = array_values($data);

        $columnsStr = implode(', ', array_map(fn($col) => $this->quoteIdentifier($col), $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $query = "INSERT INTO {$this->quoteIdentifier($this->table)} ({$columnsStr}) VALUES ({$placeholders})";
        $this->db->query($query, $values);

        return $this->db->getInsertedId();
    }

    public function update(array $data) : int
    {
        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $this->assertValidIdentifier($column);
            $sets[] = $this->quoteIdentifier($column) . ' = ?';
            $values[] = $value;
        }

        $query = "UPDATE {$this->quoteIdentifier($this->table)} SET " . implode(', ', $sets);

        if (!empty($this->where)) {
            $query .= ' WHERE ' . $this->buildWhereClause();
            $values = array_merge($values, $this->whereBindings);
        }

        $this->db->query($query, $values);
        return $this->db->rowCount();
    }

    public function delete() : int
    {
        $query = "DELETE FROM {$this->quoteIdentifier($this->table)}";

        if (!empty($this->where)) {
            $query .= ' WHERE ' . $this->buildWhereClause();
        }

        $this->db->query($query, $this->whereBindings);
        return $this->db->rowCount();
    }

    public function toSql() : string
    {
        return $this->buildSelectQuery();
    }

    public function getBindings() : array
    {
        return $this->getMergedBindings();
    }

    protected function getMergedBindings() : array
    {
        return array_merge($this->whereBindings, $this->havingBindings);
    }

    protected function buildSelectQuery() : string
    {
        $query = 'SELECT ' . implode(', ', array_map(
            fn($col) => $this->quoteSelectColumn($col),
            $this->select
        ));

        $query .= ' FROM ' . $this->quoteIdentifier($this->table);

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $query .= " {$join['type']} JOIN "
                    . $this->quoteIdentifier($join['table'])
                    . ' ON ' . $this->quoteIdentifier($join['first'])
                    . " {$join['operator']} "
                    . $this->quoteIdentifier($join['second']);
            }
        }

        if (!empty($this->where)) {
            $query .= ' WHERE ' . $this->buildWhereClause();
        }

        if (!empty($this->groupBy)) {
            $query .= ' GROUP BY ' . implode(', ', array_map(
                fn($col) => $this->quoteIdentifier($col),
                $this->groupBy
            ));
        }

        if (!empty($this->having)) {
            $query .= ' HAVING ' . implode(' AND ', $this->having);
        }

        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $query .= " OFFSET {$this->offset}";
        }

        return $query;
    }

    protected function buildWhereClause() : string
    {
        $conditions = [];

        foreach ($this->where as $index => $condition) {
            $prefix = $index === 0 ? '' : " {$condition['type']} ";
            $column = $this->quoteIdentifier($condition['column']);

            if ($condition['operator'] === 'IN' || $condition['operator'] === 'NOT IN') {
                $placeholders = implode(', ', array_fill(0, count($condition['value']), '?'));
                $conditions[] = $prefix . "{$column} {$condition['operator']} ({$placeholders})";
            } elseif ($condition['operator'] === 'BETWEEN') {
                $conditions[] = $prefix . "{$column} BETWEEN ? AND ?";
            } elseif ($condition['operator'] === 'IS NULL' || $condition['operator'] === 'IS NOT NULL') {
                $conditions[] = $prefix . "{$column} {$condition['operator']}";
            } else {
                $conditions[] = $prefix . "{$column} {$condition['operator']} ?";
            }
        }

        return implode('', $conditions);
    }

    protected function aggregate(string $function, string $column) : mixed
    {
        $clone = clone $this;
        $clone->select = ["{$function}({$this->quoteAggregateArgument($column)}) AS aggregate"];

        $query = $clone->buildSelectQuery();
        $result = $this->db->query($query, $clone->getMergedBindings())->getOne();

        return $result['aggregate'] ?? 0;
    }

    protected function quoteSelectColumn(string $col) : string
    {
        if ($col === '*') {
            return $col;
        }
        if (str_contains($col, '(')) {
            return $col;
        }
        if (preg_match('/^\d+$/', $col)) {
            return $col;
        }
        return $this->quoteIdentifier($col);
    }

    protected function quoteIdentifier(string $name) : string
    {
        $name = trim($name);

        if (preg_match('/^(.+?)\s+(?:AS\s+)?([A-Za-z_][A-Za-z0-9_]*)$/i', $name, $m)) {
            return $this->quoteIdentifier($m[1]) . ' AS `' . $m[2] . '`';
        }

        $parts = explode('.', $name);
        foreach ($parts as $p) {
            if ($p !== '*' && !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $p)) {
                throw new \InvalidArgumentException("Invalid identifier: {$name}");
            }
        }

        return implode('.', array_map(
            fn($p) => $p === '*' ? '*' : "`{$p}`",
            $parts
        ));
    }

    protected function quoteAggregateArgument(string $column) : string
    {
        if ($column === '*') {
            return '*';
        }
        return $this->quoteIdentifier($column);
    }

    protected function assertValidIdentifier(string $name) : void
    {
        $this->quoteIdentifier($name);
    }
}
