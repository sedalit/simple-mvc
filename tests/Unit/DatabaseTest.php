<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Database;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;

class DatabaseTest extends TestCase
{
    protected Database $database;
    protected MockObject $mockPDO;
    protected MockObject $mockStatement;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockPDO = $this->createMock(PDO::class);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        
        $this->mockPDO->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn(['id' => 1, 'name' => 'Test']);
        $this->mockStatement->method('fetchAll')->willReturn([['id' => 1, 'name' => 'Test']]);
        $this->mockStatement->method('fetchColumn')->willReturn(1);
        $this->mockStatement->method('rowCount')->willReturn(1);
        $this->mockPDO->method('lastInsertId')->willReturn('123');
        
        $this->database = new class($this->mockPDO) extends Database {
            const TABLES_WHITELIST = [];
            public function __construct(PDO $pdo) {
                $this->connection = $pdo;
            }
        };
    }

    public function testCanExecuteQuery(): void
    {
        $result = $this->database->query('SELECT * FROM users WHERE id = ?', [1]);
        
        $this->assertInstanceOf(Database::class, $result);
    }

    public function testCanGetOneRecord(): void
    {
        $this->database->query('SELECT * FROM users WHERE id = ?', [1]);
        $result = $this->database->getOne();
        
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $result);
    }

    public function testCanGetAllRecords(): void
    {
        $this->database->query('SELECT * FROM users');
        $result = $this->database->getAll();
        
        $this->assertEquals([['id' => 1, 'name' => 'Test']], $result);
    }

    public function testCanGetColumn(): void
    {
        $this->database->query('SELECT COUNT(*) FROM users');
        $result = $this->database->getColumn();
        
        $this->assertEquals(1, $result);
    }

    public function testCanFindAll(): void
    {
        $result = $this->database->findAll('users');
        
        $this->assertEquals([['id' => 1, 'name' => 'Test']], $result);
    }

    public function testCanFindOne(): void
    {
        $result = $this->database->findOne('users', 1);
        
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $result);
    }

    public function testFindOrFailReturnsRecord(): void
    {
        $result = $this->database->findOrFail('users', 1);
        
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $result);
    }

    public function testCanGetInsertedId(): void
    {
        $result = $this->database->getInsertedId();
        
        $this->assertEquals('123', $result);
    }

    public function testCanGetRowCount(): void
    {
        $this->database->query('UPDATE users SET name = ? WHERE id = ?', ['New Name', 1]);
        $result = $this->database->rowCount();
        
        $this->assertEquals(1, $result);
    }

    public function testCanCountRecords(): void
    {
        $result = $this->database->count('users');
        
        $this->assertEquals(1, $result);
    }

    public function testQueryWithParameters(): void
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([1, 'John']);
            
        $this->database->query('SELECT * FROM users WHERE id = ? AND name = ?', [1, 'John']);
    }

    public function testQueryWithoutParameters(): void
    {
        $this->mockStatement->expects($this->once())
            ->method('execute')
            ->with([]);
            
        $this->database->query('SELECT * FROM users');
    }

    public function testHandlesPDOException(): void
    {
        $this->mockPDO->method('prepare')
            ->willThrowException(new \PDOException('Database error'));
            
        $this->expectException(\Exception::class);
        $this->database->query('INVALID SQL');
    }

    public function testCanGetQueriesInDebugMode(): void
    {
        // В реальном тесте нужно было бы настроить DEBUG константу
        $queries = $this->database->getQueries();
        $this->assertIsArray($queries);
    }
}