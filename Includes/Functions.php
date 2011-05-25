<?php

/**
 *		Functions.php
 *
 *		General Functions
 *
 *		@version 1.0
 */


/**
 *		fvcn_enqueue_files()
 *
 */
function fvcn_enqueue_files() {
	if (!is_admin()) {
		wp_enqueue_script('fvcn-add-post', FVCN_PLUGIN_URL . 'Public/javascript/add-post.js', array('jquery'), FVCN_VERSION);
		if (FvCommunityNews_Settings::getInstance()->Tracking) {
			wp_enqueue_script('fvcn-tracking', FVCN_PLUGIN_URL . 'Public/javascript/tracker.js', array('jquery'), FVCN_VERSION);
		}
		
		wp_enqueue_style('fvcn-default', FVCN_PLUGIN_URL . 'Public/css/styles.css', false, FVCN_VERSION);
	}
}
add_action('init', 'fvcn_enqueue_files');

/**
 *		fvcn_head()
 *
 */
function fvcn_head() {
	echo '<link rel="alternate" type="application/rss+xml" title="' . get_option('blogname') . ' &raquo; ' . __('FV Community News Feed', 'fvcn') . '" href="' . fvcn_get_rss_url() . '" />' . "\n";
}
add_action('wp_head', 'fvcn_head');

/**
 *		fvcn_get_setting()
 *
 *		@param string $name
 *		@return mixed
 */
function fvcn_get_setting($name) {
	return FvCommunityNews_Settings::getInstance()->get( $name );
}

/**
 *		fvcn_post_hooks()
 *
 *		@param string $content
 *		@return string
 */
function fvcn_post_hooks($content) {
	$hooks = apply_filters('fvcn_post_hooks', array(
		'<!--fvCommunityNews:Form-->'			=> 'Form',
		'<!--fvCommunityNews:Submissions-->'	=> 'ListPosts',
		'<!--fvCommunityNews:Posts-->'			=> 'ListPosts',
		'<!--fvCommunityNews:Archive-->'		=> 'PostsArchive'
	));
	$template = FvCommunityNews_Template::getInstance();
	
	foreach ($hooks as $hook=>$templateFile) {
		if (strstr($content, $hook)) {
			// TODO: Fix this without using ob_
			ob_start();
				$template->render( $templateFile );
				$data = ob_get_contents();
			ob_end_clean();
			
			$content = str_replace($hook, $data, $content);
		}
	}
	
	return $content;
}
add_filter('the_content', 'fvcn_post_hooks');

/**
 *		fvcn_install()
 *
 */
function fvcn_install() {
	$install = new FvCommunityNews_Install();
	
	if (!$install->isInstalled() || !$install->isCurrentVersion()) {
		$install->installSettings()
				->installDatabase();
	}
	
}
add_action('fvcn_activation', 'fvcn_install', 10);


/**
 *		fvcn_register_widgets()
 *
 *		@uses register_widget()
 */
function fvcn_register_widgets() {
	register_widget('FvCommunityNews_Widgets_Form');
	register_widget('FvCommunityNews_Widgets_ListPosts');
}
add_action('widgets_init', 'fvcn_register_widgets');

