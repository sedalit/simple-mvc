<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Services\Mail\Mail;
use PHPFramework\Services\Mail\MailService;

class MailServiceTest extends TestCase
{
    public function testCanCreateMailObject(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient@example.com']
        );
        
        $this->assertEquals('test@example.com', $mail->from);
        $this->assertEquals('Test Subject', $mail->subject);
        $this->assertEquals('Test Body', $mail->body);
        $this->assertEquals(['recipient@example.com'], $mail->to);
    }

    public function testCanCreateMailObjectWithAllParameters(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient@example.com'],
            cc: ['cc@example.com'],
            bcc: ['bcc@example.com'],
            attachments: ['/path/to/file.pdf']
        );
        
        $this->assertEquals('test@example.com', $mail->from);
        $this->assertEquals('Test Subject', $mail->subject);
        $this->assertEquals('Test Body', $mail->body);
        $this->assertEquals(['recipient@example.com'], $mail->to);
        $this->assertEquals(['cc@example.com'], $mail->cc);
        $this->assertEquals(['bcc@example.com'], $mail->bcc);
        $this->assertEquals(['/path/to/file.pdf'], $mail->attachments);
    }

    public function testCanCreateMailObjectWithEmptyParameters(): void
    {
        $mail = new Mail();
        
        $this->assertEquals('', $mail->from);
        $this->assertEquals('', $mail->subject);
        $this->assertEquals('', $mail->body);
        $this->assertEquals([], $mail->to);
        $this->assertEquals([], $mail->cc);
        $this->assertEquals([], $mail->bcc);
        $this->assertEquals([], $mail->attachments);
    }

    public function testCanCreateMailObjectWithMultipleRecipients(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient1@example.com', 'recipient2@example.com']
        );
        
        $this->assertEquals(['recipient1@example.com', 'recipient2@example.com'], $mail->to);
    }

    public function testCanCreateMailObjectWithMultipleCc(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient@example.com'],
            cc: ['cc1@example.com', 'cc2@example.com']
        );
        
        $this->assertEquals(['cc1@example.com', 'cc2@example.com'], $mail->cc);
    }

    public function testCanCreateMailObjectWithMultipleBcc(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient@example.com'],
            bcc: ['bcc1@example.com', 'bcc2@example.com']
        );
        
        $this->assertEquals(['bcc1@example.com', 'bcc2@example.com'], $mail->bcc);
    }

    public function testCanCreateMailObjectWithMultipleAttachments(): void
    {
        $mail = new Mail(
            from: 'test@example.com',
            subject: 'Test Subject',
            body: 'Test Body',
            to: ['recipient@example.com'],
            attachments: ['/path/to/file1.pdf', '/path/to/file2.jpg']
        );
        
        $this->assertEquals(['/path/to/file1.pdf', '/path/to/file2.jpg'], $mail->attachments);
    }

    public function testMailObjectPropertiesArePublic(): void
    {
        $mail = new Mail();
        
        $mail->from = 'test@example.com';
        $mail->subject = 'Test Subject';
        $mail->body = 'Test Body';
        $mail->to = ['recipient@example.com'];
        $mail->cc = ['cc@example.com'];
        $mail->bcc = ['bcc@example.com'];
        $mail->attachments = ['/path/to/file.pdf'];
        
        $this->assertEquals('test@example.com', $mail->from);
        $this->assertEquals('Test Subject', $mail->subject);
        $this->assertEquals('Test Body', $mail->body);
        $this->assertEquals(['recipient@example.com'], $mail->to);
        $this->assertEquals(['cc@example.com'], $mail->cc);
        $this->assertEquals(['bcc@example.com'], $mail->bcc);
        $this->assertEquals(['/path/to/file.pdf'], $mail->attachments);
    }

    public function testMailObjectWithSpecialCharacters(): void
    {
        $mail = new Mail(
            from: 'test+tag@example.com',
            subject: 'Test Subject with Special Characters: !@#$%^&*()',
            body: 'Test Body with Special Characters: !@#$%^&*()',
            to: ['recipient+tag@example.com']
        );
        
        $this->assertEquals('test+tag@example.com', $mail->from);
        $this->assertEquals('Test Subject with Special Characters: !@#$%^&*()', $mail->subject);
        $this->assertEquals('Test Body with Special Characters: !@#$%^&*()', $mail->body);
        $this->assertEquals(['recipient+tag@example.com'], $mail->to);
    }

    public function testMailObjectWithUnicode(): void
    {
        $mail = new Mail(
            from: 'тест@example.com',
            subject: 'Тестовая тема',
            body: 'Тестовое сообщение',
            to: ['получатель@example.com']
        );
        
        $this->assertEquals('тест@example.com', $mail->from);
        $this->assertEquals('Тестовая тема', $mail->subject);
        $this->assertEquals('Тестовое сообщение', $mail->body);
        $this->assertEquals(['получатель@example.com'], $mail->to);
    }

    public function testMailObjectWithLongContent(): void
    {
        $longSubject = str_repeat('Test Subject ', 100);
        $longBody = str_repeat('Test Body ', 1000);
        
        $mail = new Mail(
            from: 'test@example.com',
            subject: $longSubject,
            body: $longBody,
            to: ['recipient@example.com']
        );
        
        $this->assertEquals($longSubject, $mail->subject);
        $this->assertEquals($longBody, $mail->body);
    }
}