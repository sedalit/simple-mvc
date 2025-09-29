<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class MaxRule extends ValidationRule {
    protected string $message = "The :fieldname: must be a maximum :rule: characters length";

    public static function key(): ?string
    {
        return 'max';
    }

    public function passes() : bool
    {
        return mb_strlen($this->value, ENCODING) <= ($this->params[0] ?? 0);
    }
}