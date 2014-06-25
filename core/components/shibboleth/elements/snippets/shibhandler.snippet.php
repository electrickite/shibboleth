<?php
/**
 * shibHandler
 *
 * DESCRIPTION
 *
 * Handles MODX user logins via Shibboleth. Use on a resource that is accessible
 * to anonymous users.
 *
 * USAGE:
 *
 * [[!shibHandler]]
 */
$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'model/shibboleth.class.php';

$handler = new ShibbolethHandler($modx);
$handler->doLogin();