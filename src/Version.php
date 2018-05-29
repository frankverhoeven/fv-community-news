<?php

declare(strict_types=1);

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
    private static $currentVersion;

    /**
     * @var string
     */
    private static $latestVersion;

    /**
     * Get the current plugin version.
     *
     * @return string
     */
    public static function getCurrentVersion(): string
    {
        if (null === self::$currentVersion) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            $reflection = new \ReflectionClass(FvCommunityNews::class);
            $data = \get_plugin_data($reflection->getFileName());
            self::$currentVersion = $data['Version'];
        }

        return self::$currentVersion;
    }

    /**
     * Fetch the latest version from the api
     *
     * @return string
     */
    public static function getLatestVersion(): string
    {
        if (null === self::$latestVersion) {
            $apiRequest = new ApiRequest(Api::latestVersion());
            
            try {
                $response = $apiRequest->execute();
            } catch (ApiException $e) {
                $response = null;
            }

            if (\is_array($response) && 200 == $response['response']['code']) {
                $data = \json_decode($response['body'], true);
                self::$latestVersion = $data['version'];
            }
        }

        return self::$latestVersion;
    }
}
