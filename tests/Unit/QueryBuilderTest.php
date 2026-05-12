<?php

namespace Tests\Unit;

use PHPFramework\Database;
use PHPFramework\QueryBuilder;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase {
    protected Database $db;
    protected QueryBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = $this->createMock(Database::class);
        $this->builder = new QueryBuilder($this->db);
    }

    public function testTableMethod()
    {
        $result = $this->builder->table('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testSelectMethod()
    {
        $this->builder->table('users')->select(['id', 'name', 'email']);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('SELECT `id`, `name`, `email`', $sql);
        $this->assertStringContainsString('FROM `users`', $sql);
    }

    public function testSelectWithMultipleArguments()
    {
        $this->builder->table('users')->select('id', 'name', 'email');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('SELECT `id`, `name`, `email`', $sql);
    }

    public function testWhereMethod()
    {
        $this->builder->table('users')->where('id', '=', 1);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `id` = ?', $sql);
        $this->assertEquals([1], $this->builder->getBindings());
    }

    public function testWhereWithTwoArguments()
    {
        $this->builder->table('users')->where('id', 1);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `id` = ?', $sql);
        $this->assertEquals([1], $this->builder->getBindings());
    }

    public function testMultipleWhereConditions()
    {
        $this->builder->table('users')
            ->where('status', 'active')
            ->where('age', '>', 18);
        
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `status` = ? AND `age` > ?', $sql);
        $this->assertEquals(['active', 18], $this->builder->getBindings());
    }

    public function testOrWhereMethod()
    {
        $this->builder->table('users')
            ->where('status', 'active')
            ->orWhere('role', 'admin');
        
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `status` = ? OR `role` = ?', $sql);
        $this->assertEquals(['active', 'admin'], $this->builder->getBindings());
    }

    public function testWhereInMethod()
    {
        $this->builder->table('users')->whereIn('id', [1, 2, 3]);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `id` IN (?, ?, ?)', $sql);
        $this->assertEquals([1, 2, 3], $this->builder->getBindings());
    }

    public function testWhereNotInMethod()
    {
        $this->builder->table('users')->whereNotIn('status', ['banned', 'deleted']);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `status` NOT IN (?, ?)', $sql);
        $this->assertEquals(['banned', 'deleted'], $this->builder->getBindings());
    }

    public function testWhereBetweenMethod()
    {
        $this->builder->table('users')->whereBetween('age', [18, 65]);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `age` BETWEEN ? AND ?', $sql);
        $this->assertEquals([18, 65], $this->builder->getBindings());
    }

    public function testWhereBetweenThrowsExceptionWithInvalidValues()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->table('users')->whereBetween('age', [18]);
    }

    public function testWhereNullMethod()
    {
        $this->builder->table('users')->whereNull('deleted_at');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `deleted_at` IS NULL', $sql);
    }

    public function testWhereNotNullMethod()
    {
        $this->builder->table('users')->whereNotNull('email_verified_at');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('WHERE `email_verified_at` IS NOT NULL', $sql);
    }

    public function testJoinMethod()
    {
        $this->builder->table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id');

        $sql = $this->builder->toSql();
        $this->assertStringContainsString('INNER JOIN `posts` ON `users`.`id` = `posts`.`user_id`', $sql);
    }

    public function testLeftJoinMethod()
    {
        $this->builder->table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id');

        $sql = $this->builder->toSql();
        $this->assertStringContainsString('LEFT JOIN `posts` ON `users`.`id` = `posts`.`user_id`', $sql);
    }

    public function testRightJoinMethod()
    {
        $this->builder->table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id');

        $sql = $this->builder->toSql();
        $this->assertStringContainsString('RIGHT JOIN `posts` ON `users`.`id` = `posts`.`user_id`', $sql);
    }

    public function testOrderByMethod()
    {
        $this->builder->table('users')->orderBy('created_at', 'DESC');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('ORDER BY `created_at` DESC', $sql);
    }

    public function testOrderByDefaultDirection()
    {
        $this->builder->table('users')->orderBy('name');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('ORDER BY `name` ASC', $sql);
    }

    public function testOrderByThrowsExceptionWithInvalidDirection()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->table('users')->orderBy('name', 'INVALID');
    }

    public function testMultipleOrderBy()
    {
        $this->builder->table('users')
            ->orderBy('status', 'ASC')
            ->orderBy('created_at', 'DESC');
        
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('ORDER BY `status` ASC, `created_at` DESC', $sql);
    }

    public function testGroupByMethod()
    {
        $this->builder->table('orders')->groupBy('user_id');
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('GROUP BY `user_id`', $sql);
    }

    public function testGroupByWithMultipleColumns()
    {
        $this->builder->table('orders')->groupBy(['user_id', 'status']);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('GROUP BY `user_id`, `status`', $sql);
    }

    public function testHavingMethod()
    {
        $this->builder->table('orders')
            ->select(['user_id', 'COUNT(*) as total'])
            ->groupBy('user_id')
            ->having('total', '>', 5);
        
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('HAVING `total` > ?', $sql);
    }

    public function testLimitMethod()
    {
        $this->builder->table('users')->limit(10);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    public function testOffsetMethod()
    {
        $this->builder->table('users')->limit(10)->offset(20);
        $sql = $this->builder->toSql();
        $this->assertStringContainsString('LIMIT 10', $sql);
        $this->assertStringContainsString('OFFSET 20', $sql);
    }

    public function testComplexQuery()
    {
        $this->builder->table('users')
            ->select(['users.id', 'users.name', 'COUNT(posts.id) as posts_count'])
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->where('users.status', 'active')
            ->whereNotNull('users.email_verified_at')
            ->groupBy('users.id')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'DESC')
            ->limit(10);
        
        $sql = $this->builder->toSql();
        
        $this->assertStringContainsString('SELECT `users`.`id`, `users`.`name`, COUNT(posts.id) as posts_count', $sql);
        $this->assertStringContainsString('FROM `users`', $sql);
        $this->assertStringContainsString('LEFT JOIN `posts`', $sql);
        $this->assertStringContainsString('WHERE `users`.`status` = ?', $sql);
        $this->assertStringContainsString('AND `users`.`email_verified_at` IS NOT NULL', $sql);
        $this->assertStringContainsString('GROUP BY `users`.`id`', $sql);
        $this->assertStringContainsString('HAVING `posts_count` > ?', $sql);
        $this->assertStringContainsString('ORDER BY `posts_count` DESC', $sql);
        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    public function testGetMethod()
    {
        $expectedData = [
            ['id' => 1, 'name' => 'John'],
            ['id' => 2, 'name' => 'Jane']
        ];

        $this->db->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getAll')
            ->willReturn($expectedData);

        $result = $this->builder->table('users')->get();
        $this->assertEquals($expectedData, $result);
    }

    public function testFirstMethod()
    {
        $expectedData = ['id' => 1, 'name' => 'John'];

        $this->db->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getOne')
            ->willReturn($expectedData);

        $result = $this->builder->table('users')->first();
        $this->assertEquals($expectedData, $result);
    }

    public function testFindMethod()
    {
        $expectedData = ['id' => 1, 'name' => 'John'];

        $this->db->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getOne')
            ->willReturn($expectedData);

        $result = $this->builder->table('users')->find(1);
        $this->assertEquals($expectedData, $result);
    }

    public function testCountMethod()
    {
        $this->db->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getOne')
            ->willReturn(['count' => 42]);

        $result = $this->builder->table('users')->count();
        $this->assertEquals(42, $result);
    }

    public function testExistsMethod()
    {
        $this->db->expects($this->once())
            ->method('query')
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getOne')
            ->willReturn(['1' => 1]);

        $result = $this->builder->table('users')->where('id', 1)->exists();
        $this->assertTrue($result);
    }

    public function testInsertMethod()
    {
        $data = ['name' => 'John', 'email' => 'john@example.com'];

        $this->db->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains('INSERT INTO `users`'),
                $this->equalTo(['John', 'john@example.com'])
            )
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('getInsertedId')
            ->willReturn('1');

        $result = $this->builder->table('users')->insert($data);
        $this->assertEquals('1', $result);
    }

    public function testUpdateMethod()
    {
        $data = ['name' => 'Jane'];

        $this->db->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains('UPDATE `users` SET `name` = ?'),
                $this->equalTo(['Jane', 1])
            )
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->builder->table('users')->where('id', 1)->update($data);
        $this->assertEquals(1, $result);
    }

    public function testDeleteMethod()
    {
        $this->db->expects($this->once())
            ->method('query')
            ->with(
                $this->stringContains('DELETE FROM `users` WHERE `id` = ?'),
                $this->equalTo([1])
            )
            ->willReturnSelf();

        $this->db->expects($this->once())
            ->method('rowCount')
            ->willReturn(1);

        $result = $this->builder->table('users')->where('id', 1)->delete();
        $this->assertEquals(1, $result);
    }
}