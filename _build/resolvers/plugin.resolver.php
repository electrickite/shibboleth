<?php
/**
* Resolver to connect plugins to system events for Shibboleth extra
*
* @package shibboleth
* @subpackage build
*/
/* @var $object xPDOObject */
/* @var $pluginObj modPlugin */
/* @var $mpe modPluginEvent */
/* @var xPDOObject $object */
/* @var array $options */
/* @var $modx modX */
/* @var $pluginObj modPlugin */
/* @var $pluginEvent modPluginEvent */
/* @var $newEvents array */

if (!function_exists('checkFields')) {
    function checkFields($required, $objectFields) {

        global $modx;
        $fields = explode(',', $required);
        foreach ($fields as $field) {
            if (!isset($objectFields[$field])) {
                $modx->log(MODX::LOG_LEVEL_ERROR, '[Plugin Resolver] Missing field: ' . $field);
                return false;
            }
        }
        return true;
    }
}


$newEvents = array (
            );


if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            foreach($newEvents as $k => $fields) {

                $event = $modx->getObject('modEvent', array('name' => $fields['name']));
                if (!$event) {
                    $event = $modx->newObject('modEvent');
                    if ($event) {
                        $event->fromArray($fields, "", true, true);
                        $event->save();
                    }
                }
            }

            $intersects = array (
                0 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnHandleRequest',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
                1 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnWebPagePrerender',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
                2 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnBeforeManagerLogin',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
                3 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnBeforeWebLogin',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
                4 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnManagerLoginFormPrerender',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
                5 =>  array (
                  'pluginid' => 'ShibbolethProtect',
                  'event' => 'OnManagerLoginFormRender',
                  'priority' => '0',
                  'propertyset' => '0',
                ),
            );

            if (is_array($intersects)) {
                foreach ($intersects as $k => $fields) {
                    /* make sure we have all fields */
                    if (!checkFields('pluginid,event,priority,propertyset', $fields)) {
                        continue;
                    }
                    $event = $modx->getObject('modEvent', array('name' => $fields['event']));

                    $plugin = $modx->getObject('modPlugin', array('name' => $fields['pluginid']));
                    $propertySetObj = null;
                    if (!empty($fields['propertyset'])) {
                        $propertySetObj = $modx->getObject('modPropertySet',
                            array('name' => $fields['propertyset']));
                    }
                    if (!$plugin || !$event) {
                        $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find Plugin and/or Event ' .
                            $fields['plugin'] . ' - ' . $fields['event']);
                        continue;
                    }
                    $pluginEvent = $modx->getObject('modPluginEvent', array('pluginid'=>$plugin->get('id'),'event' => $fields['event']) );
                    
                    if (!$pluginEvent) {
                        $pluginEvent = $modx->newObject('modPluginEvent');
                    }
                    if ($pluginEvent) {
                        $pluginEvent->set('event', $fields['event']);
                        $pluginEvent->set('pluginid', (integer) $plugin->get('id'));
                        $pluginEvent->set('priority', (integer) $fields['priority']);
                        if ($propertySetObj) {
                            $pluginEvent->set('propertyset', (integer) $propertySetObj->get('id'));
                        } else {
                            $pluginEvent->set('propertyset', 0);
                        }

                    }
                    if (! $pluginEvent->save()) {
                        $modx->log(xPDO::LOG_LEVEL_ERROR, 'Unknown error saving pluginEvent for ' .
                            $fields['plugin'] . ' - ' . $fields['event']);
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            foreach($newEvents as $k => $fields) {
                $event = $modx->getObject('modEvent', array('name' => $fields['name']));
                if ($event) {
                    $event->remove();
                }
            }
            break;
    }
}

return true;