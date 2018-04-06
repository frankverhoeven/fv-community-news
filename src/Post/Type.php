<?php

namespace FvCommunityNews\Post;

/**
 * Post Type
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class Type
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->type;
    }

    /**
     * Post type key.
     *
     * @return Type
     */
    public static function post(): Type
    {
        return new static('fvcn-post');
    }

    /**
     * Tag type key.
     *
     * @return Type
     */
    public static function tag(): Type
    {
        return new static('fvcn-tag');
    }
}
