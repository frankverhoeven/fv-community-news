<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Widget\Form as FormWidget;
use FvCommunityNews\Widget\ListPosts as ListPostsWidget;
use FvCommunityNews\Widget\TagCloud as TagCloudWidget;

/**
 * WidgetsInit
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class WidgetsInit implements HookInterface
{
    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook()
    {
        FormWidget::register();
        ListPostsWidget::register();
        TagCloudWidget::register();

        do_action('fvcn_widgets_init');
    }
}
