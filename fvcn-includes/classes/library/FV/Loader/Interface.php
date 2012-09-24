<?php

/**
 * FV_Loader_Interface
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
interface FV_Loader_Interface
{
    /**
     * Load a file.
     *
     * @param string $file
     * @param bool $once
     * @return bool
     */
    public function loadFile($file, $once=true);
}
