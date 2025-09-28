<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class FileRule extends ValidationRule {
    protected string $message = 'The :fieldname: is required';

    public static function key(): ?string
    {
        return 'file';
    }

    public function passes() : bool
    {
        $hasErrorField = isset($this->value['error']);

        if ($hasErrorField && is_array($this->value['error'])) {
            foreach ($this->value['error'] as $error) {
               if ($error !== 0) {
                return false;
               }
            }
            return true;
        }

        return !($hasErrorField && $this->value['error'] !== 0);
    }
}