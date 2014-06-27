<?php
/**
 * shibAuth
 *
 * DESCRIPTION
 *
 * Determines if a user is authorized to view protected content
 *
 * RETURN:
 *
 * Returns '1' if the user is authorized, '0' if not.
 *
 * USAGE:
 *
 * [[!shibAuth]]
 */

$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'autoload.php';

$user = new ShibbolethUser($modx);

return (int) $user->isAuthorized();