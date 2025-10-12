<?php

namespace PHPFramework\Validation;

abstract class ValidationRule implements ValidationRuleInterface
{
    protected const FIELDNAME_PLACEHOLDER = ':fieldname:';
    protected const RULE_PLACEHOLDER = ':rule:';

    protected string $message = 'Validation error';
    protected string $field;
    protected mixed $value;
    protected array $params;
    protected array|\ArrayAccess $items;

    public function __construct(string $field, mixed $value, array $params = [], array|\ArrayAccess $items = [])
    {
        $this->field = $field;
        $this->value = $value;
        $this->params = $params;
        $this->items = $items;
    }

    public function message(): string
    {
        $fieldName = ucfirst($this->field);
        $params = implode(', ', $this->params);
        return str_replace([self::FIELDNAME_PLACEHOLDER, self::RULE_PLACEHOLDER], [$fieldName, $params], $this->message);
    }
}