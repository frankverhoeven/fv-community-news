<?php

namespace FvCommunityNews\Hook;

/**
 * HookInterface
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
interface HookInterface
{
    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook(): void;
}
