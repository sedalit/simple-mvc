<?php

namespace Tests\Utils;

use PHPUnit\Framework\TestCase;

use PHPFramework\Utils\Env;
use PHPFramework\Utils\Text;
use PHPFramework\Utils\File;

class UtilsTest extends TestCase {
    protected function setUp(): void
    {
        parent::setUp();
        file_put_contents('.env.test', 'TEST_VAR=test_value');
        Env::load('.env.test');
    }

    public function testEnvGet(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';
        $value = Env::get('TEST_VAR');
        
        $this->assertEquals('test_value', $value);
    }

    public function testEnvGetWithDefault(): void
    {
        $value = Env::get('NON_EXISTENT', 'default_value');
        $this->assertEquals('default_value', $value);
    }

    public function testEnvGetWithoutDefault(): void
    {
        $value = Env::get('NON_EXISTENT');
        $this->assertEquals('', $value);
    }

    public function testFileGetExtension(): void
    {
        $extension = File::getExtension('test.jpg');
        $this->assertEquals('jpg', $extension);
    }

    public function testFileGetExtensionWithPath(): void
    {
        $extension = File::getExtension('/path/to/file.pdf');
        $this->assertEquals('pdf', $extension);
    }

    public function testFileGetExtensionWithoutExtension(): void
    {
        $extension = File::getExtension('filename');
        $this->assertEquals('', $extension);
    }

    public function testFileGetFileNameFromString(): void
    {
        $fileName = File::getFileName('test.jpg');
        $this->assertEquals('test.jpg', $fileName);
    }

    public function testFileGetFileNameFromArray(): void
    {
        $file = ['name' => 'test.jpg', 'type' => 'image/jpeg'];
        $fileName = File::getFileName($file);
        $this->assertEquals('test.jpg', $fileName);
    }

    public function testFileGetFileNameFromInvalidInput(): void
    {
        $fileName = File::getFileName(123);
        $this->assertEquals('', $fileName);
    }

    public function testFileWrite(): void
    {
        $filePath = './test_write.txt';
        $content = 'Test content';
        
        $result = File::write($filePath, $content);
        $this->assertNotFalse($result);
        
        $this->assertTrue(file_exists($filePath));
        $this->assertEquals($content, file_get_contents($filePath));
        
        unlink($filePath);
    }

    public function testFileSerialize(): void
    {
        $filePath = './test_serialize.txt';
        $data = ['name' => 'John', 'age' => 30];
        
        $result = File::serialize($filePath, $data);
        $this->assertNotFalse($result);
        
        $this->assertTrue(file_exists($filePath));
        
        unlink($filePath);
    }

    public function testFileUnserialize(): void
    {
        $filePath = './test_unserialize.txt';
        $data = ['name' => 'John', 'age' => 30];
        
        file_put_contents($filePath, serialize($data));
        
        $result = File::unserialize($filePath);
        $this->assertEquals($data, $result);
        
        unlink($filePath);
    }

    public function testFileUnserializeNonExistentFile(): void
    {
        $result = File::unserialize('non_existent_file.txt');
        $this->assertNull($result);
    }

    public function testFileUnlinkIfExists(): void
    {
        $filePath = './test_unlink.txt';
        file_put_contents($filePath, 'test');
        
        $this->assertTrue(file_exists($filePath));
        
        $result = File::unlinkIfExists($filePath);
        $this->assertTrue($result);
        $this->assertFalse(file_exists($filePath));
    }

    public function testFileUnlinkIfExistsNonExistentFile(): void
    {
        $result = File::unlinkIfExists('non_existent_file.txt');
        $this->assertFalse($result);
    }

    public function testTextSlugify(): void
    {
        $slug = Text::slugify('Hello World');
        $this->assertEquals('hello-world', $slug);
    }

    public function testTextSlugifyWithSpecialCharacters(): void
    {
        $slug = Text::slugify('Hello, World!');
        $this->assertEquals('hello-world', $slug);
    }

    public function testTextSlugifyWithSpaces(): void
    {
        $slug = Text::slugify('Hello   World');
        $this->assertEquals('hello-world', $slug);
    }

    public function testTextSlugifyWithId(): void
    {
        $slug = Text::slugify('Hello World', 123);
        $this->assertEquals('hello-world-123', $slug);
    }

    public function testTextSlugifyWithCyrillic(): void
    {
        $slug = Text::slugify('Привет Мир');
        $this->assertEquals('privet-mir', $slug);
    }

    public function testTextSlugifyWithCyrillicAndId(): void
    {
        $slug = Text::slugify('Привет Мир', 456);
        $this->assertEquals('privet-mir-456', $slug);
    }

    public function testTextSlugifyEmptyString(): void
    {
        $slug = Text::slugify('');
        $this->assertEquals('', $slug);
    }

    public function testTextSlugifyWithNumbers(): void
    {
        $slug = Text::slugify('Test 123');
        $this->assertEquals('test-123', $slug);
    }

    public function testTextSlugifyWithHyphens(): void
    {
        $slug = Text::slugify('Test-Hyphen');
        $this->assertEquals('test-hyphen', $slug);
    }

    public function testTextSlugifyWithMultipleHyphens(): void
    {
        $slug = Text::slugify('Test---Hyphen');
        $this->assertEquals('test-hyphen', $slug);
    }

    public function testTextSlugifyWithLeadingTrailingHyphens(): void
    {
        $slug = Text::slugify('-Test-');
        $this->assertEquals('test', $slug);
    }

    public function testTextSlugifyWithUnicode(): void
    {
        $slug = Text::slugify('Café');
        $this->assertEquals('cafe', $slug);
    }

    public function testTextSlugifyWithMixedLanguages(): void
    {
        $slug = Text::slugify('Hello Привет');
        $this->assertEquals('hello-privet', $slug);
    }

    public function testTextSlugifyWithOnlySpecialCharacters(): void
    {
        $slug = Text::slugify('!@#$%^&*()');
        $this->assertEquals('', $slug);
    }

    public function testTextSlugifyWithOnlyNumbers(): void
    {
        $slug = Text::slugify('123456');
        $this->assertEquals('123456', $slug);
    }

    public function testTextSlugifyWithOnlyNumbersAndId(): void
    {
        $slug = Text::slugify('123456', 789);
        $this->assertEquals('123456-789', $slug);
    }
}