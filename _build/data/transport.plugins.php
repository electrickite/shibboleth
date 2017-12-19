<?php
/**
 * Shibboleth MODX Extra plugin transport script
 *
 * @package shibboleth
 * @subpackage build
 */

$plugins = array();

/* create the plugin object */
$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->set('id', 1);
$plugins[1]->set('property_preprocess', false);
$plugins[1]->set('name', 'ShibbolethProtect');
$plugins[1]->set('description', 'Secures certain pages via Shibboleth authentication and allow Shibboleth logins for MODX users');
$plugins[1]->set('category', 0);
$plugins[1]->setContent(file_get_contents($sources['elements'] . 'plugins/shibbolethprotect.plugin.php'));

$events = include $sources['data'] . 'events/shibbolethprotect.events.php';
if (is_array($events) && !empty($events)) {
    $plugins[1]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO, 'Packaged in '.count($events).' Plugin Events for ShibbolethProtect plugin.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find plugin events for ShibbolethProtect plugin!');
}
unset($events);

return $plugins;
