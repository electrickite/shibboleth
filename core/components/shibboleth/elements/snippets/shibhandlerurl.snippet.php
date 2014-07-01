<?php
/**
 * shibHandlerUrl
 *
 * DESCRIPTION
 *
 * Returns the Shibboleth login handler URL
 *
 * PROPERTIES:
 *
 * &target string optional. Default: Current resource URL
 *   If specified, user will be redirected to &target URL
 *   after authentication
 */
$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'autoload.php';
$handler = new ShibbolethHandler($modx);

$target = $modx->getOption('target', $scriptProperties, null);
$context = $modx->getOption('context', $scriptProperties, null);

return $handler->modxHandlerUrl($context, $target);