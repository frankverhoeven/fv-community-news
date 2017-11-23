<?php

namespace FvCommunityNews\Akismet;

use Exception;
use FvCommunityNews\Container;

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
     */
    public function submitHam(array $params)
    {
        $this->makeApiCall('/1.1/submit-ham', $params);
    }


    /**
     * fvcn_akismet_check_post()
     *
     * @version 20120711
     * @param int $postId
     */
    public static function fvcn_akismet_check_post($postId)
    {
        try {
            Container::getInstance()->getAkismetHandler()->checkPost($postId);
        } catch (Exception $e) {}
    }

    /**
     * fvcn_akismet_submit_post()
     *
     * @version 20120711
     * @param int $postId
     */
    public static function fvcn_akismet_submit_post($postId)
    {
        try {
            Container::getInstance()->getAkismetHandler()->submitPost($postId);
        } catch (Exception $e) {}
    }

    /**
     * fvcn_akismet_register_settings()
     *
     * @version 20120711
     */
    public static function fvcn_akismet_register_settings()
    {
        Container::getInstance()->getAkismetHandler()->registerSettings();
    }

    /**
     * fvcn_akismet()
     *
     * @version 20171111
     */
    public static function fvcn_akismet()
    {
        if (!defined('AKISMET_VERSION')) {
            return;
        }

        if (get_option('_fvcn_akismet_enabled', false)) {
            add_action('fvcn_insert_post', [self::class, 'fvcn_akismet_check_post']);
            add_action('fvcn_spam_post', [self::class, 'fvcn_akismet_submit_post']);
            add_action('fvcn_publish_post', [self::class, 'fvcn_akismet_submit_post']);
        }

        if (is_admin()) {
            add_action('fvcn_register_admin_settings', [self::class, 'fvcn_akismet_register_settings']);
        }
    }
}
