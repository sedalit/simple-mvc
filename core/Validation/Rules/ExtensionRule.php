<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;
use PHPFramework\Utils\File;

class ExtensionRule extends ValidationRule
{
    protected string $message = "File :fieldname: has wrong extension. Allowed :rule:.";

    protected array $allowed = [];

    public function __construct(string $field, mixed $value, array $params = [])
    {
        parent::__construct($field, $value, $params);
        $this->allowed = explode(',', $params[0]);
        $this->allowed = array_map('strtolower', $this->allowed);

        $this->message = str_replace([':fieldname:', ':rule:'], [self::FIELDNAME_PLACEHOLDER, $params[0]], $this->message);
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

        // Обработка массива файлов
        if (is_array($this->value['name'])) {
            if (empty($this->value['name'][0])) {
                return true;
            }

            for ($i = 0; $i < count($this->value['name']); $i++) {
                if (!$this->isAllowedExtension($this->value['name'][$i])) {
                    return false;
                }
            }

            return true;
        }

        // Обработка одного файла
        $name = $this->value['name'];

        return $this->isAllowedExtension($name);
    }

    private function isAllowedExtension($fileName) : bool
    {
        $extension = File::getExtension($fileName);
        $extension = strtolower($extension);

        return in_array($extension, $this->allowed);
    }
}