<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\View;
use PHPFramework\Utils\File;

class ViewTest extends TestCase {
    protected const TEST_VIEW_FILE = VIEWS . '/test.php';
    protected View $view;

    protected function setUp() : void
    {
        parent::setUp();
        $this->view = new View();
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        if (file_exists(self::TEST_VIEW_FILE)) {
            unlink(self::TEST_VIEW_FILE);
        }
        if (is_dir(dirname(self::TEST_VIEW_FILE))) {
            rmdir(dirname(self::TEST_VIEW_FILE));
        }
    }

    public function testRenderWithData(): void
    {
        $data = ['name' => 'Foo'];
        $result = $this->renderTestView('Hello <?= $name ?>!', $data);

        $this->assertStringContainsString('Hello Foo!', $result);
    }

    public function testRenderWithoutData(): void
    {
        $result = $this->renderTestView('Hello <?= $name ?? "stranger" ?>!');
        $this->assertStringContainsString("Hello stranger!", $result);
    }

    public function testViewHandlesSpecialCharacters(): void
    {
        $data = ['message' => 'Hello "World" & <script>alert("xss")</script>'];
        $result = $this->renderTestView('Message: <?= $message ?>', $data);

        $this->assertStringContainsString('Hello "World" & <script>alert("xss")</script>', $result);
    }

    private function renderTestView(string $viewContent, array $data = []) : string
    {
        $viewFile = $this->createViewFile($viewContent);
        $result = $this->view->render('test', $data);

        unlink($viewFile);
        return $result;
    }

    private function createViewFile(string $viewContent) : string
    {
        File::makeDirIfNotExists(dirname(self::TEST_VIEW_FILE));
        file_put_contents(self::TEST_VIEW_FILE, $viewContent);
        return self::TEST_VIEW_FILE;
    }
}