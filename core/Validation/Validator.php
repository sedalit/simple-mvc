<?php

namespace PHPFramework\Validation;

use PHPFramework\Validation\Rules\EmailRule;
use PHPFramework\Validation\Rules\ExtensionRule;
use PHPFramework\Validation\Rules\FileRule;
use PHPFramework\Validation\Rules\FileSizeRule;
use PHPFramework\Validation\Rules\MaxRule;
use PHPFramework\Validation\ValidationRuleInterface;
use PHPFramework\Validation\Rules\RequiredRule;
use PHPFramework\Validation\Rules\MinRule;
use PHPFramework\Validation\Rules\UniqueRule;

class Validator {
    protected array $errors = [];
    protected array $rules = [
        'required' => RequiredRule::class,
        'min' => MinRule::class,
        'max' => MaxRule::class,
        'email' => EmailRule::class,
        'unique' => UniqueRule::class,
        'extension' => ExtensionRule::class,
        'fileSize' => FileSizeRule::class,
        'file' => FileRule::class,
    ];

    public function __construct(string $customRulesPath = VALIDATION_RULES)
    {
        $this->loadCustomRules($customRulesPath);
    }

    public function validate(array $data, array $rules) : bool
    {
        $this->errors = [];

        
        foreach ($rules as $field => $ruleString) {
            $ruleItems = explode('|', $ruleString);
            
            foreach ($ruleItems as $ruleItem) {
                [$name, $param] = array_pad(explode(':', $ruleItem, 2), 2, null);

                if (!isset($this->rules[$name])) {
                    continue;
                }

                $class = $this->rules[$name];

                /** @var ValidationRuleInterface $rule */
                $rule = new $class($field, $data[$field] ?? '', $param ? [$param] : []);
                if (!$rule->passes()) {
                    $this->errors[$field][] = $rule->message();
                }
            }
        }

        return !$this->hasErrors();
    }

    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }

    protected function loadCustomRules(string $path) : void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*.php') as $file) {
            require_once $file;

            $className = pathinfo($file, PATHINFO_FILENAME);
            $rule = "App\\Validation\\Rules\\{$className}";

            if (class_exists($rule) && in_array(ValidationRuleInterface::class, class_implements($rule))) {
                $key = $rule::key();

                if (!$key) {
                    $key = strtolower(preg_replace('/Rule$/', '', $className));
                }
                
                $this->rules[$key] = $rule;
            }
        }
    }

    public function errors() : array
    {
        return $this->errors;
    }
}