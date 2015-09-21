<?php

/**
 * Utility class for the ShibbolethProtect Plugin
 */
class ShibbolethPluginHelper extends ShibbolethBase {

    /**
     * Attempts to work around Apache prepending REDIRECT_ to environment
     * variables
     */
    public function fixEnvironment()
    {
        if ($this->modx->getOption('shibboleth.fixenv', $this->scriptProperties, true)) {
            foreach ($_SERVER as $key => $value)
                if (substr_compare($key, "REDIRECT_", 0, 9) == 0)
                    $_SERVER[preg_replace('/REDIRECT_/', '', $key)] = $value;
        }
    }

    /**
     * Sets the current system event output. Handles older versions of MODX.
     */
    public function setEventOutput($output)
    {
        $version = $this->modx->getVersionData();

        if ($version['major_version'] < 3) {
            $this->modx->event->_output = $output;
        } else {
            $this->modx->event->output($output);
        }
    }

}