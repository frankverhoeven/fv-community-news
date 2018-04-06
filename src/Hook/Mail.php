<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Config\AbstractConfig as Config;
use FvCommunityNews\Post\Status as PostStatus;
use FvCommunityNews\Post\Type as PostType;

/**
 * Notification Mail
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Mail implements HookInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook()
    {
        $postId = \func_get_arg(0);

        if (!$this->config['_fvcn_mail_on_submission'] && !$this->config['_fvcn_mail_on_moderation']) {
            return;
        }
        if (PostStatus::spam() == fvcn_get_post_status($postId)) {
            return;
        }

        if (PostStatus::pending() == fvcn_get_post_status($postId) && ($this->config['_fvcn_mail_on_submission'] || $this->config['_fvcn_mail_on_moderation'])) {
            $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post Awaiting Moderation', 'fvcn');
            $moderationPage = add_query_arg(['post_type' => PostType::post(), 'post_status' => PostStatus::pending()], site_url('/wp-admin/edit.php'));
        } else if ($this->config['_fvcn_mail_on_submission']) {
            $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post', 'fvcn');
            $moderationPage = add_query_arg(['post_type' => PostType::post()], site_url('/wp-admin/edit.php'));
        } else {
            return;
        }

        $to = get_option('admin_email');
        if (!is_email($to)) {
            return;
        }

        $message = '<html><head><style type="text/css">*,html{padding:0;margin:0}body{font:11px/17px"Lucida Grande","Lucida Sans Unicode",Helvetica,Arial,Verdana;color:#333}a{color:#08c;text-decoration:underline}#container{margin:10px auto;width:450px}.column{display:inline-block;vertical-align:top}#post-thumbnail{width:90px;padding:5px}#post-info{width:350px;padding:2px 0 0}#post-info p{margin:2px 0 0}#post-info p span{float:left;width:85px;text-align:right;margin-right:8px;font-weight:700}#post-content{padding:3px 5px 0}#post-actions{padding:5px}</style></head><body><div id="container"><div id="post-details"><div id="post-thumbnail" class="column">' . (fvcn_has_post_thumbnail($postId) ? fvcn_get_post_thumbnail($postId, [90, 90]) : fvcn_get_post_author_avatar($postId, 90)) . '</div><div id="post-info" class="column"><p><span>' . __('Author Name', 'fvcn') . '</span>' . fvcn_get_post_author_display_name($postId) . ' (<a href="http://whois.arin.net/rest/ip/' . fvcn_get_post_author_ip($postId) . '">' . fvcn_get_post_author_ip($postId) . '</a>)</p><p><span>' . __('Author Email', 'fvcn') . '</span><a href="mailto:' . fvcn_get_post_author_email($postId) . '">' . fvcn_get_post_author_email($postId) . '</a></p><p><span>' . __('Title', 'fvcn') . '</span>' . fvcn_get_post_title($postId) . '</p><p><span>' . __('Link', 'fvcn') . '</span>' . (fvcn_has_post_link($postId) ? '<a href="' . fvcn_get_post_link($postId) . '">' . parse_url(fvcn_get_post_link($postId), PHP_URL_HOST) . '</a>' : __('No Link Added', 'fvcn')) . '</p><p><span>' . __('Tags', 'fvcn') . '</span>' . fvcn_get_post_tag_list($postId, ['before'=>'', 'after'=>'']) . '</p></div></div><div id="post-content">' . fvcn_get_post_content($postId) . '</div><div id="post-actions"><a href="' . $moderationPage . '">' . __('Moderation Page', 'fvcn') . '</a> | <a href="' . add_query_arg(['post' => $postId, 'action' => 'edit'], site_url('/wp-admin/post.php')) . '">' . __('Edit Post', 'fvcn') . '</a> | <a href="' . fvcn_get_post_permalink($postId) . '">' . __('Permalink', 'fvcn') . '</a></div></div></body></html>';

        $headers = [
            'Content-Type: text/html',
        ];

        wp_mail($to, $subject, $message, $headers);
    }
}
