<?php

namespace FvCommunityNews\Application;

class Application
{
    public function run()
    {
        $bootstrap = new Bootstrap();
        $bootstrap->registerShortcodes();
    }
}
