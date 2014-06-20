<?php
/**
 * ShibbolethProtect plugin for Shibboleth extra
 *
 * @package shibboleth
 */

/**
 * Description
 * -----------
 * Secures certain pages via Shibboleth authentication and allows Shibboleth
 * logins for MODX users
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package shibboleth
 **/

$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'model/shibboleth.class.php';

$event = $modx->event->name;
$user = new ShibbolethUser($modx);
$tv = $modx->getOption('shibboleth.tv', $scriptProperties);


if ($event == 'OnHandleRequest' && $modx->getOption('shibboleth.fixenv',null,true)) {
    // Fix Apache redirected environment variable names
    foreach ($_SERVER as $key => $value)
        if (substr_compare($key, "REDIRECT_", 0, 9) == 0)
            $_SERVER[preg_replace('/REDIRECT_/', '', $key)] = $value;

} elseif ($event == 'OnWebPagePrerender' && (bool)$modx->resource->getTVValue($tv)) {
    // Protect selected resources with Shibboleth authentication    
    if ( ! $user->isAuthenticated()) {
        // If the user is not authenticated, send them to the login service
        $user->authenticate();
    } else {
        if ( ! $user->isAuthorized()) $modx->sendUnauthorizedPage();
    }

} elseif ($event == 'OnManagerLoginFormPrerender' && $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
    // Display error messages stored in the user session
    if (isset($_SESSION['shibboleth_error'])) {
        $modx->smarty->assign('error_message', $_SESSION['shibboleth_error']);
        unset($_SESSION['shibboleth_error']);
    }

} elseif ($event == 'OnManagerLoginFormRender' && $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
    // Add the Shibboleth login link to the manager login form
    $url = $user->modxHandlerUrl('mgr', MODX_MANAGER_URL);
    $text = $modx->getOption('shibboleth.login_text', $scriptProperties, 'Shibboleth Login');

    $html = '<br class="clear" />
    <div class="login-cb-row">
        <div class="login-cb-col">
            <div class="modx-login-fl-link">
               <h3><a href="'.$url.'">'.$text.'</a></h3>
            </div>
        </div>
    </div>';
    $modx->event->output($html);

} elseif ($event == 'OnBeforeManagerLogin' || $event == 'OnBeforeWebLogin') {
    // First, determine if the force_shib settings is present for the user
    $force_shib = $modx->getOption('shibboleth.force_shib', $scriptProperties, false);
    $user = $modx->getObject('modUser', array('username' => $username));

    if ($user) {
        $settings = $user->getSettings();
        if (isset($settings['shibboleth.force_shib']))
            $force_shib = $settings['shibboleth.force_shib'];
    }

    // Next, we deny login if shibboleth auth is both allwowed and forced
    if ($force_shib && $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
        $modx->error->failure('You must log in using Shibboleth.');
    } else {
        $modx->event->_output = true;
    }
}