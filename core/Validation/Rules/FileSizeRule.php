<?php

namespace PHPFramework\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class FileSizeRule extends ValidationRule {
    protected const BYTE = 1024;
    protected const B = 'byte';
    protected const KB = 'kilobyte';
    protected const MB = 'megabyte';
    protected const GB = 'gigabyte';
    protected const SIZE_UNITS = [
        self::B => ['b'],
        self::KB => ['kb'],
        self::MB => ['mb'],
        self::GB => ['gb'],
    ];

    protected string $message = "File is too large. Max allowed size is :maxSize:.";

    protected string $targetUnit;
    protected int $maxSize;
    protected int $maxSizeInBytes;

    public function __construct(string $field, mixed $value, array $params = [])
    {
        parent::__construct($field, $value, $params);
        $this->maxSize = $this->parseMaxSize();
        $this->targetUnit = $this->parseUnit();
        $this->maxSizeInBytes = $this->getMaxSizeInBytes();

        $sizeToShow = "{$this->maxSize} {$this->targetUnit}";

        if ($this->maxSize > 1) {
            $sizeToShow .= 's';
        }

        $this->message = str_replace(':maxSize:', $sizeToShow, $this->message);
    }

    public static function key(): ?string
    {
        return 'fileSize';
    }

    public function passes() : bool
    {   
        // Обработка массива файлов
        if (is_array($this->value['size'])) {
            if (empty($this->value['size'][0])) {
                return true;
            }

            for ($i = 0; $i < count($this->value['size']); $i++) {
                if ($this->value['size'][$i] > $this->maxSizeInBytes) {
                    return false;
                }
            }

            return true;
        }

        // Обработка одного файла
        return $this->value['size'] <= $this->maxSizeInBytes;
    }

    protected function parseMaxSize() : int
    {
        $sizeStr = preg_replace('/[^0-9.]/', '', $this->params[0]);

        return (int)$sizeStr;
    }

    protected function parseUnit() : string
    {
        $rawUnit = preg_replace('/[^a-z]/i', '', strtolower($this->params[0] ?? ''));
        $rawUnit = trim($rawUnit);
        $rawUnit = rtrim($rawUnit, 's');

        foreach (self::SIZE_UNITS as $key => $value) {
            if (in_array($rawUnit, $value) || $rawUnit === $key) {
                return $key;
            }
        }

        return self::MB;
    }

    protected function getMaxSizeInBytes() : int
    {
        if ($this->targetUnit == self::B) {
            return $this->maxSize;
        }

        $indexes = array_flip(array_keys(self::SIZE_UNITS));
        $exponent = $indexes[$this->targetUnit]; 
        $multiplier = pow(self::BYTE, $exponent);

        return $this->maxSize * $multiplier;
    }
}