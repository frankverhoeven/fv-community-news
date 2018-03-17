<?php

namespace FvCommunityNews\Syncer\Api;

use WP_Error;

/**
 * Request
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Request
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
        $options = \array_merge($this->options, $options, ['body' => $data]);
        $response = \wp_remote_request($this->url, $options);

        if ($response instanceof WP_Error) {
            throw new Exception($response->get_error_message(), $response->get_error_code());
        }

        return $response;
    }
}
