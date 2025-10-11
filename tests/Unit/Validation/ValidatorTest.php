<?php

namespace Tests\Unit\Validation;

use PHPUnit\Framework\TestCase;
use PHPFramework\Validation\Validator;

class ValidatorTest extends TestCase
{
    protected Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }

    public function testCanValidateRequiredField(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'required'];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testRequiredFieldValidationFails(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];

        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
        $this->assertTrue($this->validator->hasErrors());
        $this->assertArrayHasKey('name', $this->validator->errors());
    }

    public function testCanValidateEmail(): void
    {
        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'email'];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function testEmailValidationFails(): void
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'email'];

        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
        $this->assertArrayHasKey('email', $this->validator->errors());
    }

    public function testCanValidateMinLength(): void
    {
        $data = ['password' => '123456'];
        $rules = ['password' => 'min:6'];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function testMinLengthValidationFails(): void
    {
        $data = ['password' => '123'];
        $rules = ['password' => 'min:6'];

        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
        $this->assertArrayHasKey('password', $this->validator->errors());
    }

    public function testCanValidateMaxLength(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'max:10'];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function testMaxLengthValidationFails(): void
    {
        $data = ['name' => 'Very Long Name That Exceeds Limit'];
        $rules = ['name' => 'max:10'];

        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
        $this->assertArrayHasKey('name', $this->validator->errors());
    }

    public function testCanValidateMultipleRules(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function testMultipleRulesValidationFails(): void
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123'
        ];
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];

        $result = $this->validator->validate($data, $rules);
        $this->assertFalse($result);
        $this->assertArrayHasKey('name', $this->validator->errors());
        $this->assertArrayHasKey('email', $this->validator->errors());
        $this->assertArrayHasKey('password', $this->validator->errors());
    }

    public function testCanValidateWithArrayRules(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => ['required', 'min:3']];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }

    public function testClearsErrorsOnNewValidation(): void
    {
        // Первая валидация с ошибками
        $data = ['name' => ''];
        $rules = ['name' => 'required'];
        $this->validator->validate($data, $rules);
        $this->assertTrue($this->validator->hasErrors());

        // Вторая валидация без ошибок
        $data = ['name' => 'John'];
        $rules = ['name' => 'required'];
        $result = $this->validator->validate($data, $rules);
        
        $this->assertTrue($result);
        $this->assertFalse($this->validator->hasErrors());
    }

    public function testCanGetErrors(): void
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];
        $this->validator->validate($data, $rules);

        $errors = $this->validator->errors();
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testIgnoresUnknownRules(): void
    {
        $data = ['name' => 'John'];
        $rules = ['name' => 'unknown_rule'];

        $result = $this->validator->validate($data, $rules);
        $this->assertTrue($result);
    }
}