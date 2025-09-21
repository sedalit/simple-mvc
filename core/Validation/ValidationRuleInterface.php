<?php

namespace PHPFramework\Validation;

interface ValidationRuleInterface {
    /**
     * Уникальный ключ правила (например 'email'). Если вернуть null, валидатор определит ключ по имени файла
     * @return ?string
     */
    public static function key() : ?string;
    
    /**
     * Выполняет проверку значения.
     * @return bool
     */
    public function passes() : bool;

    /**
     * Возвращает сообщение об ошибке.
     * @return string
     */
    public function message() : string;
}