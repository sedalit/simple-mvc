<?php

namespace PHPFramework;

abstract class Model {
    protected array $fillable = [];
    protected array $attributes = [];

    protected abstract function tableName() : string;

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

    public function save()
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
}