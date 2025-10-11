<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class UniqueRule extends ValidationRule
{
    protected string $message = ":fieldname: is already taken";

    public static function key() : ?string
    {
        return 'unique';
    }

    public function passes(): bool
    {
        $table = $this->params[0];
        if (str_contains($this->params[0], ',')) {
            $explodedParams = explode(',', $this->params[0]);
            [$table, $dataField] = $explodedParams;

            return db()->query("SELECT {$this->field} FROM {$table} WHERE {$this->field} = ? AND {$dataField} != ?", [$this->value, $this->items[$dataField]])->getColumn();
        }
        
        $result = db()->query("SELECT {$this->field} FROM {$table} WHERE {$this->field} = ?", [$this->value])->getColumn();
        return !$result;
    }
}