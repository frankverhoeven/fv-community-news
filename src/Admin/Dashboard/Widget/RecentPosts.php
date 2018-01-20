<?php

namespace FvCommunityNews\Admin\Dashboard\Widget;

use FvCommunityNews\Config\AbstractConfig as Config;
use FvCommunityNews\Post\Mapper as PostMapper;
use FvCommunityNews\Post\PostType;

/**
 * RecentPosts
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class RecentPosts
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var PostMapper
     */
    private $postMapper;

    /**
     * __construct()
     *
     * @param Config $config
     * @param PostMapper $postMapper
     */
    public function __construct(Config $config, PostMapper $postMapper)
    {
        $this->config = $config;
        $this->postMapper = $postMapper;

        add_action('fvcn_admin_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('fvcn_admin_head', [$this, 'dashboardHead']);

        add_action('wp_ajax_fvcn-dashboard-widget-rp-ajax', [$this, 'ajaxResponse']);

        $this->response();
    }

    /**
     * enqueueScripts()
     *
     */
    public function enqueueScripts()
    {
        $registry = \FvCommunityNews::$container->get('Registry');

        wp_enqueue_script(
            'fvcn-dashboard-widget-rp-js',
            $registry['pluginUrl'] . 'public/js/dashboard.js',
            ['jquery'],
            '20120721'
        );

        wp_localize_script(
            'fvcn-dashboard-widget-rp-js',
            'FvCommunityNewsAdminDashboardOptions',
            [
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'action' => 'fvcn-dashboard-widget-rp-ajax',
                'nonce' => wp_create_nonce('fvcn-dashboard')
            ]
        );

        do_action('fvcn_enqueue_dashboard_widget_recent_posts_scripts');
    }

    /**
     * dashboardHead()
     *
     */
    public function dashboardHead()
    {
        ?>
        <style type="text/css">
            #fvcn-dashboard-recent-posts .inside {
                margin: 0 auto;
                padding: 0;
            }
            #fvcn-dashboard-recent-posts .inside .dashboard-widget-control-form {
                padding: 0 10px 10px;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post {
                clear: both;
                padding: 10px;
                border-top: 1px solid #dfdfdf;
                background: #fafafa;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post:first-child {
                border-top: none;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-post-title {
                color: #666;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post.pending {
                background-color: #fef7f1;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .avatar,
            #fvcn-dashboard-recent-posts-list .fvcn-post .wp-post-image {
                float: left;
                margin: 0 10px 10px 0;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-dashboard-post-wrap blockquote {
                margin: 0 auto;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions {
                visibility: hidden;
                margin: 3px 0 0;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post:hover .fvcn-row-actions {
                visibility: visible;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .publish a {
                color: #006505;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .unpublish a {
                color: #d98500;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .spam a {
                color: #bc0b0b;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .trash a {
                color: #bc0b0b;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .trash a:hover,
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .spam a:hover,
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .unpublish a:hover,
            #fvcn-dashboard-recent-posts-list .fvcn-post .fvcn-row-actions .publish a:hover {
                color: #d54e21;
            }
            #fvcn-dashboard-recent-posts-list .fvcn-post.approved .fvcn-row-actions .publish,
            #fvcn-dashboard-recent-posts-list .fvcn-post.pending .fvcn-row-actions .unpublish {
                display: none;
            }
            #fvcn-dashboard-view-links {
                float: none;
                border-top: 1px solid #eee;
                padding: 8px 12px 4px;
                margin: 0;
            }
        </style>
        <?php

        do_action('fvcn_dashboard_widget_recent_posts_styles');
    }

    /**
     * response()
     *
     */
    public function response()
    {
        if (!isset($_GET['action'], $_GET['post_id']) || false === strpos($_GET['action'], 'fvcn')) {
            return;
        }

        $this->updatePostStatus($_GET['post_id'], $_GET['action']);
    }

    /**
     * ajaxResponse()
     *
     */
    public function ajaxResponse()
    {
        $success = false;
        $message = false;

        if (!isset($_POST['nonce'], $_POST['post_id'], $_POST['fvcn_action'])
            || !wp_verify_nonce($_POST['nonce'], 'fvcn-dashboard')
            || !is_numeric($_POST['post_id'])
            || !fvcn_get_post($_POST['post_id'])
            || !current_user_can('edit_posts', $_POST['post_id']))
        {
            $message = __('Are you sure?', 'fvcn');
        }

        if (false === $message) {
            $success = $this->updatePostStatus($_POST['post_id'], $_POST['fvcn_action']);

            if (false === $success) {
                $message = __('Invallid action', 'fvcn');
            }
        }

        if (false === $success) {
            $response = [
                'success' => false,
                'message' => $message
            ];
        } else {
            $response = [
                'success' => true
            ];
        }

        echo json_encode($response);

        exit;
    }

    /**
     * updatePostStatus()
     *
     * @param $postId
     * @param string $action
     * @return bool
     */
    protected function updatePostStatus($postId, $action)
    {
        switch ($action) {
            case 'fvcn_toggle_post_spam_status' :
                check_admin_referer('fvcn-spam-post_' . $postId);
                return fvcn_is_post_spam($postId) ? $this->postMapper->publishPost($postId) : $this->postMapper->spamPost($postId);
                break;

            case 'fvcn_toggle_post_publish_status' :
                check_admin_referer('fvcn-publish-post_' . $postId);
                return fvcn_is_post_published($postId) ? $this->postMapper->unpublishPost($postId) : $this->postMapper->publishPost($postId);
                break;

            default:
                return false;
        }
    }

    /**
     * register()
     *
     * @return RecentPosts
     */
    public function register()
    {
        if (current_user_can('edit_posts')) {
            wp_add_dashboard_widget(
                'fvcn-dashboard-recent-posts',
                __('Recent Community News', 'fvcn'),
                [$this, 'widget'],
                [$this, 'control']
            );
        }

        return $this;
    }

    /**
     * widget()
     *
     */
    public function widget()
    {
        $options = [
            'posts_per_page' => $this->config['_fvcn_dashboard_rp_num'],
            'post_status' => PostType::STATUS_PUBLISH . ',' . PostType::STATUS_PENDING
        ];

        if (fvcn_has_posts($options)) :
            $alt = 'odd alt'; ?>

            <div id="fvcn-dashboard-recent-posts-list">
                <?php while (fvcn_posts()) : fvcn_the_post(); ?>

                    <?php
                    $class = 'fvcn-post ';

                    if (PostType::STATUS_PENDING == fvcn_get_post_status()) {
                        $class .= 'pending ';
                    } else {
                        $class .= 'approved ';
                    }

                    $class .= $alt;
                    ?>

                    <div id="fvcn-post-<?php fvcn_post_id(); ?>" class="<?= $class; ?>">
                        <?php
                        if (fvcn_has_post_thumbnail()) {
                            fvcn_post_thumbnail(0, [50, 50]);
                        } else {
                            fvcn_post_author_avatar(0, 50);
                        }
                        ?>

                        <div class="fvcn-dashboard-post-wrap">
                            <h4 class="fvcn-post-title">
                                <?php if (fvcn_has_post_link()) : ?>
                                    <a href="<?php fvcn_post_link(); ?>"><?php fvcn_post_title(); ?></a>
                                <?php else : ?>
                                    <?php fvcn_post_title(); ?>
                                <?php endif; ?>

                                <?php _e('by', 'fvcn'); ?>

                                <?php fvcn_post_author(); ?>

                                <a href="<?php fvcn_post_permalink(); ?>">#</a>
                            </h4>

                            <blockquote><?php fvcn_post_excerpt(); ?></blockquote>

                            <p class="fvcn-row-actions">
                                <?php
                                $publish_uri = esc_url(wp_nonce_url(add_query_arg([
                                    'post_id' => fvcn_get_post_id(),
                                    'action' => 'fvcn_toggle_post_publish_status'
                                ], 'index.php'), 'fvcn-publish-post_' . fvcn_get_post_id()));
                                $edit_uri = esc_url(add_query_arg([
                                    'post' => fvcn_get_post_id(),
                                    'action' => 'edit'
                                ], 'post.php'));
                                $spam_uri = esc_url(wp_nonce_url(add_query_arg([
                                    'post_id' => fvcn_get_post_id(),
                                    'action' => 'fvcn_toggle_post_spam_status'
                                ], 'index.php'), 'fvcn-spam-post_' . fvcn_get_post_id()));
                                $trash_uri = esc_url(wp_nonce_url(add_query_arg([
                                    'post' => fvcn_get_post_id(),
                                    'action' => 'trash'
                                ], 'post.php'), 'trash-' . PostType::POST_TYPE_KEY . '_' . fvcn_get_post_id()));
                                ?>
                                <span class="publish"><a href="<?= $publish_uri; ?>"><?php _e('Publish', 'fvcn'); ?></a></span>
                                <span class="unpublish"><a href="<?= $publish_uri; ?>"><?php _e('Unpublish', 'fvcn'); ?></a></span>
                                <span class="edit"> | <a href="<?= $edit_uri; ?>"><?php _e('Edit', 'fvcn'); ?></a></span>
                                <span class="spam"> | <a href="<?= $spam_uri; ?>"><?php _e('Spam', 'fvcn'); ?></a></span>
                                <span class="trash"> | <a href="<?= $trash_uri; ?>"><?php _e('Trash', 'fvcn'); ?></a></span>
                            </p>
                        </div>
                    </div>

                    <?php
                    $alt = ($alt == 'even' ? 'odd alt' : 'even');
                endwhile; ?>
            </div>

            <p id="fvcn-dashboard-view-links">
                <a href="edit.php?post_type=<?= PostType::POST_TYPE_KEY ?>"><?php _e('View All', 'fvcn'); ?></a>
            </p>

        <?php else : ?>

            <p><?php _e('No posts found, yet.', 'fvcn'); ?></p>

        <?php endif;
    }

    /**
     * control()
     *
     */
    public function control()
    {
        if ('post' == strtolower($_SERVER['REQUEST_METHOD']) && isset($_POST['_fvcn_dashboard_rp'])) {
            update_option('_fvcn_dashboard_rp_num', min(max((int) $_POST['_fvcn_dashboard_rp_num'], 1), 30));
        }

        ?>
        <p>
            <label for="_fvcn_dashboard_rp_num"><?php _e('Number of posts to show:', 'fvcn'); ?></label>
            <input type="text" name="_fvcn_dashboard_rp_num" id="_fvcn_dashboard_rp_num" value="<?= fvcn_get_form_option('_fvcn_dashboard_rp_num'); ?>" size="3">
            <small><?php _e('(1 - 30)', 'fvcn'); ?></small>
        </p>

        <input type="hidden" name="_fvcn_dashboard_rp" id="_fvcn_dashboard_rp" value="1">
        <?php
    }
}
