<?php

namespace PHPFramework;

abstract class Model {
    protected array $fillable = [];
    protected array $attributes = [];

    protected abstract function tableName() : string;

    protected abstract function primaryKeyName() : string;

    public function loadData() : void
    {
        $data = request()->getData();
        foreach ($this->fillable as $value) {
            if (isset($data[$value])) {
                $this->attributes[$value] = $data[$value];
            } else {
                $this->attributes[$value] = '';
            }
        }
    }

    public function attributes() : array
    {
        return $this->attributes;
    }

    public function setAttribute(string $key, mixed $value) : void
    {
        $this->attributes[$key] = $value;
    }

    public function save() : bool|string
    {
        $fieldsKeys = array_keys($this->attributes);
        $fields = array_map(function($field) {
            return "`{$field}`";
        }, $fieldsKeys);
        $fields = implode(',', $fields);

        $valuesPlaceholders = array_map(function($field) {
            return ":{$field}";
        }, $fieldsKeys);
        $valuesPlaceholders = implode(',', $valuesPlaceholders);
        $query = "INSERT INTO {$this->tableName()} ($fields) VALUES ($valuesPlaceholders)";
        db()->query($query, $this->attributes);

        return db()->getInsertedId();
    }

    public function update() : int|bool
    {
        if (!isset($this->attributes[$this->primaryKeyName()])) {
            return false;
        }

        $primaryKeyValue = $this->attributes[$this->primaryKeyName()];
        $fields = '';

        foreach ($this->attributes as $key => $value) {
            if ($key === $this->primaryKeyName()) {
                continue;
            }

            $fields .= " `{$key}`=:{$key},";
        }

        $fields = rtrim($fields, ',');
        $query = "UPDATE {$this->tableName()} SET {$fields} WHERE `{$this->primaryKeyName()}`=:{$this->primaryKeyName()}";
        db()->query($query, $this->attributes);
        return db()->rowCount();
    }

    public function delete(mixed $id) : int
    {
        db()->query("DELETE FROM {$this->tableName()} WHERE `{$this->primaryKeyName()}` = ?", [$id]);
        return db()->rowCount();
    }
}