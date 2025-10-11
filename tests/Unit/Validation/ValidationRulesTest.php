<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Validation\Rules\RequiredRule;
use PHPFramework\Validation\Rules\EmailRule;
use PHPFramework\Validation\Rules\MinRule;
use PHPFramework\Validation\Rules\MaxRule;
use PHPFramework\Validation\Rules\UniqueRule;
use PHPFramework\Validation\Rules\MatchRule;

class ValidationRulesTest extends TestCase
{
    public function testRequiredRulePasses(): void
    {
        $rule = new RequiredRule('name', 'John', []);
        $this->assertTrue($rule->passes());
    }

    public function testRequiredRuleFails(): void
    {
        $rule = new RequiredRule('name', '', []);
        $this->assertFalse($rule->passes());
        $this->assertStringContainsString('required', $rule->message());
    }

    public function testEmailRulePasses(): void
    {
        $rule = new EmailRule('email', 'test@example.com', []);
        $this->assertTrue($rule->passes());
    }

    public function testEmailRuleFails(): void
    {
        $rule = new EmailRule('email', 'invalid-email', []);
        $this->assertFalse($rule->passes());
        $this->assertStringContainsString('email', $rule->message());
    }

    public function testMinRulePasses(): void
    {
        $rule = new MinRule('password', 'password123', ['8']);
        $this->assertTrue($rule->passes());
    }

    public function testMinRuleFails(): void
    {
        $rule = new MinRule('password', '123', ['8']);
        $this->assertFalse($rule->passes());
        $this->assertStringContainsString('minimum', $rule->message());
    }

    public function testMaxRulePasses(): void
    {
        $rule = new MaxRule('name', 'John', ['10']);
        $this->assertTrue($rule->passes());
    }

    public function testMaxRuleFails(): void
    {
        $rule = new MaxRule('name', 'Very Long Name', ['5']);
        $this->assertFalse($rule->passes());
        $this->assertStringContainsString('maximum', $rule->message());
    }

    public function testMatchRulePasses(): void
    {
        $data = ['password' => 'password123', 'password_confirmation' => 'password123'];
        $rule = new MatchRule('password', 'password123', ['password_confirmation'], $data);
        $this->assertTrue($rule->passes());
    }

    public function testMatchRuleFails(): void
    {
        $data = ['password' => 'password123', 'password_confirmation' => 'different'];
        $rule = new MatchRule('password', 'password123', ['password_confirmation'], $data);
        $this->assertFalse($rule->passes());
        $this->assertStringContainsString('match', $rule->message());
    }

    public function testRulesHandleNullValues(): void
    {
        $rule = new RequiredRule('name', null, []);
        $this->assertFalse($rule->passes());
    }

    public function testRulesHandleEmptyArrays(): void
    {
        $rule = new RequiredRule('items', [], []);
        $this->assertFalse($rule->passes());
    }
}