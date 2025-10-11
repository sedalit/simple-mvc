<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Pagination;
use PHPFramework\PaginationRenderer;

class PaginationTest extends TestCase {
    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
    }

    public function testCreatePagination() : void
    {
        $pagination = new Pagination(1, 10, 100);
        
        $this->assertEquals(0, $pagination->getOffset());
        $this->assertEquals(10, $pagination->getPagesCount());
    }

    public function testGetOffset() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $this->assertEquals(0, $pagination->getOffset());
        
        $pagination = new Pagination(2, 10, 100);
        $this->assertEquals(10, $pagination->getOffset());
        
        $pagination = new Pagination(3, 10, 100);
        $this->assertEquals(20, $pagination->getOffset());
    }

    public function testGetPagesCount() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $this->assertEquals(10, $pagination->getPagesCount());
        
        $pagination = new Pagination(1, 15, 100);
        $this->assertEquals(7, $pagination->getPagesCount());
        
        $pagination = new Pagination(1, 10, 95);
        $this->assertEquals(10, $pagination->getPagesCount());
    }

    public function testGetPages() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $pages = $pagination->getPages();
        
        $this->assertIsArray($pages);
        $this->assertNotEmpty($pages);
        
        $firstPage = $pages[0];
        $this->assertArrayHasKey('label', $firstPage);
        $this->assertArrayHasKey('page', $firstPage);
        $this->assertArrayHasKey('link', $firstPage);
        $this->assertArrayHasKey('active', $firstPage);
    }

    public function testSetMiddleSize() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $pagination->setMiddleSize(5);
        
        $this->assertEquals(5, $pagination->getMiddleSize());
    }

    public function testRenderPagination() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $html = $pagination->render();
        
        $this->assertStringContainsString('<nav', $html);
        $this->assertStringContainsString("<ul class='pagination'>", $html);
        $this->assertStringContainsString('<li', $html);
    }

    public function testConvertToString() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $html = (string)$pagination;
        
        $this->assertStringContainsString('<nav', $html);
        $this->assertStringContainsString("<ul class='pagination'>", $html);
    }

    public function testPaginationWithSinglePage() : void
    {
        $pagination = new Pagination(1, 10, 5);
        $pages = $pagination->getPages();
        
        $this->assertCount(1, $pages);
        $this->assertTrue($pages[0]['active']);
    }

    public function testPaginationWithTwoPages() : void
    {
        $pagination = new Pagination(1, 10, 15);
        $pages = $pagination->getPages();

        $this->assertCount(2, $pages);
        $this->assertTrue($pages[0]['active']);
        $this->assertFalse($pages[1]['active']);
    }

    public function testPaginationWithManyPages() : void
    {
        $pagination = new Pagination(5, 10, 100);
        $pages = $pagination->getPages();
        
        $this->assertGreaterThan(3, count($pages));
        
        $activePage = array_filter($pages, fn($page) => $page['active']);
        $this->assertCount(1, $activePage);
        $this->assertEquals(5, reset($activePage)['page']);
    }

    public function testPaginationWithInvalidPage() : void
    {
        $pagination = new Pagination(0, 10, 100);
        $this->assertEquals(1, $pagination->getCurrentPage());
        
        $pagination = new Pagination(15, 10, 100);
        $this->assertEquals(1, $pagination->getCurrentPage());
    }

    public function testPaginationWithZeroTotal() : void
    {
        $pagination = new Pagination(1, 10, 0);
        $this->assertEquals(0, $pagination->getPagesCount());
        $this->assertEquals(0, $pagination->getOffset());
    }

    public function testPaginationWithNegativeValues() : void
    {
        $pagination = new Pagination(-1, -10, -100);
        $this->assertEquals(1, $pagination->getCurrentPage());
        $this->assertEquals(0, $pagination->getOffset());
    }

    public function testPaginationRenderer() : void
    {
        $pagination = new Pagination(1, 10, 100);
        $html = PaginationRenderer::render($pagination);
        
        $this->assertStringContainsString("<nav aria-label='Page navigation'", $html);
        $this->assertStringContainsString("<ul class='pagination'", $html);
        $this->assertStringContainsString("<li class='page-item'", $html);
        $this->assertStringContainsString("<a class='page-link'", $html);
    }

    public function testPaginationRendererWithActivePage() : void
    {
        $pagination = new Pagination(2, 10, 100);
        $html = PaginationRenderer::render($pagination);
        
        $this->assertStringContainsString("class='page-item active'", $html);
    }

    public function testPaginationRendererWithNavigationArrows() : void
    {
        $pagination = new Pagination(5, 10, 100);
        $html = PaginationRenderer::render($pagination);
        
        $this->assertStringContainsString('&laquo;', $html);
        $this->assertStringContainsString('&raquo;', $html);
        $this->assertStringContainsString('&lt;', $html);
        $this->assertStringContainsString('&gt;', $html);
    }

    public function testPaginationWithLargeDataset() : void
    {
        $pagination = new Pagination(50, 10, 10000);
        $pages = $pagination->getPages();
        
        $this->assertGreaterThan(5, count($pages));
        $this->assertEquals(1000, $pagination->getPagesCount());
    }

    public function testPaginationWithSmallPerPage() : void
    {
        $pagination = new Pagination(1, 1, 100);
        $this->assertEquals(100, $pagination->getPagesCount());
        $this->assertEquals(0, $pagination->getOffset());
    }

    public function testPaginationWithLargePerPage() : void
    {
        $pagination = new Pagination(1, 1000, 100);
        $this->assertEquals(1, $pagination->getPagesCount());
        $this->assertEquals(0, $pagination->getOffset());
    }
}