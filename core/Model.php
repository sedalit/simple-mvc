<?php

namespace PHPFramework;

use PHPFramework\Utils\Text;

abstract class Model implements \ArrayAccess {
    protected array $fillable = [];
    protected array $attributes = [];

    protected abstract function tableName() : string;

    protected abstract function primaryKeyName() : string;

    public function __construct()
    {
        $this->loadData();
    }

    public function loadData() : void
    {
        $data = Request::data();
        
        foreach ($this->fillable as $value) {
            $fieldValue = isset($data[$value]) ? $data[$value] : '';
            $this->attributes[$value] = Text::trim($fieldValue);
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

        $id = db()->getInsertedId();
        $this->setAttribute($this->primaryKeyName(), $id);
        
        return $id;
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

    public function offsetSet(mixed $offset, mixed $value) : void 
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetExists(mixed $offset) : bool 
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset(mixed $offset) : void 
    {
        unset($this->attributes[$offset]);
    }

    public function offsetGet($offset) : mixed 
    {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }

    public function __get(string $name) : mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value) : void 
    {
        if (in_array($name, $this->fillable, true)) {
            $this->attributes[$name] = $value;
        }
    }

    public function __unset(string $name) : void
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }
}