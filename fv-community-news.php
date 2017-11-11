<?php

/**
 * Plugin Name: FV Community News
 * Plugin URI:  https://frankverhoeven.me/wordpress-plugin-fv-community-news/
 * Description: Allow visitors of your site to post articles.
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
             ->setupVariables()
             ->setupActions();

        $app = new Application();
        $app->run();
    }

    /**
     * setupVariables()
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

            'langDir' => $pluginDir . 'fvcn-languages',

            'postSlug' => $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_slug'),
            'postTagSlug' => $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_tag_slug'),
            'postArchiveSlug'=> $baseSlug . '/' . Options::fvcnGetOption('_fvcn_post_archive_slug'),
        ]));

        return $this;
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
            'fvcn-includes/fvcn-core-hooks.php',
            'fvcn-includes/fvcn-core-theme.php',
            'fvcn-includes/fvcn-common-functions.php',
            'fvcn-includes/fvcn-post-functions.php',

            'src/Template/common-functions.php',
            'src/Template/options-functions.php',
            'src/Template/post-functions.php',
            'src/Template/tag-functions.php',
            'src/Template/user-functions.php',
        ];

        $dir = plugin_dir_path(__FILE__);
        foreach ($files as $file) {
            $autoloader->loadFile($dir . $file);
        }

        return $this;
    }

    /**
     * setupActions()
     *
     * @version 20120710
     * @return FvCommunityNews
     */
    private function setupActions()
    {
        register_activation_hook(__FILE__, 'fvcn_activation');
        register_deactivation_hook(__FILE__, 'fvcn_deactivation');

        add_action('fvcn_load_text_domain', [$this, 'loadTextdomain'], 5);

        return $this;
    }

    /**
     * loadTextdomain()
     *
     * @version 20120710
     * @return bool
     */
    public function loadTextdomain()
    {
        $locale = apply_filters('fvcn_locale', get_locale());

        $mofile = sprintf('fvcn-%s.mo', $locale);

        $mofile_local = Registry::get('langDir') . '/' . $mofile;
        $mofile_global = WP_LANG_DIR . '/fv-community-news/' . $mofile;

        // /wp-content/plugins/fv-community-news/fvcn-languages/
        if (file_exists($mofile_local)) {
            return load_textdomain('fvcn', $mofile_local);

        // /wp-content/languages/fv-community-news/
        } elseif (file_exists($mofile_global)) {
            return load_textdomain('fvcn', $mofile_global);
        }

        return false;
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
 *
 *     Q.E.D. (Quod Erat Demonstrandum)
 *
 */
