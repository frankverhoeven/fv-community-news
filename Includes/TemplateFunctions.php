<?php

/**
 *		Template.php
 *
 *		Template functions
 *
 *		@version 1.0
 */


/**
 *
 *		Post Functions
 *
 */

/**
 *		fvcn_has_posts()
 *
 *		@param mixed $args
 *		@return bool
 */
function fvcn_has_posts($args='') {
	global $wpdb;
	
	$default = array(
		'start'		=> 0,
		'num'		=> 5,
		'approved'	=> '1',
		'order'		=> array('Date' => 'DESC'),
		'where'		=> array(),
	);
	
	$args = apply_filters('fvcn_has_posts_options', wp_parse_args($args, $default));
	
	$registry = FvCommunityNews_Registry::getInstance();
	$mapper = new FvCommunityNews_Models_PostMapper($wpdb);
	
	$options = array_merge(array('Approved' => $args['approved']), $args['where']);
	
	$posts = $mapper->getAll($args['start'], $args['num'], $options, $args['order']);
	
	if ($posts) {
		$registry->Posts = $posts;
		$registry->PostNum = -1;
		
		return true;
	}
	
	return false;
}

/**
 *		fvcn_posts()
 *
 *		@return bool
 */
function fvcn_posts() {
	$reg = FvCommunityNews_Registry::getInstance();
	$reg->PostNum++;
	
	if (array_key_exists($reg->PostNum, $reg->Posts)) {
		return true;
	}
	
	return false;
}

/**
 *		fvcn_the_post()
 *
 *		@return FvCommunityNews_Models_Post
 */
function fvcn_the_post() {
	$reg = FvCommunityNews_Registry::getInstance();
	return $reg->CurrentPost = $reg->Posts[ $reg->PostNum ];
}

/**
 *		fvcn_get_post_id()
 *
 *		@return int
 */
function fvcn_get_post_id() {
	return apply_filters('fvcn_post_id', FvCommunityNews_Registry::getInstance()->CurrentPost->getId());
}

/**
 *		fvcn_post_id()
 *
 */
function fvcn_post_id() {
	echo fvcn_get_post_id();
}

/**
 *		fvcn_get_post_author()
 *
 *		@return string
 */
function fvcn_get_post_author() {
	return apply_filters('fvcn_post_author', FvCommunityNews_Registry::getInstance()->CurrentPost->getAuthor());
}

/**
 *		fvcn_post_author()
 *
 */
function fvcn_post_author() {
	echo fvcn_get_post_author();
}

/**
 *		fvcn_get_post_author_email()
 *
 *		@return string
 */
function fvcn_get_post_author_email() {
	return apply_filters('fvcn_post_author_email', FvCommunityNews_Registry::getInstance()->CurrentPost->getAuthorEmail());
}

/**
 *		fvcn_post_author_email()
 *
 */
function fvcn_post_author_email() {
	echo fvcn_get_post_author_email();
}

/**
 *		fvcn_get_post_status()
 *
 *		@return string
 */
function fvcn_get_post_status() {
	return apply_filters('fvcn_post_status', FvCommunityNews_Registry::getInstance()->CurrentPost->getApproved());
}

/**
 *		fvcn_post_status()
 *
 */
function fvcn_post_status() {
	echo fvcn_get_post_status();
}

/**
 *		fvcn_get_post_author_ip()
 *
 *		@return string
 */
function fvcn_get_post_author_ip() {
	return apply_filters('fvcn_post_author_ip', FvCommunityNews_Registry::getInstance()->CurrentPost->getAuthorIp());
}

/**
 *		fvcn_post_author_ip()
 *
 */
function fvcn_post_author_ip() {
	echo fvcn_get_post_author_ip();
}

/**
 *		fvcn_get_post_title()
 *
 *		@return string
 */
function fvcn_get_post_title() {
	return apply_filters('fvcn_post_title', FvCommunityNews_Registry::getInstance()->CurrentPost->getTitle());
}

/**
 *		fvcn_post_title()
 *
 */
function fvcn_post_title() {
	echo fvcn_get_post_title();
}

