<?php

/**
 * Plugin Name:	FV Community News
 * Plugin URI:	http://www.frank-verhoeven.com/wordpress-plugin-fv-community-news/
 * Description:	Allow visitors of your site to post articles.
 * Version:		3.0.2
 * Author:		Frank Verhoeven
 * Author URI:	http://www.frank-verhoeven.com/
 */

/**
 * fv-community-news.php
 *
 * Bootstrap
 *
 * @package		FV Community News
 * @subpackage	Bootstrap
 * @author		Frank Verhoeven <info@frank-verhoeven.com>
 */

if (!defined('ABSPATH')) {
	exit;
}


if (!class_exists('FvCommunityNews'))
{
	/**
	 * FvCommunityNews
	 *
	 */
	final class FvCommunityNews
	{
		/**
		 * @var string
		 */
		public $version	= '3.0.2';
		
		/**
		 * @var FvCommunityNews
		 */
		private static $_instance;
		
		
		/**
		 * __construct()
		 *
		 * @version 20120709
		 * @return void
		 */
		public function __construct()
		{
			
		}
		
		/**
		 * start()
		 *
		 * @version 20120710
		 * @return FvCommunityNews
		 */
		public function start()
		{
			$this->_loadFiles()
				 ->_setupVariables()
				 ->_setupActions();
		}
		
		/**
		 * _setupVariables()
		 *
		 * @version 20120710
		 * @return FvCommunityNews
		 */
		private function _setupVariables()
		{
			$pluginDir = plugin_dir_path(__FILE__);
			$pluginUrl = plugin_dir_url(__FILE__);
			$baseSlug  = fvcn_get_option('_fvcn_base_slug');
			
			FvCommunityNews_Registry::setInstance(new FvCommunityNews_Registry(array(
				'pluginDir'		=> $pluginDir,
				'pluginUrl'		=> $pluginUrl,
				
				'themeDir'		=> $pluginDir . 'fvcn-theme',
				'themeUrl'		=> $pluginUrl . 'fvcn-theme',
				
				'langDir'		=> $pluginDir . 'fvcn-languages',
				
				'postType'		=> apply_filters('fvcn_post_type',				'fvcn-post'	),
				'postTagId'		=> apply_filters('fvcn_post_tag_id',			'fvcn-tag'	),
				
				'psPublic'		=> apply_filters('fvcn_post_status_public',		'publish'	),
				'psTrash'		=> apply_filters('fvcn_post_status_trash',		'trash'		),
				'psPrivate'		=> apply_filters('fvcn_post_status_private',	'private'	),
				'psPending'		=> apply_filters('fvcn_post_status_pending',	'pending'	),
				'psSpam'		=> apply_filters('fvcn_post_status_spam',		'spam'		),
				
				'postSlug'		=> apply_filters('fvcn_post_slug',			$baseSlug . '/' . fvcn_get_option('_fvcn_post_slug')		),
				'postTagSlug'	=> apply_filters('fvcn_post_tag_slug',		$baseSlug . '/' . fvcn_get_option('_fvcn_post_tag_slug')	),
				'postArchiveSlug'=>apply_filters('fvcn_post_archive_slug',	$baseSlug . '/' . fvcn_get_option('_fvcn_post_archive_slug')),
			)));
			
			return $this;
		}
		
		/**
		 * _loadFiles()
		 *
		 * @version 20120716
		 * @return FvCommunityNews
		 */
		private function _loadFiles()
		{
			$files = array(
				'fvcn-includes/fvcn-core-hooks.php',
				'fvcn-includes/fvcn-core-classes.php',
				'fvcn-includes/fvcn-core-options.php',
				'fvcn-includes/fvcn-core-install.php',
				'fvcn-includes/fvcn-core-javascript.php',
				'fvcn-includes/fvcn-core-validate.php',
				'fvcn-includes/fvcn-core-widgets.php',
				'fvcn-includes/fvcn-core-shortcodes.php',
				'fvcn-includes/fvcn-core-theme.php',
				'fvcn-includes/fvcn-core-sync.php',
				'fvcn-includes/fvcn-common-functions.php',
				'fvcn-includes/fvcn-common-template.php',
				'fvcn-includes/fvcn-post-functions.php',
				'fvcn-includes/fvcn-post-template.php',
				'fvcn-includes/fvcn-tag-template.php',
				'fvcn-includes/fvcn-user-functions.php',
				'fvcn-includes/fvcn-user-template.php',
				'fvcn-includes/fvcn-deprecated-functions.php',
				'fvcn-includes/fvcn-extend-akismet.php'
			);
			
			if (is_admin()) {
				$files[] = 'fvcn-admin/fvcn-admin.php';
			}
			
			$dir = plugin_dir_path(__FILE__);
			foreach ($files as $file) {
				if (file_exists($dir . $file)) {
					require_once $dir . $file;
				} else {
					throw new Exception('The file "' . $file . '" was not found');
				}
			}
			
			return $this;
		}
		
		/**
		 * _setupActions()
		 *
		 * @version 20120710
		 * @return FvCommunityNews
		 */
		private function _setupActions()
		{
			register_activation_hook(  __FILE__, 'fvcn_activation'  );
			register_deactivation_hook(__FILE__, 'fvcn_deactivation');
			
			$actions = array(
				'register_post_type'	=> 'registerPostType',
				'register_post_statuses'=> 'registerPostStatuses',
				'register_taxonomy'		=> 'registerTaxonomy',
				'load_text_domain'		=> 'loadTextdomain'
			);
			
			foreach ($actions as $hook=>$method) {
				add_action('fvcn_' . $hook, array($this, $method), 5);
			}
			
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
			
			$mofile_local  = FvCommunityNews_Registry::get('langDir') . '/' . $mofile;
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
		
		/**
		 * registerPostType()
		 *
		 * @version 20120710
		 * @return FvCommunityNews
		 */
		public function registerPostType() {
			$post = array(
				'labels'	=> array(
					'name'				=> __('FV Community News',		'fvcn'),
					'menu_name'			=> __('Community News',			'fvcn'),
					'singular_name'		=> __('Community News',			'fvcn'),
					'all_items'			=> __('Community News',			'fvcn'),
					'add_new'			=> __('New Post',				'fvcn'),
					'add_new_item'		=> __('Create New Post',		'fvcn'),
					'edit'				=> __('Edit',					'fvcn'),
					'edit_item'			=> __('Edit Post',				'fvcn'),
					'new_item'			=> __('New Post',				'fvcn'),
					'view'				=> __('View Post',				'fvcn'),
					'view_item'			=> __('View Post',				'fvcn'),
					'search_items'		=> __('Search Community News',	'fvcn'),
					'not_found'			=> __('No posts found',			'fvcn'),
					'not_found_in_trash'=> __('No posts found in Trash','fvcn')
				),
				'rewrite'	=> array(
					'slug'			=> FvCommunityNews_Registry::get('postSlug'),
					'with_front'	=> false
				),
				'supports'	=> array(
					'title',
					'editor',
					'thumbnail',
					'comments'
				)
			);
			
			$options = apply_filters('fvcn_register_fvcn_post_type', array(
				'labels'				=> $post['labels'],
				'rewrite'				=> $post['rewrite'],
				'supports'				=> $post['supports'],
				'description'			=> __('FV Community News Posts', 'fvcn'),
				'has_archive'			=> FvCommunityNews_Registry::get('postArchiveSlug'),
				'public'				=> true,
				'publicly_queryable'	=> true,
				'can_export'			=> true,
				'hierarchical'			=> false,
				'query_var'				=> true,
				'exclude_from_search'	=> false,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'menu_position'			=> 20,
				'menu_icon'				=> '',
				'capability_type'		=> 'post',
			));
			
			register_post_type(FvCommunityNews_Registry::get('postType'), $options);
			
			return $this;
		}
		
		/**
		 * registerPostStatuses()
		 *
		 * @version 20120716
		 * @return FvCommunityNews
		 */
		public function registerPostStatuses() {
			$status = apply_filters('fvcn_register_spam_post_status', array(
				'label'						=> __('Spam', 'fvcn'),
				'label_count'				=> _nx_noop('Spam <span class="count">(%s)</span>', 'Spam <span class="count">(%s)</span>', 'fvcn'),
				'protected'                 => true,
				'exclude_from_search'		=> true,
				'show_in_admin_status_list'	=> true,
				'show_in_admin_all_list'	=> false
			));
			
			register_post_status(FvCommunityNews_Registry::get('psSpam'), $status);
			
			return $this;
		}
		
		/**
		 * registerTaxonomy()
		 *
		 * @version 20120716
		 * @return FvCommunityNews
		 */
		public function registerTaxonomy()
		{
			$tag = array(
				'labels'	=> array(
					'name'              => __('Tags',			'fvcn'),
					'singular_name'     => __('Tag',			'fvcn'),
					'search_items'      => __('Search Tags',	'fvcn'),
					'popular_items'     => __('Popular Tags',	'fvcn'),
					'all_items'         => __('All Tags',		'fvcn'),
					'edit_item'         => __('Edit Tag',		'fvcn'),
					'update_item'       => __('Update Tag',		'fvcn'),
					'add_new_item'      => __('Add New Tag',	'fvcn'),
					'new_item_name'     => __('New Tag Name',	'fvcn'),
				),
				'rewrite'	=> array(
					'slug'			=> FvCommunityNews_Registry::get('postTagSlug'),
					'with_front'	=> false
				)
			);
			
			$options = apply_filters('fvcn_register_fvcn_post_tag_id', array(
				'labels'		=> $tag['labels'],
				'rewrite'		=> $tag['rewrite'],
				'public'		=> true
			));
			
			register_taxonomy(
				FvCommunityNews_Registry::get('postTagId'),
				FvCommunityNews_Registry::get('postType'),
				$options
			);
			
			return $this;
		}
		
		
		/**
		 * setInstance()
		 *
		 * @version 20120710
		 * @param FvCommunityNews $instance
		 * @return FvCommunityNews
		 */
		public static function setInstance(FvCommunityNews $instance=null)
		{
			if (null === self::$_instance) {
				if (null === $instance) {
					self::$_instance = new FvCommunityNews();
				} else {
					self::$_instance = $instance;
				}
			}
		}
		
		/**
		 * getInstance()
		 *
		 * @version 20120710
		 * @return FvCommunityNews
		 */
		public static function getInstance()
		{
			self::setInstance();
			return self::$_instance;
		}
	}
	
	
	/**
	 * Lets roll
	 *
	 */
	try {
		FvCommunityNews::getInstance()->start();
	} catch (Exception $e) {
		error_log( 'fvcn error: ' . $e->getMessage() );
	}
}



/**
 *
 *		Q.E.D. (Quod Erat Demonstrandum)
 *
 */
