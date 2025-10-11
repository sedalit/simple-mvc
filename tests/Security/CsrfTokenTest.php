<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Security\CsrfToken;
use PHPFramework\Session;
use PHPFramework\Application;
use PHPFramework\Services\CoreService;

class CsrfTokenTest extends TestCase
{
    protected Session $session;
    protected Application $app;

    public function __construct()
    {
        parent::__construct();
        $this->app = new Application('/test', [
            CoreService::class,
        ]);
        $this->session = $this->app->session();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->session->forget('_csrf_token');
    }

    public function testCanGenerateToken(): void
    {
        $token = CsrfToken::get();
        
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
    }

    public function testTokenIsStoredInSession(): void
    {
        $token = CsrfToken::get();
        $sessionToken = $this->session->get('_csrf_token');
        
        $this->assertEquals($token, $sessionToken);
    }

    public function testTokenIsReusedFromSession(): void
    {
        $token1 = CsrfToken::get();
        $token2 = CsrfToken::get();
        
        $this->assertEquals($token1, $token2);
    }

    public function testCanValidateCorrectToken(): void
    {
        $token = CsrfToken::get();
        $isValid = CsrfToken::validate($token);
        
        $this->assertTrue($isValid);
    }

    public function testCanValidateIncorrectToken(): void
    {
        $token = CsrfToken::get();
        $isValid = CsrfToken::validate('incorrect_token');
        
        $this->assertFalse($isValid);
    }

    public function testCanValidateNullToken(): void
    {
        $isValid = CsrfToken::validate(null);
        
        $this->assertFalse($isValid);
    }

    public function testCanValidateEmptyToken(): void
    {
        $isValid = CsrfToken::validate('');
        
        $this->assertFalse($isValid);
    }

    public function testTokenIsRandom(): void
    {
        $this->session->forget('_csrf_token');
        
        $token1 = CsrfToken::get();
        
        // Очищаем сессию снова
        $this->session->forget('_csrf_token');
        
        $token2 = CsrfToken::get();
        
        $this->assertNotEquals($token1, $token2);
    }

    public function testTokenContainsOnlyHexCharacters(): void
    {
        $token = CsrfToken::get();
        
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
    }

    public function testTokenLengthIsCorrect(): void
    {
        $token = CsrfToken::get();
        
        $this->assertEquals(64, strlen($token));
    }

    public function testValidateWithDifferentTokens(): void
    {
        $token1 = CsrfToken::get();
        
        $this->session->forget('_csrf_token');
        $token2 = CsrfToken::get();
        
        $this->assertFalse(CsrfToken::validate($token1));
        $this->assertTrue(CsrfToken::validate($token2));
    }

    public function testTokenGenerationWithExistingSession(): void
    {
        $this->session->set('_csrf_token', 'existing_token');
        
        $token = CsrfToken::get();
        $this->assertEquals('existing_token', $token);
    }

    public function testTokenGenerationWithoutSession(): void
    {
        $this->session->forget('_csrf_token');
        
        $token = CsrfToken::get();
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function testValidateWithTimingAttack(): void
    {
        $token = CsrfToken::get();
        $start = microtime(true);
        
        CsrfToken::validate('incorrect_token');
        
        $end = microtime(true);
        $time = $end - $start;
        
        $this->assertLessThan(0.1, $time);
    }

    public function testConstantsAreCorrect(): void
    {
        $this->assertEquals('_csrf', CsrfToken::INPUT_FIELD);
        $this->assertEquals('X-CSRF-TOKEN', CsrfToken::HEADER_NAME);
    }

    public function testTokenIsSecure(): void
    {
        $token = CsrfToken::get();
        
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
        
        $this->assertGreaterThanOrEqual(32, strlen($token));
    }
}