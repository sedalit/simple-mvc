<?php

namespace PHPFramework;

abstract class Model {

    protected array $fillable = [];
    protected array $attributes = [];

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
}