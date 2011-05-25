<?php

/**
 *		Hooks.php
 *		FvCommunityNews_Hooks
 *
 *		Actions & Filters
 *
 *		@version 1.0
 */

class FvCommunityNews_Hooks {
	
	/**
	 *	Prefix
	 *	@var string
	 */
	private static $_filterPrefix = 'fvcn_';
	
	/**
	 *	Default Filters
	 *	@var array
	 */
	private static $_defaultFilters = array(
		'post_author'	=> array(
			array('sanitize_text_field'),
			array('wp_kses_data'),
			array('_wp_specialchars', 30),
		),
		'post_author_email'	=> array(
			array('sanitize_email'),
			array('wp_kses_data'),
		),
		'post_title'	=> array(
			array('sanitize_text_field'),
			array('wp_kses_data'),
			array('stripslashes', 12),
			array('_wp_specialchars', 30),
		),
		'post_content'	=> array(
			array('wptexturize'),
			array('convert_chars'),
			array('wp_filter_kses'),
			array('make_clickable', 9),
			array('wp_rel_nofollow', 10),
			array('stripslashes', 12),
			array('force_balance_tags', 25),
			array('wpautop', 30),
		),
		'post_excerpt'	=> array(
			array('wptexturize'),
			array('convert_chars'),
			array('wp_filter_kses'),
			array('make_clickable', 9),
			array('wp_rel_nofollow', 10),
			array('stripslashes', 12),
			array('wpautop', 30),
		),
		'post_url'	=> array(
			array('wp_strip_all_tags'),
			array('esc_url'),
			array('wp_kses_data'),
			array('stripslashes', 12),
		),
		'post_pre_author'	=> array(
			array('sanitize_text_field'),
			array('wp_filter_kses'),
			array('_wp_specialchars', 30),
		),
		'post_pre_author_email'	=> array(
			array('trim'),
			array('sanitize_email'),
			array('wp_filter_kses', 30),
		),
		'post_pre_title'	=> array(
			array('sanitize_text_field'),
			array('wp_filter_kses'),
			array('_wp_specialchars', 30),
		),
		'post_pre_content'	=> array(
			array('balanceTags', 50),
		),
		'post_pre_url'	=> array(
			array('wp_strip_all_tags'),
			array('esc_url_raw'),
			array('wp_filter_kses'),
		),
	);
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		
	}
	
	/**
	 *		hookFilters()
	 *
	 *		@uses add_filter()
	 */
	public static function hookFilters() {
		foreach (self::$_defaultFilters as $hook=>$filters) {
			if (!is_array($filters)) {
				throw new Exception('Invallid filters provided');
			}
			
			foreach ($filters as $filter) {
				if (!is_array($filter)) {
					throw new Exception('Invallid flter provided');
				}
				
				if (2 == count($filter)) {
					add_filter(self::$_filterPrefix . $hook, $filter[0], $filter[1]);
				} else {
					add_filter(self::$_filterPrefix . $hook, $filter[0]);
				}
			}
		}
	}
	
	
}

