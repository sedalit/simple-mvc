<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class MaxRule extends ValidationRule {
    protected string $message = "The :fieldname: must be a maximum :max: characters length";

    public function __construct(string $field, mixed $value, array $params = [])
    {
        parent::__construct($field, $value, $params);
        $this->message = str_replace([':fieldname:', ':max:'], [self::FIELDNAME_PLACEHOLDER, $params[0] ?? 0], $this->message);
    }

    public static function key(): ?string
    {
        return 'max';
    }

    public function passes() : bool
    {
        return mb_strlen($this->value, ENCODING) <= ($this->params[0] ?? 0);
    }
}