<?php

/**
 * FV_Loader_Interface
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
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
