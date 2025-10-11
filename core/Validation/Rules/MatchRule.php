<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class MatchRule extends ValidationRule {
    protected string $message = 'The :fieldname: doesnt match :rule:';

    public static function key(): ?string
    {
        return 'match';
    }

    public function passes() : bool
    {
        return $this->items[$this->params[0]] === $this->value;
    }
}