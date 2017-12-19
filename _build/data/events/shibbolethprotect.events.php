<?php
/**
 * Event transport file for the ShibbolethProtect plugin
 *
 * @package shibboleth
 * @subpackage build
 */

$events = array();

$events[0]= $modx->newObject('modPluginEvent');
$events[0]->fromArray(array(
    'event' => 'OnBeforeManagerLogin',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[1]= $modx->newObject('modPluginEvent');
$events[1]->fromArray(array(
    'event' => 'OnBeforeWebLogin',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[2]= $modx->newObject('modPluginEvent');
$events[2]->fromArray(array(
    'event' => 'OnHandleRequest',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[3]= $modx->newObject('modPluginEvent');
$events[3]->fromArray(array(
    'event' => 'OnManagerLoginFormPrerender',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[4]= $modx->newObject('modPluginEvent');
$events[4]->fromArray(array(
    'event' => 'OnManagerLoginFormRender',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[5]= $modx->newObject('modPluginEvent');
$events[5]->fromArray(array(
    'event' => 'OnManagerPageInit',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[6]= $modx->newObject('modPluginEvent');
$events[6]->fromArray(array(
    'event' => 'OnLoadWebDocument',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[7]= $modx->newObject('modPluginEvent');
$events[7]->fromArray(array(
    'event' => 'OnManagerAuthentication',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

$events[8]= $modx->newObject('modPluginEvent');
$events[8]->fromArray(array(
    'event' => 'OnWebAuthentication',
    'priority' => 0,
    'propertyset' => 0,
), '', true, true);

return $events;
