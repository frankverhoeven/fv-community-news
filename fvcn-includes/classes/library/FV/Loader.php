<?php

require_once './Loader/Interface.php';


/**
 * FV_Loader
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
class FV_Loader implements FV_Loader_Interface
{
    /**
     * Load a file.
     *
     * @param string $file
     * @param bool $once
     * @return bool
     * @throws Exception
     */
    public function loadFile($file, $once=true)
    {
        if (!file_exists($file)) {
            throw new Exception( 'The file "' . $file . '" could not be found' );
        }

        if (true === $once) {
            return require_once $file;
        } else {
            return require $file;
        }
    }
}
