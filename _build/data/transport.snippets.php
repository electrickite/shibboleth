<?php
/**
 * Shibboleth MODX Extra snippet transport script
 *
 * @package shibboleth
 * @subpackage build
 */

$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'property_preprocess' => false,
    'name' => 'shibAuth',
    'description' => 'Tests whether the user has a valid Shibboleth session',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['elements'] . 'snippets/shibauth.snippet.php'));
$properties = include $sources['data'] . 'properties/shibauth.properties.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'property_preprocess' => false,
    'name' => 'shibLoginUrl',
    'description' => 'Returns the full URL for the shibboleth session initiator',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['elements'] . 'snippets/shibloginurl.snippet.php'));
$properties = include $sources['data'] . 'properties/shibloginurl.properties.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'property_preprocess' => false,
    'name' => 'shibAttr',
    'description' => 'Displays the value of a Shibboleth attribute for a user',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['elements'] . 'snippets/shibattr.snippet.php'));
$properties = include $sources['data'] . 'properties/shibattr.properties.php';
$snippets[3]->setProperties($properties);
unset($properties);

$snippets[4] = $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
    'id' => 4,
    'property_preprocess' => false,
    'name' => 'shibHandler',
    'description' => 'Routes users through Shibboleth web authentication and logs them in to MODX when they have been authenticated.',
), '', true, true);
$snippets[4]->setContent(file_get_contents($sources['elements'] . 'snippets/shibhandler.snippet.php'));
$properties = include $sources['data'] . 'properties/shibhandler.properties.php';
$snippets[4]->setProperties($properties);
unset($properties);

$snippets[5] = $modx->newObject('modSnippet');
$snippets[5]->fromArray(array(
    'id' => 5,
    'property_preprocess' => false,
    'name' => 'shibHandlerUrl',
    'description' => 'Generates a URL for the handler resource used to process MODX user logins via Shibboleth',
), '', true, true);
$snippets[5]->setContent(file_get_contents($sources['elements'] . 'snippets/shibhandlerurl.snippet.php'));
$properties = include $sources['data'] . 'properties/shibhandlerurl.properties.php';
$snippets[5]->setProperties($properties);
unset($properties);

return $snippets;
