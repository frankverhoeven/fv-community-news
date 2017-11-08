<?php

/**
 *	fvcn-core-ajax.php
 *
 *	AJAX Handler
 *
 *	@package	FV Community News
 *	@subpackage	Javascript
 *	@author		Frank Verhoeven <hi@frankverhoeven.me>
 */

if (!defined('ABSPATH')) {
	die('Direct access is not allowed!');
}


/**
 * FvCommunityNews_Javascript
 *
 */
class FvCommunityNews_Javascript
{
	/**
	 * @var array
	 */
	protected $_jsParams = [];
	
	/**
	 * __construct()
	 * 
	 * @version 20120712
	 * @return void
	 */
	public function __construct()
	{
		$this->_jsParams = [
			'ajaxurl'	=> esc_url( admin_url('admin-ajax.php') ),
			'nonce'		=> wp_create_nonce('fvcn-ajax'),
			'action'	=> 'fvcn-ajax',
			'thumbnail'	=> fvcn_is_post_form_thumbnail_enabled() ? '1' : '0',
			'locale'	=> [
				'loading'	=> __('Loading', 'fvcn')
            ]
        ];
		
		add_action('wp_ajax_fvcn-ajax',			[$this, 'response']);
		add_action('wp_ajax_nopriv_fvcn-ajax',	[$this, 'response']);
	}
	
	/**
	 * enqueueScripts()
	 * 
	 * @version 20120717
	 * @return FvCommunityNews_Javascript
	 */
	public function enqueueScripts()
	{
		// Replace the outdated version of jQuery Form that is shipped with WordPress
		wp_deregister_script('jquery-form');
		wp_register_script('jquery-form', FvCommunityNews_Registry::get('pluginUrl') . 'fvcn-includes/js/jquery-form.js');
		
		wp_enqueue_script('fvcn-js', FvCommunityNews_Registry::get('pluginUrl') . 'fvcn-includes/js/fvcn-js.js', ['jquery', 'jquery-form']);
		
		wp_localize_script('fvcn-js', 'FvCommunityNewsJavascript', $this->_jsParams);
		
		return $this;
	}
	
	public function response()
	{
		if (!wp_verify_nonce($_POST['nonce'], 'fvcn-ajax')) {
			exit;
		}
		
		$postId = fvcn_new_post_handler();
		
		if (fvcn_has_errors()) {
			$errors = [];
			foreach (FvCommunityNews_Container::getInstance()->getWpError()->get_error_codes() as $code) {
				$errors[ $code ] = FvCommunityNews_Container::getInstance()->getWpError()->get_error_message($code);
			}
			
			$response = [
				'success' => 'false',
				'errors'  => $errors
            ];
		} else {
			if (fvcn_get_public_post_status() == fvcn_get_post_status($postId)) {
				$permalink = fvcn_get_post_permalink($postId);
				$message   = '';
			} else {
				$permalink = '';
				$message   = __('Your post has been added and is pending review.', 'fvcn');
			}
			
			$response = [
				'success'	=> 'true',
				'permalink'	=> $permalink,
				'message'	=> $message
            ];
		}
		
		die( json_encode($response) );
	}
}


/**
 * fvcn_javascript()
 * 
 * @version 20120714
 * @return void
 */
function fvcn_javascript()
{
	add_action('fvcn_enqueue_scripts', [FvCommunityNews_Container::getInstance()->getJavascript(), 'enqueueScripts']);
}

