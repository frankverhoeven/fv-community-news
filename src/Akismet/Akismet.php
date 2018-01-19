<?php

namespace FvCommunityNews\Akismet;

use Exception;

/**
 * Akismet
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Akismet
{
    /**
     * @var string
     */
    protected $apiKey;
    /**
     * @var string
     */
    protected $blogUrl;

    /**
     * __construct()
     *
     * @version 20120711
     * @param string $apiKey
     * @param string $blogUrl
     */
    public function __construct($apiKey, $blogUrl)
    {
        $this->setApiKey($apiKey)
            ->setBlogUrl($blogUrl);
    }

    /**
     * setApiKey()
     *
     * @version 20120711
     * @param string $apiKey
     * @return Akismet
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * getApiKey()
     *
     * @version 20120711
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * setBlogUrl()
     *
     * @version 20120711
     * @param string $blogUrl
     * @return Akismet
     */
    public function setBlogUrl($blogUrl)
    {
        $this->blogUrl = $blogUrl;
        return $this;
    }

    /**
     * getBlogUrl()
     *
     * @version 20120711
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * post()
     *
     * @param string $host
     * @param string $path
     * @param array $params
     * @return string
     * @throws Exception
     * @version 20171111
     */
    protected function post($host, $path, array $params)
    {
        $uri = 'http://' . $host . $path;
        $request = [
            'body' => $params,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
                'Host' => $host,
                'User-Agent' => 'FV Community News/' . fvcn_get_version() . ' | Akismet/20120711'
            ],
            'httpversion' => '1.0',
            'timeout' => 15
        ];

        $response = wp_remote_post($uri, $request);

        if (is_wp_error($response)) {
            throw new Exception('Error while accessing Akismet');
        }

        return trim($response['body']);
    }

    /**
     * makeApiCall()
     *
     * @version 20120711
     * @param string $path
     * @param array $params
     * @return string
     * @throws Exception
     */
    protected function makeApiCall($path, array $params)
    {
        if (isset($params['user_ip'], $params['user_agent'])) {
            $params['blog'] = $this->getBlogUrl();

            return $this->post($this->getApiKey() . '.rest.akismet.com', $path, $params);
        }

        throw new Exception('Missing required Akismet params (user_ip, user_agent)');
    }

    /**
     * verifyKey()
     *
     * @version 20120711
     * @param string $key
     * @param string $blog
     * @return bool
     * @throws Exception
     */
    public function verifyKey($key=null, $blog=null)
    {
        if (null === $key) {
            $key = $this->getApiKey();
        }
        if (null === $blog) {
            $blog = $this->getBlogUrl();
        }

        return ('valid' == $this->post('rest.akismet.com', '/1.1/verify-key', ['key' => $key, 'blog' => $blog]));
    }

    /**
     * isSpam()
     *
     * @version 20120711
     * @param array $params
     * @return bool
     * @throws Exception
     */
    public function isSpam(array $params)
    {
        $response = $this->makeApiCall('/1.1/comment-check', $params);

        if ('invalid' == $response) {
            throw new Exception('Invalid API key');
        }
        if ('true' == $response) {
            return true;
        }

        return false;
    }

    /**
     * submitSpam()
     *
     * @version 20120711
     * @param array $params
     * @throws Exception
     */
    public function submitSpam(array $params)
    {
        $this->makeApiCall('/1.1/submit-spam', $params);
    }

    /**
     * submitHam()
     *
     * @version 20120711
     * @param array $params
     * @throws Exception
     */
    public function submitHam(array $params)
    {
        $this->makeApiCall('/1.1/submit-ham', $params);
    }
}
