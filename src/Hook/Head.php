<?php

namespace FvCommunityNews\Hook;

/**
 * Head
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Head implements HookInterface
{
    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook(): void
    {
        echo '<meta name="generator" content="FV Community News">' . "\n";
        do_action('fvcn_head');
    }
}
