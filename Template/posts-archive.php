<?php

// The number of posts on one page
$postsPerPage = 10;



// DO NOT EDIT BELOW!

global $wp_rewrite, $wp_query;
$mapper = new FvCommunityNews_Models_PostMapper();

if ($wp_rewrite->using_permalinks()) {
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
} else {
	(int)@$_GET['page']  > 1 ? $current = (int)$_GET['page'] : $current = 1;
}
$start = ($current - 1) * $postsPerPage;
$total = $mapper->getCount(array('Approved'=>'1'));

$pagination = array(
	'base' => @add_query_arg('page', '%#%'),
	'format' => '',
	'total' => ceil($total / $postsPerPage),
	'current' => $current,
	'next_text' => __('Next &raquo;', 'fvcn'),
	'prev_text' => __('&laquo; Previous', 'fvcn'),
);
if ($wp_rewrite->using_permalinks()) {
	$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');
}

$pagination = paginate_links($pagination);
$options = array(
	'num'	=> $postsPerPage,
	'start'	=> $start
);

// You can start editing here
?>



<?php if (fvcn_has_posts($options)) : ?>
	
	<div class="fvcn-pagination">
		<?php echo $pagination; ?>
	</div>
	
	<ul class="fvcn-posts-list">
		<?php while (fvcn_posts()) : fvcn_the_post(); ?>
			
			<li>
				<h4><?php fvcn_post_link( fvcn_get_post_title() ); ?></h4>
				<?php fvcn_post_content(); ?>
				<small><?php fvcn_post_date(); ?></small>
			</li>
			
		<?php endwhile; ?>
	</ul>
	
	<div class="fvcn-pagination">
		<?php echo $pagination; ?>
	</div>
	
<?php else : ?>
	
	<p><?php _e('There was no Community News found.', 'fvcn'); ?></p>
	
<?php endif; ?>
