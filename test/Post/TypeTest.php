<?php

namespace FvCommunityNews\Test\Post;

use FvCommunityNews\Post\Type;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testCanCreatePost()
    {
        $this->assertInstanceOf(Type::class, Type::post());
    }

    public function testIsValidPost()
    {
        $this->assertEquals('fvcn-post', Type::post()->getType());
    }

    public function testCanCreateTag()
    {
        $this->assertInstanceOf(Type::class, Type::tag());
    }

    public function testIsValidTag()
    {
        $this->assertEquals('fvcn-tag', Type::tag()->getType());
    }

    public function testCanBeUsedAsString()
    {
        $this->assertEquals('fvcn-post', Type::post());
        $this->assertEquals('fvcn-tag', Type::tag());
    }
}
