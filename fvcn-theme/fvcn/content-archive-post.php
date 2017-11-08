<?php

/**
 * content-archive-post.php
 *
 * @version    20120716
 * @package    FV Community News
 * @subpackage Theme
 */

?>

<div class="entry-content fvcn-post-content">
    <?php if (fvcn_has_post_thumbnail()) : ?>
        <div class="fvcn-post-thumbnail">
            <?php fvcn_post_thumbnail(0, [60, 60]); ?>
        </div>
    <?php endif; ?>

    <?php fvcn_post_content(); ?>
</div>

<footer class="fvcn-post-meta">
    <span class="fvcn-post-tags tag-links">
        <?php fvcn_post_tag_list(0, ['before'=>__('Tags: ', 'fvcn'), 'after'=>'']); ?>
    </span>
</footer>
