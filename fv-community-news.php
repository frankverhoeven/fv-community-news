<?php

/**
 * Plugin Name: FV Community News
 * Plugin URI:  https://frankverhoeven.me/wordpress-plugin-fv-community-news/
 * Description: Allow visitors of your site to submit articles.
 * Version:     3.1
 * Author:      Frank Verhoeven
 * Author URI:  https://frankverhoeven.me/
 */

use FvCommunityNews\Application\Application;
use FvCommunityNews\Options;
use FvCommunityNews\Registry;

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
    const VERSION = '3.1';
    /**
     * @var string
     */
    const DIR = __DIR__;
    /**
     * @var string
     */
    const FILE = __FILE__;

    /**
     * __construct()
     *
     * @version 20120709
     */
    public function __construct()
    {}

    /**
     * start()
     *
     * @version 20120710
     */
    public function start()
    {
        $this->loadFiles()
             ->setupVariables();

        $app = new Application(include __DIR__ . '/config/default.config.php');
        $app->run();
    }

    /**
     * loadFiles()
     *
     * @version 20120716
     * @return FvCommunityNews
     * @throws Exception
     */
    private function loadFiles()
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

        return $this;
    }

    /**
     * setupVariables()
     *
     * @todo: remove
     *
     * @version 20120710
     * @return FvCommunityNews
     */
    private function setupVariables()
    {
        $pluginDir = plugin_dir_path(__FILE__);
        $pluginUrl = plugin_dir_url(__FILE__);
        $baseSlug = Options::fvcnGetOption('_fvcn_base_slug');

        Registry::setInstance(new Registry([
            'pluginDir' => $pluginDir,
            'pluginUrl' => $pluginUrl,

            'themeDir' => $pluginDir . 'fvcn-theme',
            'themeUrl' => $pluginUrl . 'fvcn-theme',

            'postSlug' => $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_slug'),
            'postTagSlug' => $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_tag_slug'),
            'postArchiveSlug'=> $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_archive_slug'),
        ]));

        return $this;
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
