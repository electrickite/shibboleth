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
require_once $corePath.'autoload.php';
$modx->getService('shibHandler', 'ShibbolethHandler', $corePath.'model/');
$helper = new ShibbolethPluginHelper($modx);
$modx->lexicon->load('shibboleth:default');

$event = $modx->event->name;
$tv = $modx->getOption('shibboleth.tv', $scriptProperties);

if ($event == 'OnHandleRequest') {
    // Fix Apache redirected environment variable names
    $helper->fixEnvironment();

    // Remove the MODX session if no shibboleth session is present
    $modx->shibHandler->enforceShibSession();

    // Handle Shibboleth authentication if requested
    $param = $modx->getOption('shibboleth.login_param', $scriptProperties, 'shibboleth_login');
    if (isset($_REQUEST[$param])) {
        $modx->shibHandler->doLogin();
    }
}

elseif ($event == 'OnWebPagePrerender' && (bool)$modx->resource->getTVValue($tv)) {
    // Protect selected resources with Shibboleth authentication    
    if ( ! $modx->shibHandler->shibUser()->isAuthenticated()) {
        // If the user is not authenticated, send them to the login service
        $modx->shibHandler->authenticate();
    } else {
        if ( ! $modx->shibHandler->shibUser()->isAuthorized()) $modx->sendUnauthorizedPage();
    }
}

elseif ($event == 'OnManagerLoginFormPrerender' && $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
    // Display error messages stored in the user session
    if ($modx->shibHandler->getError()) {
        $modx->smarty->assign('error_message', $modx->shibHandler->getError());
        $modx->shibHandler->setError();

    } elseif ($modx->getOption('shibboleth.force_shib', $scriptProperties, false) && !isset($_REQUEST['show_login'])) {
        // Skip the login form if we are forcing Shibboleth auth
        $modx->sendRedirect($modx->shibHandler->modxHandlerUrl());
    }
}

elseif ($event == 'OnManagerLoginFormRender' && $modx->getOption('shibboleth.allow_auth', $scriptProperties, true)) {
    // Add the Shibboleth login link to the manager login form
    $url = $modx->shibHandler->modxHandlerUrl();
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
}

elseif ($event == 'OnBeforeManagerLogin' || $event == 'OnBeforeWebLogin') {
    $allow_shib_auth = $modx->getOption('shibboleth.allow_auth', $scriptProperties, true);

    // First, determine if the force_shib setting is present for the user
    $force_shib = $modx->getOption('shibboleth.force_shib', $scriptProperties, false);
    $user = $modx->getObject('modUser', array('username' => $username));

    if ($user) {
        $settings = $user->getSettings();
        if (isset($settings['shibboleth.force_shib']))
            $force_shib = $settings['shibboleth.force_shib'];
    }

    // Next, we deny login if shibboleth auth is both allowed and forced unless
    // this is a Shibboleth authentication attempt
    if ($force_shib && $allow_shib_auth && !$modx->shibHandler->isShibAuthAttempt()) {
        $modx->error->failure($modx->lexicon('shibboleth.force_shib_message'));
    } else {
        $helper->setEventOutput(true);
    }
}

elseif ($event == 'OnManagerAuthentication' || $event == 'OnWebAuthentication') {
    // If this is a Shib auth attempt and the user matches the authenticated
    // user in the Shib handler, skip the password check.
    if ($modx->shibHandler->isShibAuthAttempt() && $modx->shibHandler->matchAuthenticatedUserTo($user)) {
        $helper->setEventOutput(true);
    }
}

elseif ($event == 'OnManagerPageInit') {
    // Correct manager logout links for Shibboleth sessions
    if ($modx->shibHandler->getModxShibSession()) {
        $logout_message = $modx->lexicon('shibboleth.logout_message');
        $show = $modx->getOption('shibboleth.force_shib', $scriptProperties, false) ? '?show_login' : null;

        $modx->regClientStartupHTMLBlock('
            <script type="text/javascript">
            Ext.onReady(function() {
                MODx.on("beforeLogout", function() {
                    var message = MODx.lang.logout_confirm ;
                    MODx.lang.logout_confirm = "'.$logout_message.'"+" "+message;
                });
                MODx.on("afterLogout", function() {
                    location.href = "./'.$show.'";
                    return false;
                });
            });
            </script>
        ');
    }
}