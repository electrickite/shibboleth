<?php
/**
 * Shibboleth MODX Extra system settings transport script
 *
 * @package shibboleth
 * @subpackage build
 */

$settings = array();

$settings[1]= $modx->newObject('modSystemSetting');
$settings[1]->fromArray(array(
    'key' => 'shibboleth.login_param',
    'value' => 'shibboleth_login',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[2]= $modx->newObject('modSystemSetting');
$settings[2]->fromArray(array(
    'key' => 'shibboleth.create_users',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[3]= $modx->newObject('modSystemSetting');
$settings[3]->fromArray(array(
    'key' => 'shibboleth.rules',
    'value' => '',
    'xtype' => 'textarea',
    'namespace' => 'shibboleth',
    'area' => 'content_protection',
), '', true, true);

$settings[4]= $modx->newObject('modSystemSetting');
$settings[4]->fromArray(array(
    'key' => 'shibboleth.rules_file',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'content_protection',
), '', true, true);

$settings[5]= $modx->newObject('modSystemSetting');
$settings[5]->fromArray(array(
    'key' => 'shibboleth.tv',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'content_protection',
), '', true, true);

$settings[6]= $modx->newObject('modSystemSetting');
$settings[6]->fromArray(array(
    'key' => 'shibboleth.username_attribute',
    'value' => 'REMOTE_USER',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[7]= $modx->newObject('modSystemSetting');
$settings[7]->fromArray(array(
    'key' => 'shibboleth.enforce_session',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[8]= $modx->newObject('modSystemSetting');
$settings[8]->fromArray(array(
    'key' => 'shibboleth.login_path',
    'value' => '/Shibboleth.sso/Login',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[9]= $modx->newObject('modSystemSetting');
$settings[9]->fromArray(array(
    'key' => 'shibboleth.fixenv',
    'value' => '1',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[10]= $modx->newObject('modSystemSetting');
$settings[10]->fromArray(array(
    'key' => 'shibboleth.session_indicator',
    'value' => 'Shib-Session-ID',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[11]= $modx->newObject('modSystemSetting');
$settings[11]->fromArray(array(
    'key' => 'shibboleth.force_shib',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[12]= $modx->newObject('modSystemSetting');
$settings[12]->fromArray(array(
    'key' => 'shibboleth.email_attribute',
    'value' => 'EMAIL',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[13]= $modx->newObject('modSystemSetting');
$settings[13]->fromArray(array(
    'key' => 'shibboleth.fullname_attribute',
    'value' => 'DISPLAYNAME',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'env',
), '', true, true);

$settings[14]= $modx->newObject('modSystemSetting');
$settings[14]->fromArray(array(
    'key' => 'shibboleth.login_text',
    'value' => 'Shibboleth Login',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'misc',
), '', true, true);

$settings[15]= $modx->newObject('modSystemSetting');
$settings[15]->fromArray(array(
    'key' => 'shibboleth.group_rules',
    'value' => '',
    'xtype' => 'textarea',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[16]= $modx->newObject('modSystemSetting');
$settings[16]->fromArray(array(
    'key' => 'shibboleth.force_ssl',
    'value' => '1',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'misc',
), '', true, true);

$settings[17]= $modx->newObject('modSystemSetting');
$settings[17]->fromArray(array(
    'key' => 'shibboleth.allow_auth',
    'value' => '1',
    'xtype' => 'combo-boolean',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[18]= $modx->newObject('modSystemSetting');
$settings[18]->fromArray(array(
    'key' => 'shibboleth.transform_snippet',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

$settings[19]= $modx->newObject('modSystemSetting');
$settings[19]->fromArray(array(
    'key' => 'shibboleth.handler',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'shibboleth',
    'area' => 'user_login',
), '', true, true);

return $settings;
