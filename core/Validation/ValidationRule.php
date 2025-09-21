<?php

namespace PHPFramework\Validation;

abstract class ValidationRule implements ValidationRuleInterface
{
    protected const FIELDNAME_PLACEHOLDER = ':fieldname:';
    protected string $message = 'Validation error';
    protected string $field;
    protected mixed $value;
    protected array $params;

    public function __construct(string $field, mixed $value, array $params = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->params = $params;
    }

    public function message(): string
    {
        return str_replace(self::FIELDNAME_PLACEHOLDER, $this->field, $this->message);
    }
}