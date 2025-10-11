<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Factory;
use stdClass;

class FactoryTest extends TestCase
{
    public function testCanCreateFactory(): void
    {
        $factory = new Factory(\stdClass::class);
        $this->assertInstanceOf(Factory::class, $factory);
    }

    public function testCanCreateFactoryWithArguments(): void
    {
        $factory = new Factory(\stdClass::class, ['arg1', 'arg2']);
        $this->assertInstanceOf(Factory::class, $factory);
    }

    public function testCanInvokeFactory(): void
    {
        $factory = new Factory(\stdClass::class);
        $object = $factory();
        
        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testCanInvokeFactoryWithCustomClass(): void
    {
        $factory = new Factory(\stdClass::class);
        $object = $factory(\ArrayObject::class);
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
    }

    public function testCanInvokeFactoryWithCustomArguments(): void
    {
        $factory = new Factory(\ArrayObject::class);
        $object = $factory(\ArrayObject::class, [['key' => 'value']]);
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['key' => 'value'], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithStoredArguments(): void
    {
        $factory = new Factory(\ArrayObject::class, [['stored' => 'data']]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['stored' => 'data'], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithMixedArguments(): void
    {
        $factory = new Factory(\ArrayObject::class, [['stored' => 'data']]);
        $object = $factory(\ArrayObject::class, [['custom' => 'data']]);
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['custom' => 'data'], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithEmptyArguments(): void
    {
        $factory = new Factory(\stdClass::class, []);
        $object = $factory();
        
        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testCanInvokeFactoryWithNullArguments(): void
    {
        $factory = new Factory(\stdClass::class, [null]);
        $object = $factory();
        
        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testCanInvokeFactoryWithMultipleArguments(): void
    {
        $factory = new Factory(\ArrayObject::class, [['arg1'], 0]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
    }

    public function testCanInvokeFactoryWithObjectArguments(): void
    {
        $obj = new \stdClass();
        $factory = new Factory(\ArrayObject::class, [$obj]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
    }

    public function testCanInvokeFactoryWithNestedArrays(): void
    {
        $factory = new Factory(\ArrayObject::class, [['nested' => ['deep' => 'value']]]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['nested' => ['deep' => 'value']], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithSpecialCharacters(): void
    {
        $factory = new Factory(\ArrayObject::class, [['special' => '!@#$%^&*()']]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['special' => '!@#$%^&*()'], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithUnicodeCharacters(): void
    {
        $factory = new Factory(\ArrayObject::class, [['unicode' => 'Привет мир']]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals(['unicode' => 'Привет мир'], $object->getArrayCopy());
    }

    public function testCanInvokeFactoryWithEmptyArray(): void
    {
        $factory = new Factory(\ArrayObject::class, [[]]);
        $object = $factory();
        
        $this->assertInstanceOf(\ArrayObject::class, $object);
        $this->assertEquals([], $object->getArrayCopy());
    }
}