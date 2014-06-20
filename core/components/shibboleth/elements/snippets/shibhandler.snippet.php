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

$shibUser = new ShibbolethUser($modx);

// Set session context(s) to log into
if (isset($scriptProperties['authContext'])) {
    $context = $scriptProperties['authContext'];
} elseif ($modx->request->getParameters('ctx')) {
    $context = $modx->request->getParameters('ctx');
} else {
    $context = $modx->context->get('key');
}

// Find target URL
if (isset($_SESSION['shibboleth_target'])) {
    $target = $_SESSION['shibboleth_target'];
    unset($_SESSION['shibboleth_target']);
} elseif (isset($scriptProperties['target'])) {
    $target = $scriptProperties['target'];
} else {
    $target = urldecode($modx->request->getParameters('target'));
}

// Normalize target URL
$target = $shibUser->buildTarget($target);


if ( ! $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
    // Bail if we do not allow MODX users to authenticate via Shibboleth

} elseif ( ! $shibUser->isAuthenticated()) {
    // If the user is not authenticated, stash the target in the session
    $_SESSION['shibboleth_target'] = $target;

    // Then send them to the login service
    $shibUser->authenticate();

} else {
    // If the user is authenticated, match them to a MODX user and log them in
    if ($shibUser->setModxUser()) {
        $shibUser->syncModxGroups();
        $shibUser->loginModxUser($context);
    }
}

if (!$modx->user->isAuthenticated($context)) {
    $_SESSION['shibboleth_error'] = 'That account could not be located. You may not have permission to access this resource.';
}

$modx->sendRedirect($target);