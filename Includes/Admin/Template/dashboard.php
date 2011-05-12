<?php if (fvcn_has_posts(array('approved'=>'!spam', 'num'=>fvcn_get_setting('NumDashboardListItems')))) : ?>
	
	<div id="the-comment-list" class="list:comment">
		<?php $class = 'even'; while (fvcn_posts()) : fvcn_the_post(); ?>
		
		<div id="post-<?php fvcn_post_id(); ?>" class="comment <?php echo $class; ?> comment-item depth-1 <?php echo ('1'==fvcn_get_post_approved()?'approved':'unapproved'); ?> fvcn-post">
			<?php echo get_avatar(fvcn_get_post_author_email(), 50); ?>
			
			<div class="dashboard-comment-wrap">
				<h4 class="comment-meta">
					<?php printf(__('%1$s from %2$s %3$s', 'fvcn'),
						'<cite class="comment-author">' . fvcn_get_post_link( fvcn_get_post_title() ) . '</cite>',
						'<cite class="comment-author"><a href="mailto:' . fvcn_get_post_author_email() . '" class="url">' . fvcn_get_post_author() . '</a></cite>',
						'<span class="approve">' . __('[Pending]', 'fvcn') . '</span>'); ?>
					<?php if (fvcn_get_setting('Tracking')) : ?><small><?php printf(__('(%d views)', 'fvcn'), fvcn_get_post_views()); ?></small><br /><?php endif; ?>
				</h4>
				
				<blockquote><?php fvcn_post_excerpt(); ?></blockquote>
				
				<p class="row-actions fvcn-actions">
					<span class="approve"><a href="<?php fvcn_post_approve_link(); ?>"><?php _e('Approve' , 'fvcn'); ?></a></span>
					<span class="unapprove"><a href="<?php fvcn_post_unapprove_link(); ?>"><?php _e('Unapprove' , 'fvcn'); ?></a></span>
					<span class="edit"> | <a href="<?php fvcn_post_edit_link(); ?>"><?php _e('Edit' , 'fvcn'); ?></a></span>
					<span class="spam"> | <a href="<?php fvcn_post_spam_link(); ?>"><?php _e('Spam' , 'fvcn'); ?></a></span>
					<span class="trash"> | <a href="<?php fvcn_post_delete_link(); ?>"><?php _e('Delete' , 'fvcn'); ?></a></span>
				</p>
			</div>
		</div>
		
		<?php $class = ('even' == $class) ? 'odd alt' : 'even'; endwhile; ?>
		
		<p class="textright">
			<a href="admin.php?page=fvcn-admin" class="button"><?php _e('View all', 'fvcn'); ?></a>
		</p>
	</div>
	
<?php else : ?>
	
	<p><?php _e('No Community News added yet.', 'fvcn'); ?></p>
	
<?php endif; ?>
