<?php

namespace FvCommunityNews;

use FvCommunityNews;
use FvCommunityNews\Syncer\Api\Api;
use FvCommunityNews\Syncer\Api\Exception as ApiException;
use FvCommunityNews\Syncer\Api\Request as ApiRequest;

/**
 * Version
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class Version
{
    /**
     * @var string
     */
    private static $latestVersion;

    /**
     * Get the current plugin version.
     *
     * @return string
     */
    public static function getCurrentVersion()
    {
        return FvCommunityNews::VERSION;
    }

    /**
     * Fetch the latest version from the api
     *
     * @return string
     */
    public static function getLatestVersion()
    {
        if (null === self::$latestVersion) {
            $apiRequest = new ApiRequest(Api::latestVersion());
            
            try {
                $response = $apiRequest->execute([
                    'blog_name'         => \get_bloginfo('name'),
                    'blog_description'  => \get_bloginfo('description'),
                    'blog_url'          => \get_bloginfo('url'),
                    'wordpress_url'     => \get_bloginfo('wpurl'),
                    'wordpress_version' => \get_bloginfo('version'),
                    'plugin_version'    => self::getCurrentVersion(),
                    'php_version'       => \phpversion(),
                ]);
            } catch (ApiException $e) {
                $response = null;
            }

            if (is_array($response) && 200 == $response['response']['code']) {
                $data = json_decode($response['body'], true);
                self::$latestVersion = $data['version'];
            }
        }

        return self::$latestVersion;
    }
}
