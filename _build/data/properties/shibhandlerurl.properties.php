<?php
/**
 * Property transport file for the shibHandlerUrl snippet
 *
 * @package shibboleth
 * @subpackage build
 */

$properties = array(
    array(
        'name' => 'target',
        'desc' => 'prop_shibboleth.target_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => null,
        'lexicon' => 'shibboleth:properties',
    ),
    array(
        'name' => 'context',
        'desc' => 'prop_shibboleth.context_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => null,
        'lexicon' => 'shibboleth:properties',
    )
);

return $properties;