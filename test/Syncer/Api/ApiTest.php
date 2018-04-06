<?php

namespace FvCommunityNews\Test\Syncer\Api;

use FvCommunityNews\Syncer\Api\Api;
use PHPUnit\Framework\TestCase;

final class ApiTest extends TestCase
{
    public function testCanCreateLatestVersion()
    {
        $this->assertInstanceOf(Api::class, Api::latestVersion());
    }

    public function testIsValidLatestVersion()
    {
        $latestVersion = Api::latestVersion();

        $this->assertEquals('GET', $latestVersion->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/versions/latest', $latestVersion->getUrl());
    }

    public function testCanCreateRetreivePost()
    {
        $this->assertInstanceOf(Api::class, Api::retreivePost(1));
    }

    public function testIsValidRetreivePost()
    {
        $retreivePost = Api::retreivePost(1);
        $retreivePost2 = Api::retreivePost(2);

        $this->assertEquals('GET', $retreivePost->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/1', $retreivePost->getUrl());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/2', $retreivePost2->getUrl());
    }

    public function testCanCreateSubmitPost()
    {
        $this->assertInstanceOf(Api::class, Api::submitPost());
    }

    public function testIsValidSubmitPost()
    {
        $submitPost = Api::submitPost();

        $this->assertEquals('POST', $submitPost->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts', $submitPost->getUrl());
    }

    public function testCanCreateViewPost()
    {
        $this->assertInstanceOf(Api::class, Api::viewPost(1));
    }

    public function testIsValidViewPost()
    {
        $viewPost = Api::viewPost(1);
        $viewPost2 = Api::viewPost(2);

        $this->assertEquals('POST', $viewPost->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/1/views', $viewPost->getUrl());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/2/views', $viewPost2->getUrl());
    }

    public function testCanCreateLikePost()
    {
        $this->assertInstanceOf(Api::class, Api::likePost(1));
    }

    public function testIsValidLikePost()
    {
        $likePost = Api::likePost(1);
        $likePost2 = Api::likePost(2);

        $this->assertEquals('POST', $likePost->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/1/likes', $likePost->getUrl());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/2/likes', $likePost2->getUrl());
    }

    public function testCanCreateUnlikePost()
    {
        $this->assertInstanceOf(Api::class, Api::unlikePost(1));
    }

    public function testIsValidUnlikePost()
    {
        $unlikePost = Api::unlikePost(1);
        $unlikePost2 = Api::unlikePost(2);

        $this->assertEquals('DELETE', $unlikePost->getMethod());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/1/likes', $unlikePost->getUrl());
        $this->assertEquals('https://api.frankverhoeven.me/fvcn/1.0/posts/2/likes', $unlikePost2->getUrl());
    }
}
