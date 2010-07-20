<?php
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
	
	<channel>
		<title><?php bloginfo_rss('name'); wp_title_rss(); ?> - Community News</title>
		<link><?php bloginfo_rss('url') ?></link>
		<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
		<description><?php bloginfo_rss("description") ?></description>
		<language><?php echo get_option('rss_language'); ?></language>
		<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
		<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
		<generator>Fv Community News</generator>
		
		<?php
		if (!$this->submissions) {
			echo '<!-- No submissions found. //-->';
		} else {
			foreach ($this->submissions as $item) :
			?>
			
			<item>
				<title><?php echo apply_filters('fvcn_Title', $item->Title); ?></title>
				<link><?php echo apply_filters('fvcn_Location', $item->Location); ?></link>
				<dc:creator><?php echo apply_filters('fvcn_Name', $item->Name); ?></dc:creator>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $item->Date); ?></pubDate>
				<description><![CDATA[<?php echo apply_filters('fvcn_Description', $item->Description); ?>]]></description>
				<content:encoded><![CDATA[<?php echo apply_filters('fvcn_Description', $item->Description); ?>]]></content:encoded>
			</item>
			
			<?php
			endforeach;
		}
		?>
		
	</channel>
</rss>
