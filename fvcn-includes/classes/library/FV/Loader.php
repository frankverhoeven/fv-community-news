<?php

/**
 * FV_Loader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Loader
{
    /**
     * Load a file.
     *
     * @param type $file
     * @param bool $once
     * @return bool
     * @throws Exception
     */
    public function loadFile($file, $once=true)
    {
        if (!file_exists($file)) {
            throw new Exception( sprintf('The file "%s" could not be found', $file) );
        }

        if (true === $once) {
            return require_once $file;
        } else {
            return require $file;
        }
    }
}
