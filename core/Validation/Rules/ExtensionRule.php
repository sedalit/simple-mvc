<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;
use PHPFramework\Utils\File;

class ExtensionRule extends ValidationRule
{
    protected string $message = "File :fieldname: has wrong extension. Allowed :allowed:.";

    protected array $allowed = [];

    public function __construct(string $field, mixed $value, array $params = [])
    {
        parent::__construct($field, $value, $params);
        $this->allowed = explode(',', $params[0]);
        $this->allowed = array_map('strtolower', $this->allowed);

        $this->message = str_replace([':fieldname:', ':allowed:'], [self::FIELDNAME_PLACEHOLDER, $params[0]], $this->message);
    }

    public static function key() : ?string
    {
        return 'extension';
    }

    public function passes(): bool
    {
        if (!$this->value['name'] && !$this->value['full_path']) {
            return true;
        }
        
        $name = File::getFileName($this->value);
        $extension = File::getExtension($name);
        $extension = strtolower($extension);

        return in_array($extension, $this->allowed);
    }


}