/**
 *		fvcn_get_post_content()
 *
 *		@return string
 */
function fvcn_get_post_content() {
	return apply_filters('fvcn_post_content', FvCommunityNews_Registry::getInstance()->CurrentPost->getContent());
}

/**
 *		fvcn_post_content()
 *
 */
function fvcn_post_content() {
	echo fvcn_get_post_content();
}

/**
 *		fvcn_get_post_excerpt()
 *
 *		@param int $length
 *		@return string
 */
function fvcn_get_post_excerpt($length=20) {
	$content = strip_tags(FvCommunityNews_Registry::getInstance()->CurrentPost->getContent());
	$content = preg_split("/[\n\r\t ]+/", $content, $length + 1, PREG_SPLIT_NO_EMPTY);
	
	if (count($content) > $length) {
		array_pop($content);
		$excerpt = implode(' ', $content);
		$excerpt = $excerpt . '...';
	} else {
		$excerpt = implode(' ', $content);
	}
	
	return apply_filters('fvcn_post_excerpt', $excerpt);
}

/**
 *		fvcn_post_excerpt()
 *
 */
function fvcn_post_excerpt() {
	echo fvcn_get_post_excerpt();
}

/**
 *		fvcn_get_post_url()
 *
 *		@return string
 */
function fvcn_get_post_url() {
	return apply_filters('fvcn_post_url', FvCommunityNews_Registry::getInstance()->CurrentPost->getUrl());
}

/**
 *		fvcn_has_post_url()
 *
 *		@return bool
 */
function fvcn_has_post_url() {
	return ('' != fvcn_get_post_url());
}

/**
 *		fvcn_post_url()
 *
 */
function fvcn_post_url() {
	echo fvcn_get_post_url();
}

/**
 *		fvcn_get_post_link()
 *
 *		@param string $text
 *		@return string
 */
function fvcn_get_post_link($text) {
	if (fvcn_has_post_url()) {
		$link = '<a href="' . fvcn_get_post_url() . '" title="' . $text . '">' . $text . '</a>';
	} else {
		$link = $text;
	}
	
	return apply_filters('fvcn_post_link', $link);
}

/**
 *		fvcn_post_link()
 *
 *		@param string $text
 */
function fvcn_post_link($text) {
	echo fvcn_get_post_link($text);
}

/**
 *		fvcn_get_post_title_link()
 *
 *		@return string
 */
function fvcn_get_post_title_link() {
	return apply_filters('fvcn_post_title', FvCommunityNews_Registry::getInstance()->CurrentPost->getTitle());
}

/**
 *		fvcn_post_title_link()
 *
 */
function fvcn_post_title_link() {
	echo fvcn_get_post_title();
}

/**
 *		fvcn_get_post_date()
 *
 *		@param string $format
 *		@return string
 */
function fvcn_get_post_date($format='') {
	$date = FvCommunityNews_Registry::getInstance()->CurrentPost->getDate();
	
	if ('' == $format) {
		$date = mysql2date(get_option('date_format'), $date);
	} else {
		$date = mysql2date($format, $date);
	}
	
	return apply_filters('fvcn_post_date', $date);
}

/**
 *		fvcn_post_date()
 *
 *		@param string $format
 */
function fvcn_post_date($format='') {
	echo fvcn_get_post_date($format);
}

/**
 *		fvcn_get_post_views()
 *
 *		@return int
 */
function fvcn_get_post_views() {
	return apply_filters('fvcn_post_views', FvCommunityNews_Registry::getInstance()->CurrentPost->getViews());
}

/**
 *		fvcn_post_views()
 *
 */
function fvcn_post_views() {
	echo fvcn_get_post_views();
}

/**
 *		fvcn_get_post_approved()
 *
 *		@return string
 */
function fvcn_get_post_approved() {
	return apply_filters('fvcn_post_approved', FvCommunityNews_Registry::getInstance()->CurrentPost->getApproved());
}

/**
 *		fvcn_post_approved()
 *
 */
function fvcn_post_approved() {
	echo fvcn_get_post_approved();
}

