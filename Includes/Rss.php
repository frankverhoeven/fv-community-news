<?php

/**
 *		Rss.php
 *		FvCommunityNews_Rss
 *
 *		Community News RSS Feed
 *
 *		@version 1.0
 */

class FvCommunityNews_Rss {
	
	/**
	 *	RSS Location
	 *	@var string
	 */
	protected $_feedName = 'fv-community-news-rss';
	
	/**
	 *	Number of posts
	 *	@var int
	 */
	protected $_numPosts = 10;
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		
	}
	
	/**
	 *		setFeedName()
	 *
	 *		@param string $name
	 *		@return object $this
	 */
	public function setFeedName($name) {
		$this->_feedName = $name;
		return $this;
	}
	
	/**
	 *		getFeedName()
	 *
	 *		@return string
	 */
	public function getFeedName() {
		return $this->_feedName;
	}
	
	/**
	 *		setNumPosts()
	 *
	 *		@param int $num
	 *		@return object $this
	 */
	public function setNumPosts($num) {
		$this->_numPosts = $num;
		return $this;
	}
	
	/**
	 *		getNumPosts()
	 *
	 *		@return int
	 */
	public function getNumPosts() {
		return $this->_numPosts;
	}
	
	/**
	 *		addFeed()
	 *
	 */
	public function addFeed() {
		add_feed($this->getFeedName(), array($this, 'render'));
	}
	
	/**
	 *		render()
	 *
	 */
	public function render() {
		$options = apply_filters('fvcn_rss_options', array(
			'num'	=> $this->getNumPosts(),
		));
		
		header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
		
		?>
		<rss version="2.0"
			xmlns:content="http://purl.org/rss/1.0/modules/content/"
			xmlns:dc="http://purl.org/dc/elements/1.1/"
			xmlns:atom="http://www.w3.org/2005/Atom"
			xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
			
			<channel>
				<title><?php bloginfo_rss('name'); wp_title_rss(); ?> - <?php _e('FV Community News Feed', 'fvcn'); ?></title>
				<link><?php bloginfo_rss('url'); ?></link>
				<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
				<description><?php bloginfo_rss('description'); ?></description>
				<language><?php echo get_option('rss_language'); ?></language>
				<sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
				<sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
				<generator><?php _e('FV Community News', 'fvcn'); ?></generator>
				
				<?php
				if (fvcn_has_posts($options)) {
					while (fvcn_posts()) : fvcn_the_post();
					?>
					
					<item>
						<title><?php fvcn_post_title(); ?></title>
						<link><?php fvcn_post_url(); ?></link>
						<dc:creator><?php fvcn_post_author(); ?></dc:creator>
						<pubDate><?php fvcn_post_date('D, d M Y H:i:s +0000'); ?></pubDate>
						<description><![CDATA[<?php fvcn_post_content(); ?>]]></description>
						<content:encoded><![CDATA[<?php fvcn_post_content(); ?>]]></content:encoded>
					</item>
					
					<?php
					endwhile;
				} else {
					echo '<!-- No Community News found. //-->';
				}
				?>
				
			</channel>
		</rss>
		<?php
		
		exit;
	}
	
}

