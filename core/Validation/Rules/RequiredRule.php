<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class RequiredRule extends ValidationRule {
    protected string $message = 'The ' . self::FIELDNAME_PLACEHOLDER . ' is required';

    public static function key(): ?string
    {
        return 'required';
    }

    public function passes() : bool
    {
        return !empty(trim((string)$this->value));
    }
}