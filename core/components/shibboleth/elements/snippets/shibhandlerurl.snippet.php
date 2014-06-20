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
require_once $corePath.'model/shibboleth.class.php';
$user = new ShibbolethUser($modx);

$target = $modx->getOption('target', $scriptProperties);
$context = $modx->getOption('context', $scriptProperties, $modx->context->get('key'));

return $user->modxHandlerUrl($context, $target);