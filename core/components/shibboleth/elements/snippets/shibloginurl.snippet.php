<?php
/**
 * shibLoginUrl
 *
 * DESCRIPTION
 *
 * Returns the Shibboleth login URL
 *
 * PROPERTIES:
 *
 * &target string optional. Default: Current resource URL
 *   If specified, user will be redirected to &target URL
 *   after authentication
 */

$target = $modx->getOption('target', $scriptProperties);

$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'autoload.php';

$handler = new ShibbolethHandler($modx);

return $handler->loginUrl($target);