/**
 *		fvcn_get_post_approve_link()
 *
 */
function fvcn_get_post_approve_link() {
	return apply_filters('fvcn_post_approve_link',
		esc_url(
			add_query_arg(array(
				'fvcn'			=> 'fvcn-moderate-posts',
				'fvcn-nonce'	=> wp_create_nonce('fvcn-nonce'),
				'fvcn-action'	=> 'approve',
				'fvcn-post-id'	=> fvcn_get_post_id()
			))
		)
	);
}

/**
 *		fvcn_post_approve_link()
 *
 */
function fvcn_post_approve_link() {
	echo fvcn_get_post_approve_link();
}

/**
 *		fvcn_get_post_unapprove_link()
 *
 */
function fvcn_get_post_unapprove_link() {
	return apply_filters('fvcn_post_unapprove_link',
		esc_url(
			add_query_arg(array(
				'fvcn'			=> 'fvcn-moderate-posts',
				'fvcn-nonce'	=> wp_create_nonce('fvcn-nonce'),
				'fvcn-action'	=> 'unapprove',
				'fvcn-post-id'	=> fvcn_get_post_id()
			))
		)
	);
}

/**
 *		fvcn_post_unapprove_link()
 *
 */
function fvcn_post_unapprove_link() {
	echo fvcn_get_post_unapprove_link();
}

/**
 *		fvcn_get_post_edit_link()
 *
 */
function fvcn_get_post_edit_link() {
	return apply_filters('fvcn_post_edit_link',
		esc_url(
			add_query_arg(array(
				'fvcn-action'	=> 'edit',
				'fvcn-post-id'	=> fvcn_get_post_id()
			),
			remove_query_arg(
				array('apage', '_wpnonce')
			))
		)
	);
}

/**
 *		fvcn_post_edit_link()
 *
 */
function fvcn_post_edit_link() {
	echo fvcn_get_post_edit_link();
}

/**
 *		fvcn_get_post_spam_link()
 *
 */
function fvcn_get_post_spam_link() {
	return apply_filters('fvcn_post_spam_link',
		esc_url(
			add_query_arg(array(
				'fvcn'			=> 'fvcn-moderate-posts',
				'fvcn-nonce'	=> wp_create_nonce('fvcn-nonce'),
				'fvcn-action'	=> 'spam',
				'fvcn-post-id'	=> fvcn_get_post_id()
			))
		)
	);
}

/**
 *		fvcn_post_spam_link()
 *
 */
function fvcn_post_spam_link() {
	echo fvcn_get_post_spam_link();
}

/**
 *		fvcn_get_post_unspam_link()
 *
 */
function fvcn_get_post_unspam_link() {
	return apply_filters('fvcn_post_unspam_link',
		esc_url(
			add_query_arg(array(
				'fvcn'			=> 'fvcn-moderate-posts',
				'fvcn-nonce'	=> wp_create_nonce('fvcn-nonce'),
				'fvcn-action'	=> 'unspam',
				'fvcn-post-id'	=> fvcn_get_post_id()
			))
		)
	);
}

/**
 *		fvcn_post_unspam_link()
 *
 */
function fvcn_post_unspam_link() {
	echo fvcn_get_post_unspam_link();
}

/**
 *		fvcn_get_post_delete_link()
 *
 */
function fvcn_get_post_delete_link() {
	return apply_filters('fvcn_post_delete_link',
		esc_url(
			add_query_arg(array(
				'fvcn'			=> 'fvcn-moderate-posts',
				'fvcn-nonce'	=> wp_create_nonce('fvcn-nonce'),
				'fvcn-action'	=> 'delete',
				'fvcn-post-id'	=> fvcn_get_post_id()
			))
		)
	);
}

/**
 *		fvcn_post_delete_link()
 *
 */
function fvcn_post_delete_link() {
	echo fvcn_get_post_delete_link();
}



/**
 *
 *		Form Functions
 *
 */

/**
 *		fvcn_get_form()
 *
 *		@return string
 */
