<?php
/**
 * shibAttr
 *
 * DESCRIPTION
 *
 * Displays the value of a Shibboleth attribute for a user
 *
 * RETURN:
 *
 * Return the value of the &attribute if set
 *
 * USAGE:
 *
 * [[!shibAttr? &attribute=`email`]]
 */

$attribute = strval($modx->getOption('attribute', $scriptProperties));

$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'autoload.php';

$user = new ShibbolethUser($modx);

return $user->getAttribute($attribute);