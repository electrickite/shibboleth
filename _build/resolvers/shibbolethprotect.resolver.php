<?php
/**
 * Plugin event resolver for the ShibbolethProtect plugin
 *
 * Ensures that only expected system events are attached to the plugin
 *
 * @package shibboleth
 * @subpackage build
 */

$plugin_name = 'ShibbolethProtect';
$removed_events = array();
$expected_plugin_events = array(
    'OnBeforeManagerLogin',
    'OnBeforeWebLogin',
    'OnHandleRequest',
    'OnManagerLoginFormPrerender',
    'OnManagerLoginFormRender',
    'OnManagerPageInit',
    'OnLoadWebDocument',
    'OnManagerAuthentication',
    'OnWebAuthentication'
);

if ($object->xpdo) {
    $modx = &$object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UNINSTALL:
            break;

        case xPDOTransport::ACTION_UPGRADE:
            $plugin = $modx->getObject('modPlugin', array(
                'name' => $plugin_name
            ));
            if (!$plugin) {
                $modx->log(MODX::LOG_LEVEL_ERROR, "[Plugin Resolver] Could not find $plugin_name plugin");
                break;
            }

            $events = $modx->getIterator('modPluginEvent', array(
                'pluginid' => $plugin->get('id')
            ));

            foreach ($events as $event) {
                if (!in_array($event->get('event'), $expected_plugin_events)) {
                    if ($event->remove()) {
                        $removed_events[] = $event->get('event');
                    } else {
                        $modx->log(MODX::LOG_LEVEL_ERROR, '[Plugin Resolver] Failed to remove ' . $event->get('event') . " from $plugin_name");
                    }
                }
            }

            if (!empty($removed_events)) {
                $modx->log(MODX::LOG_LEVEL_INFO, '[Plugin Resolver] Removed ' . implode(', ', $removed_events) . " events from $plugin_name");
            }
            break;
    }
}

return true;