function fvcn_get_form() {
	if (fvcn_get_setting('LoggedInToPost') && !is_user_logged_in()) {
		return apply_filters('fvcn_form', '<p>' . __('You have to be logged in to add a post.', 'fvcn') . '</p>');
	} else {
		return apply_filters('fvcn_form', FvCommunityNews_Registry::getInstance()->forms['add-post']->render());
	}
}

/**
 *		fvcn_form()
 *
 */
function fvcn_form() {
	echo fvcn_get_form();
}

/**
 *		fvcn_get_form_message()
 *
 *		@return string
 */
function fvcn_get_form_message() {
	return apply_filters('fvcn_form_message', FvCommunityNews_Registry::getInstance()->forms['add-post']->getMessage());
}

/**
 *		fvcn_form_message()
 *
 */
function fvcn_form_message() {
	echo fvcn_get_form_message();
}

/**
 *		fvcn_form_processed()
 *
 *		@return bool
 */
function fvcn_form_processed() {
	return FvCommunityNews_Registry::getInstance()->forms['add-post']->isProcessed();
}



/**
 *
 *		Complete Template Parts
 *
 */


/****
 *		Deprecated
 */

/**
 *		fvCommunityNewsForm()
 *
 */
function fvCommunityNewsForm() {
	trigger_error('The function "fvCommunityNewsForm()" is deprecated, use "fvcn_form()" instead!', E_USER_NOTICE);
	FvCommunityNews_Template::getInstance()->render('Form');
}

/**
 *		fvCommunityNewsGetSubmissions()
 *
 *		@param int $num
 *		@param string $format
 *		@return string
 */
function fvCommunityNewsGetSubmissions($num=5, $format='<li><strong><a href="%submission_url%" title="%submission_title%">%submission_title%</a></strong><small>%submission_date%</small><br />%submission_description%</li>') {
	trigger_error('The function "fvCommunityNewsGetSubmissions()" is deprecated, use "fvcn_list_posts()" instead!', E_USER_NOTICE);
	
	if (!fvcn_has_posts(array('num'=>$num))) {
		
		return '<p>' . __('No Community News found.', 'fvcn') . '</p>';
		
	} else {
		$posts = '<ul>';
		
		while (fvcn_posts()) {
			fvcn_the_post();
			
			$post = $format;
			$post = str_replace('%submission_author%',			fvcn_get_post_author(),			$post);
			$post = str_replace('%submission_author_email%',	fvcn_get_post_author_email(),	$post);
			$post = str_replace('%submission_title%',			fvcn_get_post_title(),			$post);
			$post = str_replace('%submission_url%',				fvcn_get_post_url(),			$post);
			$post = str_replace('%submission_description%',		fvcn_get_post_content(),		$post);
			$post = str_replace('%submission_date%',			fvcn_get_post_date(),			$post);
			$post = str_replace('%submission_image%',			'',								$post);
			
			$posts .= $post;
		}
		
		$posts .= '</ul>';
		
		return $posts;
	}
}

/****
 *		End Deprecated
 */


/**
 *		fvcn_list_posts()
 *
 */
function fvcn_list_posts() {
	FvCommunityNews_Template::getInstance()->render('ListPosts');
}

/**
 *		fvcn_post_archives()
 *
 */
function fvcn_post_archives() {
	FvCommunityNews_Template::getInstance()->render('PostArchive');
}



/**
 *
 *		Other Template Functions
 *
 */

/**
 *		fvcn_get_rss_url()
 *
 *		@return string
 */
function fvcn_get_rss_url() {
	global $wp_rewrite;
	$rss = FvCommunityNews_Registry::getInstance()->rss;
	
	$feed = get_option('home');
	if ($wp_rewrite->using_permalinks()) {
		$feed .= '/' . $rss->getFeedName();
	} else {
		$feed .= '/?feed=' . $rss->getFeedName();
	}
	
	return apply_filters('fvcn_rss_url', $feed);
}

/**
 *		fvcn_rss_url()
 *
 */
function fvcn_rss_url() {
	echo fvcn_get_rss_url();
}






