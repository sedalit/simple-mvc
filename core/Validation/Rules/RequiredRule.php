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
        if (is_array($this->value)) {
            return count(array_filter($this->value, 'strlen')) > 0;
        }
        
        return !empty(trim((string)$this->value));
    }
}