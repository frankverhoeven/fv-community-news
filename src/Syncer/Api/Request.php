<?php

declare(strict_types=1);

namespace FvCommunityNews\Syncer\Api;

use FvCommunityNews\Version;
use WP_Error;

/**
 * Request
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class Request
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $options;

    /**
     * Create a new API request.
     * 
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->url = $api->getUrl();
        $this->options = [
            'method' => $api->getMethod(),
        ];
    }

    /**
     * Execute the API request.
     *
     * @param array $data
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function execute(array $data = [], array $options = []): array
    {
        $this->attachBlogInfoToData($data);
        $options = \array_merge($this->options, $options, ['body' => $data]);
        $response = \wp_remote_request($this->url, $options);

        if ($response instanceof WP_Error) {
            throw new Exception($response->get_error_message(), $response->get_error_code());
        }

        return $response;
    }

    /**
     * @param array $data
     * @return void
     */
    private function attachBlogInfoToData(array &$data)
    {
        $blogInfo = [
            'blog_name'         => \get_bloginfo('name'),
            'blog_description'  => \get_bloginfo('description'),
            'blog_url'          => \get_bloginfo('url'),
            'wordpress_url'     => \get_bloginfo('wpurl'),
            'wordpress_version' => \get_bloginfo('version'),
            'plugin_version'    => Version::getCurrentVersion(),
            'php_version'       => \phpversion(),
        ];

        foreach ($blogInfo as $key => $value) {
            // Make sure we do not overwrite data
            if (!isset($data[$key])) {
                $data[$key] = $value;
            }
        }
    }
}
