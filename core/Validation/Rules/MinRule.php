<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class MinRule extends ValidationRule {
    
    protected string $message = "The :fieldname: must be a minimum :min: characters length";

    public function __construct(string $field, mixed $value, array $params = [])
    {
        parent::__construct($field, $value, $params);
        $this->message = str_replace([':fieldname:', ':min:'], [self::FIELDNAME_PLACEHOLDER, $params[0] ?? 0], $this->message);
    }

    public static function key(): ?string
    {
        return 'min';
    }

    public function passes() : bool
    {
        return mb_strlen($this->value, ENCODING) >= ($this->params[0] ?? 0);
    }
}