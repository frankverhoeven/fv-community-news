<?php

/**
 * Plugin Name: FV Community News
 * Plugin URI:  https://frankverhoeven.me/wordpress-plugin-fv-community-news/
 * Description: Allow visitors of your site to submit articles.
 * Version:     3.1.2
 * Author:      Frank Verhoeven
 * Author URI:  https://frankverhoeven.me/
 */

use FvCommunityNews\Application\Application;
use FvCommunityNews\Container\Container;

if (!defined('ABSPATH')) exit;

/**
 * FvCommunityNews
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class FvCommunityNews
{
    /**
     * @var string
     */
    const VERSION = '3.1.2';
    /**
     * @var string
     */
    const DIR = __DIR__;
    /**
     * @var string
     */
    const FILE = __FILE__;
    /**
     * @var Container
     */
    public static $container;

    /**
     * __construct()
     *
     * @version 20180119
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, [static::class, 'activation']);
        register_deactivation_hook(__FILE__, [static::class, 'deactivation']);
    }

    /**
     * start()
     *
     * @version 20180119
     */
    public function start()
    {
        $this->loadFiles();

        $app = new Application(include __DIR__ . '/config/default.config.php');
        $app->run();
    }

    /**
     * loadFiles()
     *
     * @version 20180119
     * @return void
     */
    private function loadFiles(): void
    {
        include_once __DIR__ . '/src/Autoloader.php';

        $autoloader = new \FvCommunityNews\AutoLoader(['FvCommunityNews' => __DIR__ . '/src/']);
        $autoloader->register();

        $files = [
            '/fvcn-includes/fvcn-core-theme.php',
            '/fvcn-includes/fvcn-common-functions.php',
            '/fvcn-includes/fvcn-post-functions.php',

            '/src/Template/common-functions.php',
            '/src/Template/options-functions.php',
            '/src/Template/post-functions.php',
            '/src/Template/tag-functions.php',
            '/src/Template/user-functions.php',
        ];

        foreach ($files as $file) {
            $autoloader->loadFile(__DIR__ . $file);
        }
    }

    /**
     * Activation Hook
     *
     * @return void
     */
    public static function activation(): void
    {
        do_action('fvcn_activation');
        register_uninstall_hook(__FILE__, [static::class, 'uninstall']);
    }

    /**
     * Deactivation Hook
     *
     * @return void
     */
    public static function deactivation(): void
    {
        do_action('fvcn_deactivation');
    }

    /**
     * Uninstall Hook
     *
     * @return void
     */
    public static function uninstall(): void
    {
        do_action('fvcn_uninstall');
    }
}


/**
 * Lets roll
 *
 */
try {
    $fvcn = new FvCommunityNews();
    $fvcn->start();
} catch (Exception $e) {
    if (defined('WP_DEBUG') && true === WP_DEBUG) {
        echo '<h3>' . $e->getMessage() . '</h3><pre>' . $e->getTraceAsString() . '</pre>';
    }

    error_log('fvcn: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
}


/**
 *  Q.E.D. (Quod Erat Demonstrandum)
 */
