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

        $result = db()->query("SELECT {$this->field} FROM {$table} WHERE {$this->field} = ?", [$this->value])->getColumn();
        return !$result;
    }
}