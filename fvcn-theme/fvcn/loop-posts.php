<?php

/**
 * loop-posts.php
 *
 * @package    FV Community News
 * @subpackage Theme
 */

?>

<?php while (fvcn_posts()) : fvcn_the_post(); ?>

    <?php fvcn_get_template_part('fvcn/loop', 'single-post'); ?>

<?php endwhile; ?>
