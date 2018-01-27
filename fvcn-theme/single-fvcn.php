<?php

/**
 * The Template for displaying a single community post.
 *
 * @package    FV Community News
 * @subpackage Theme
 */

get_header(); ?>

<div class="wrap">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <div class="entry-meta">
                        <span class="sep"><?php _e('Posted on', 'fvcn'); ?></span>
                        <a href="<?php fvcn_post_permalink(); ?>" rel="bookmark"><time class="entry-date"><?php fvcn_post_date(); ?></time></a>
                        <span class="by-author">
                            <span class="sep"><?php _e('by', 'fvcn'); ?></span>
                            <span class="author vcard">
                                <?php fvcn_post_author_link(); ?>
                            </span>
                        </span>
                    </div><!-- .entry-meta -->

                    <h1 class="entry-title"><?php fvcn_post_title(); ?></h1>
                </header><!-- .entry-header -->

                <?php fvcn_get_template_part('fvcn/content', 'single-post'); ?>

                <div class="entry-meta">
                    <?php edit_post_link(__('Edit', 'fvcn'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </article><!-- #post-<?php the_ID(); ?> -->


            <?php comments_template('', true); ?>

        <?php endwhile; // end of the loop. ?>

        </main><!-- #main -->
    </div><!-- #primary -->
    <?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer(); ?>