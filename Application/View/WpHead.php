<?php

$dir = WP_PLUGIN_URL . FVCN_PLUGINDIR;
$wp_rewrite = fvCommunityNewsRegistry::get('wp_rewrite');

wp_enqueue_script('jquery');
wp_enqueue_script('fvcn-front', WP_PLUGIN_URL . FVCN_PLUGINDIR . '/public/javascript/general.js', 'jquery');

if (get_option('fvcn_rssEnabled')) {
	if ($wp_rewrite->using_permalinks())
		$location = get_option('home') . '/' . str_replace('feed/%feed%', '', $wp_rewrite->get_feed_permastruct());
	else
		$location = get_option('home') . '/?feed=';
	$location .= fvCommunityNewsSettings::getInstance()->get('RssLocation');
	
	echo '<link rel="alternate" type="application/rss+xml" title="' . get_option('blogname') . ' Community News RSS Feed" href="' . $location . '" />';
}
if (fvCommunityNewsSettings::getInstance()->get('IncStyle')) {
	echo '<link rel="stylesheet" type="text/css" href="' . $dir . '/public/css/form/add-submission.css" />';
	echo '<link rel="stylesheet" type="text/css" href="' . $dir . '/public/css/archive.css" />';
	
}

?> 
<meta name="Community-News-Generator" content="FV Community News - <?php echo fvCommunityNewsSettings::getInstance()->get('fvcn_version'); ?>" />
