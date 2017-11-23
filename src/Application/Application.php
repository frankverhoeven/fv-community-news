<?php

namespace FvCommunityNews\Application;

use FvCommunityNews\Config;

class Application
{
    /**
     * @var Config
     */
    private $config;

    /**
     * __construct()
     *
     * @param array $config Application config
     * @version 20171112
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * run()
     *
     */
    public function run()
    {
        $bootstrap = new Bootstrap($this->config);
        $hooks = new Hooks($bootstrap);

        $hooks->register();
    }
}
