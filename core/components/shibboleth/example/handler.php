<?php
/**
 * MODX Shibboleth login handler
 *
 * DESCRIPTION
 *
 * Handles login requests for Shibboleth accounts
 */

// Set the path to config.core.php
//$config_path = '/path/to/config.core.php';


if (!isset($config_path)) {
    $config_path = dirname(__FILE__) . '/config.core.php';
}

@include($config_path);
if (!defined('MODX_CORE_PATH')) define('MODX_CORE_PATH', dirname(dirname(__FILE__)) . '/core/');
if (!include_once(MODX_CORE_PATH . 'model/modx/modx.class.php')) die();

$modx = new modX('', array(xPDO::OPT_CONN_INIT => array(xPDO::OPT_CONN_MUTABLE => true)));

/* initialize the proper context */
$ctx = isset($_REQUEST['ctx']) && !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'mgr';
$modx->initialize($ctx);
$modx->getRequest();
$modx->request->sanitizeRequest();

$corePath = $modx->getObject('modNamespace', 'shibboleth')->getCorePath();
require_once $corePath.'autoload.php';
$modx->lexicon->load('shibboleth:default');

$handler = new ShibbolethHandler($modx);
$handler->fixEnvironment();
$handler->doLogin();
