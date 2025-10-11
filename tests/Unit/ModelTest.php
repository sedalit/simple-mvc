<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Model;

class TestModel extends Model
{
    protected array $fillable = ['name', 'email', 'age', 'active', 'tags'];

    public function __construct()
    {
        $_SERVER = ['REQUEST_METHOD' => 'POST'];
        parent::__construct();
    }
    
    protected function tableName(): string
    {
        return 'test_models';
    }
    
    protected function primaryKeyName(): string
    {
        return 'id';
    }
}

class ModelTest extends TestCase
{
    protected TestModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TestModel();
    }

    public function testCanCreateModel(): void
    {
        $this->assertInstanceOf(Model::class, $this->model);
        $this->assertInstanceOf(\ArrayAccess::class, $this->model);
    }

    public function testCanSetAndGetAttributes(): void
    {
        $this->model->setAttribute('name', 'John');
        $this->model->setAttribute('email', 'john@example.com');
        
        $this->assertEquals('John', $this->model->name);
        $this->assertEquals('john@example.com', $this->model->email);
    }

    public function testCanGetAllAttributes(): void
    {
        $this->model->setAttribute('name', 'John');
        $this->model->setAttribute('email', 'john@example.com');
        $this->model->setAttribute('age', 22);
        $this->model->setAttribute('active', true);
        $this->model->setAttribute('tags', [1, 2]);
        
        $attributes = $this->model->attributes();
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com', 'age' => 22, 'active' => true, 'tags' => [1, 2]], $attributes);
    }

    public function testCanUseArrayAccess(): void
    {
        $this->model['name'] = 'John';
        $this->model['email'] = 'john@example.com';
        
        $this->assertEquals('John', $this->model['name']);
        $this->assertEquals('john@example.com', $this->model['email']);
        $this->assertTrue(isset($this->model['name']));
        $this->assertFalse(isset($this->model['non_existent']));
    }

    public function testCanUnsetArrayAccess(): void
    {
        $this->model['name'] = 'John';
        $this->assertTrue(isset($this->model['name']));
        
        unset($this->model['name']);
        $this->assertFalse(isset($this->model['name']));
    }

    public function testCanUseMagicMethods(): void
    {
        $this->model->name = 'John';
        $this->model->email = 'john@example.com';
        
        $this->assertEquals('John', $this->model->name);
        $this->assertEquals('john@example.com', $this->model->email);
    }

    public function testMagicSetOnlyWorksWithFillableFields(): void
    {
        $this->model->name = 'John'; // fillable
        $this->model->non_fillable = 'value'; // not fillable
        
        $this->assertEquals('John', $this->model->name);
        $this->assertNull($this->model->non_fillable);
    }

    public function testCanUnsetMagicProperties(): void
    {
        $this->model->name = 'John';
        $this->assertEquals('John', $this->model->name);
        
        unset($this->model->name);
        $this->assertNull($this->model->name);
    }

    public function testCanGetNonExistentProperty(): void
    {
        $this->assertNull($this->model->non_existent);
    }

    public function testCanGetNonExistentArrayAccess(): void
    {
        $this->assertNull($this->model['non_existent']);
    }

    public function testModelLoadsDataFromRequest(): void
    {
        $_POST = ['name' => 'John', 'email' => 'john@example.com'];

        $model = new TestModel();
        $this->assertEquals('John', $model->name);
        $this->assertEquals('john@example.com', $model->email);
    }

    public function testModelLoadsOnlyFillableData(): void
    {
        $_POST = [
            'name' => 'John',
            'email' => 'john@example.com',
            'non_fillable' => 'value'
        ];
        
        $model = new TestModel();
        $this->assertEquals('John', $model->name);
        $this->assertEquals('john@example.com', $model->email);
        $this->assertNull($model->non_fillable);
    }

    public function testModelHandlesEmptyRequestData(): void
    {
        $_POST = [];
        
        $model = new TestModel();
        $this->assertEquals('', $model->name);
        $this->assertEquals('', $model->email);
    }

    public function testModelHandlesNullRequestData(): void
    {
        $_POST = ['name' => null, 'email' => null];
        
        $model = new TestModel();
        $this->assertEquals('', $model->name);
        $this->assertEquals('', $model->email);
    }

    public function testModelHandlesSpecialCharacters(): void
    {
        $_POST = [
            'name' => 'John "Doe"',
            'email' => 'john+tag@example.com'
        ];
        
        $model = new TestModel();
        $this->assertEquals('John "Doe"', $model->name);
        $this->assertEquals('john+tag@example.com', $model->email);
    }

    public function testModelHandlesUnicodeCharacters(): void
    {
        $_POST = [
            'name' => 'Иван',
            'email' => 'иван@example.com'
        ];
        
        $model = new TestModel();
        $this->assertEquals('Иван', $model->name);
        $this->assertEquals('иван@example.com', $model->email);
    }

    public function testModelHandlesNumericData(): void
    {
        $_POST = [
            'name' => 'John',
            'age' => '30'
        ];
        
        $model = new TestModel();
        $this->assertEquals('John', $model->name);
        $this->assertEquals('30', $model->age);
    }

    public function testModelHandlesWhitespaceData(): void
    {
        $_POST = [
            'name' => '  John  ',
            'email' => '  john@example.com  '
        ];
        
        $model = new TestModel();
        $this->assertEquals('John', $model->name);
        $this->assertEquals('john@example.com', $model->email);
    }
}