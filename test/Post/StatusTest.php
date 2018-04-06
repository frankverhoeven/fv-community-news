<?php

namespace FvCommunityNews\Test\Post;

use FvCommunityNews\Post\Status;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function testCanCreatePublish()
    {
        $this->assertInstanceOf(Status::class, Status::publish());
    }

    public function testIsValidPublish()
    {
        $this->assertEquals('publish', Status::publish()->getStatus());
    }

    public function testCanCreatePrivate()
    {
        $this->assertInstanceOf(Status::class, Status::private());
    }

    public function testIsValidPrivate()
    {
        $this->assertEquals('private', Status::private()->getStatus());
    }

    public function testCanCreatePending()
    {
        $this->assertInstanceOf(Status::class, Status::pending());
    }

    public function testIsValidPending()
    {
        $this->assertEquals('pending', Status::pending()->getStatus());
    }

    public function testCanCreateSpam()
    {
        $this->assertInstanceOf(Status::class, Status::spam());
    }

    public function testIsValidSpam()
    {
        $this->assertEquals('spam', Status::spam()->getStatus());
    }

    public function testCanCreateTrash()
    {
        $this->assertInstanceOf(Status::class, Status::trash());
    }

    public function testIsValidTrash()
    {
        $this->assertEquals('trash', Status::trash()->getStatus());
    }

    public function testCanBeUsedAsString()
    {
        $this->assertEquals('publish', Status::publish());
        $this->assertEquals('private', Status::private());
        $this->assertEquals('pending', Status::pending());
        $this->assertEquals('spam', Status::spam());
        $this->assertEquals('trash', Status::trash());
    }
}
