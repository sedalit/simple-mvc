<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class EmailRule extends ValidationRule {
    protected string $message = "The :fieldname: must be a valid email address";

    public static function key(): ?string
    {
        return 'email';
    }

    public function passes() : bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL);
    }
}