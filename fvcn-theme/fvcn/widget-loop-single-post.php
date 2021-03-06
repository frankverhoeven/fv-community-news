<?php

/**
 * widget-loop-single-post.php
 *
 * @package    FV Community News
 * @subpackage Theme
 */

?>

<h5><a href="<?php fvcn_has_post_link() ? fvcn_post_link() : fvcn_post_permalink(); ?>"><?php fvcn_post_title(); ?></a></h5>

<div class="fvcn-post-content">
    <?php if (fvcn_has_post_thumbnail() && fvcn_show_widget_thumbnail()) : ?>
        <div class="fvcn-post-thumbnail">
            <?php fvcn_post_thumbnail(0, [50, 50]); ?>
        </div>
    <?php endif; ?>

    <?php fvcn_post_excerpt(); ?>
</div>
