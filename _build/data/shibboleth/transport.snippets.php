<?php
/**
 * snippets transport file for Shibboleth extra
 *
 * @package shibboleth
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $snippets */


$snippets = array();

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array (
  'id' => 1,
  'description' => 'Tests whether the user has a valid Shibboleth session',
  'name' => 'shibAuth',
), '', true, true);
$snippets[1]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/shibauth.snippet.php'));

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array (
  'id' => 2,
  'description' => 'Returns the full URL for the shibboleth session initiator',
  'name' => 'shibLoginUrl',
), '', true, true);
$snippets[2]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/shibloginurl.snippet.php'));

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array (
  'id' => 3,
  'description' => 'Displays the value of a Shibboleth attribute for a user',
  'name' => 'shibAttr',
), '', true, true);
$snippets[3]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/shibattr.snippet.php'));

$snippets[4] = $modx->newObject('modSnippet');
$snippets[4]->fromArray(array (
  'id' => 4,
  'description' => 'Routes users through Shibboleth web authentication and logs them in to MODX when they have been authenticated.',
  'name' => 'shibHandler',
), '', true, true);
$snippets[4]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/shibhandler.snippet.php'));

$snippets[5] = $modx->newObject('modSnippet');
$snippets[5]->fromArray(array (
  'id' => 5,
  'description' => 'Generates a URL for the handler resource used to process MODX user logins via Shibboleth',
  'name' => 'shibHandlerUrl',
), '', true, true);
$snippets[5]->setContent(file_get_contents($sources['source_core'] . '/elements/snippets/shibhandlerurl.snippet.php'));

return $snippets;
