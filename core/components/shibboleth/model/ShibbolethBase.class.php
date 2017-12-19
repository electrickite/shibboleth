<?php
/**
 * Common methods and properties for Shibboleth classes functions
 *
 * @package shibboleth
 */

class ShibbolethBase
{
    /**
     * The Modx object for this request
     * @var Modx
     */
    protected $modx;

    /**
     * Optional properties array from the calling script
     * @var array
     */
    protected $scriptProperties;

    /**
     * Constructor for a ShibbolethUser object.
     *
     * @param Modx $modx
     *   The Modx object
     */
    public function __construct($modx, $scriptProperties = array())
    {
        $this->modx = $modx;
        $this->scriptProperties = $scriptProperties;
    }

}