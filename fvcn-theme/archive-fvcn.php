<?php

/**
 * The Template for displaying the archive of community posts.
 *
 * @package    FV Community News
 * @subpackage Theme
 */

get_header(); ?>

<section id="primary">
    <div id="content">

    <?php if (have_posts()) : ?>

        <header class="page-header">
            <h1 class="page-title"><?php _e('Community News Archive', 'fvcn'); ?></h1>
        </header>

        <?php while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title">
                        <a href="<?php fvcn_post_permalink(); ?>" rel="bookmark"><?php fvcn_post_title(); ?></a>
                    </h1>

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
                </header><!-- .entry-header -->

                <?php fvcn_get_template_part('fvcn/content', 'archive-post'); ?>

                <div class="entry-meta">
                    <?php edit_post_link(__('Edit', 'twentyeleven'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </article>
        <?php endwhile; ?>

    <?php else : ?>

        <article id="post-0" class="post no-results not-found">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e('Nothing Found', 'fvcn'); ?></h1>
            </header><!-- .entry-header -->

            <div class="entry-content">
                <p><?php _e('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'fvcn'); ?></p>
                <?php get_search_form(); ?>
            </div><!-- .entry-content -->
        </article><!-- #post-0 -->

    <?php endif; ?>

    </div><!-- #content -->
</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
