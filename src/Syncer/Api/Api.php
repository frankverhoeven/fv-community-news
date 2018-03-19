<?php

namespace FvCommunityNews\Syncer\Api;

/**
 * Api
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class Api
{
    /**
     * @var string
     */
    const API_BASE = 'https://api.frankverhoeven.me/fvcn/1.0';
    /**
     * @var string
     */
    const API_POSTS = self::API_BASE . '/posts';
    /**
     * @var string
     */
    const API_VERSIONS = self::API_BASE . '/versions';

    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $method;

    /**
     * @param string $url
     * @param string $method
     */
    private function __construct(string $url, string $method)
    {
        $this->url = $url;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Make an API request to retreive the latest plugin version.
     *
     * @return Api
     */
    public static function latestVersion(): Api
    {
        return new static(static::API_VERSIONS . '/current', 'GET');
    }

    /**
     * Make an API request to retreive a post.
     *
     * @param int $id ID of the post to retreive.
     * @return Api
     */
    public static function retreivePost(int $id): Api
    {
        return new static(static::API_POSTS . '/' . $id, 'GET');
    }

    /**
     * Make an API request to submit a post.
     *
     * @return Api
     */
    public static function submitPost(): Api
    {
        return new static(static::API_POSTS, 'POST');
    }

    /**
     * Make an API request to add a view to a post.
     *
     * @param int $id ID of the post that is viewed.
     * @return Api
     */
    public static function viewPost(int $id): Api
    {
        return new static(static::API_POSTS . '/' . $id . '/views', 'POST');
    }

    /**
     * Make an API request to add a star to a post.
     *
     * @param int $id ID of the post to star.
     * @return Api
     */
    public static function starPost(int $id): Api
    {
        return new static(static::API_POSTS . '/' . $id . '/stars', 'POST');
    }

    /**
     * Make an API request to remove a star from a post.
     *
     * @param int $id ID of the post to unstar.
     * @return Api
     */
    public static function unstarPost(int $id): Api
    {
        return new static(static::API_POSTS . '/' . $id . '/stars', 'DELETE');
    }
}
