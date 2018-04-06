<?php

namespace FvCommunityNews\Post;

/**
 * Post Statuses
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class Status
{
    /**
     * @var string
     */
    private $status;

    /**
     * @param string $status
     */
    private function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->status;
    }

    /**
     * Publish post status
     *
     * @return Status
     */
    public static function publish(): Status
    {
        return new static('publish');
    }

    /**
     * Private post status
     *
     * @return Status
     */
    public static function private(): Status
    {
        return new static('private');
    }

    /**
     * Pending post status
     *
     * @return Status
     */
    public static function pending(): Status
    {
        return new static('pending');
    }

    /**
     * Spam post status
     *
     * @return Status
     */
    public static function spam(): Status
    {
        return new static('spam');
    }

    /**
     * Trash post status
     *
     * @return Status
     */
    public static function trash(): Status
    {
        return new static('trash');
    }
}
