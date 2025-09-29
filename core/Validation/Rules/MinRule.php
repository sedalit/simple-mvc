<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class MinRule extends ValidationRule {
    
    protected string $message = "The :fieldname: must be a minimum :rule: characters length";

    public static function key(): ?string
    {
        return 'min';
    }

    public function passes() : bool
    {
        return mb_strlen($this->value, ENCODING) >= ($this->params[0] ?? 0);
    }
}