<?php
/**
 * systemSettings transport file for Shibboleth extra
 *
 * @package shibboleth
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $systemSettings */


$systemSettings = array();

$systemSettings[1] = $modx->newObject('modSystemSetting');
$systemSettings[1]->fromArray(array (
  'key' => 'shibboleth.login_param',
  'value' => 'shibboleth_login',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Login URL parameter',
  'description' => 'The URL parameter used to start a Shibboleth login attempt',
), '', true, true);
$systemSettings[2] = $modx->newObject('modSystemSetting');
$systemSettings[2]->fromArray(array (
  'key' => 'shibboleth.create_users',
  'value' => '0',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Create MODX users',
  'description' => 'Create MODX user accounts for Shibboleth users.',
), '', true, true);
$systemSettings[3] = $modx->newObject('modSystemSetting');
$systemSettings[3]->fromArray(array (
  'key' => 'shibboleth.rules',
  'value' => '',
  'xtype' => 'textarea',
  'namespace' => 'shibboleth',
  'area' => 'content_protection',
  'name' => 'Shibboleth Authorization Rules',
  'description' => 'A list of Shibboleth authorization rules, one per line, in Apache directive format',
), '', true, true);
$systemSettings[4] = $modx->newObject('modSystemSetting');
$systemSettings[4]->fromArray(array (
  'key' => 'shibboleth.rules_file',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'content_protection',
  'name' => 'Shibboleth Rule File',
  'description' => 'Path to a file containing Shibboleth authorization rules in Apache format. Can be a .htaccess file.',
), '', true, true);
$systemSettings[5] = $modx->newObject('modSystemSetting');
$systemSettings[5]->fromArray(array (
  'key' => 'shibboleth.tv',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'content_protection',
  'name' => 'Shibboleth Template Variable',
  'description' => 'A template variable to designate a resource as protected by Shibboleth',
), '', true, true);
$systemSettings[6] = $modx->newObject('modSystemSetting');
$systemSettings[6]->fromArray(array (
  'key' => 'shibboleth.username_attribute',
  'value' => 'REMOTE_USER',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Shibboleth Username Attribute',
  'description' => 'The Shibboleth attribute that contains a users unique account identifier',
), '', true, true);
$systemSettings[7] = $modx->newObject('modSystemSetting');
$systemSettings[7]->fromArray(array (
  'key' => 'shibboleth.enforce_session',
  'value' => '0',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Enforce Shibboleth session',
  'description' => 'Logs out a MODX user that was authenticated with Shibboleth if their Shibboleth session ends.',
), '', true, true);
$systemSettings[8] = $modx->newObject('modSystemSetting');
$systemSettings[8]->fromArray(array (
  'key' => 'shibboleth.login_path',
  'value' => '/Shibboleth.sso/Login',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Shibboleth login path',
  'description' => 'The relative path to the Shibboleth login handler',
), '', true, true);
$systemSettings[9] = $modx->newObject('modSystemSetting');
$systemSettings[9]->fromArray(array (
  'key' => 'shibboleth.fixenv',
  'value' => '1',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Fix environment variables',
  'description' => 'Fixes REDIRECT_ prefixes in certain Apache environments',
), '', true, true);
$systemSettings[10] = $modx->newObject('modSystemSetting');
$systemSettings[10]->fromArray(array (
  'key' => 'shibboleth.session_indicator',
  'value' => 'Shib-Session-ID',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Shibboleth session ID',
  'description' => 'Environment variable that holds the Shibboleth session ID. ',
), '', true, true);
$systemSettings[11] = $modx->newObject('modSystemSetting');
$systemSettings[11]->fromArray(array (
  'key' => 'shibboleth.force_shib',
  'value' => '0',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Force MODX Shibboleth authentication',
  'description' => 'Force users to authenticate via Shibboleth and not through any other authentication mechanism',
), '', true, true);
$systemSettings[12] = $modx->newObject('modSystemSetting');
$systemSettings[12]->fromArray(array (
  'key' => 'shibboleth.email_attribute',
  'value' => 'EMAIL',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Shibboleth email attribute',
  'description' => 'The Shibboleth attribute that contains the user email address',
), '', true, true);
$systemSettings[13] = $modx->newObject('modSystemSetting');
$systemSettings[13]->fromArray(array (
  'key' => 'shibboleth.fullname_attribute',
  'value' => 'DISPLAYNAME',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'env',
  'name' => 'Shibboleth full name attribute',
  'description' => 'The Shibboleth attribute that contains the user full name',
), '', true, true);
$systemSettings[14] = $modx->newObject('modSystemSetting');
$systemSettings[14]->fromArray(array (
  'key' => 'shibboleth.login_text',
  'value' => 'Shibboleth Login',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'misc',
  'name' => 'Login text',
  'description' => 'Text for the Shibboleth login link on the MODX manager login form',
), '', true, true);
$systemSettings[15] = $modx->newObject('modSystemSetting');
$systemSettings[15]->fromArray(array (
  'key' => 'shibboleth.group_rules',
  'value' => '',
  'xtype' => 'textarea',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Group mapping rules',
  'description' => 'Assign MODX users to groups based on Shibboleth attributes. One rule per line. Ex: Administrator "Super User" AttributeName value1 value2',
), '', true, true);
$systemSettings[16] = $modx->newObject('modSystemSetting');
$systemSettings[16]->fromArray(array (
  'key' => 'shibboleth.force_ssl',
  'value' => '1',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'misc',
  'name' => 'Force SSL',
  'description' => 'Force all Shibboleth communication over SSL',
), '', true, true);
$systemSettings[17] = $modx->newObject('modSystemSetting');
$systemSettings[17]->fromArray(array (
  'key' => 'shibboleth.allow_auth',
  'value' => '1',
  'xtype' => 'combo-boolean',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Allow MODX user authentication',
  'description' => 'Allows MODX system users to authenticate via Shibboleth.',
), '', true, true);
$systemSettings[18] = $modx->newObject('modSystemSetting');
$systemSettings[18]->fromArray(array (
  'key' => 'shibboleth.transform_snippet',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Username transform snippet',
  'description' => 'Name of a snippet used to transform Shibboleth username format into MODX usernames. Shibboleth username will be passed in the $username parameter and the snippet should return a username to check against the MODX database. Ex: a snippet could transform Shibboleth usernames like \'DOMAIN\\username\' to \'username\'',
), '', true, true);
$systemSettings[19] = $modx->newObject('modSystemSetting');
$systemSettings[19]->fromArray(array (
  'key' => 'shibboleth.handler',
  'value' => '',
  'xtype' => 'textfield',
  'namespace' => 'shibboleth',
  'area' => 'user_login',
  'name' => 'Shibboleth handler',
  'description' => 'The resource ID of the Shibboleth login handler document',
), '', true, true);
return $systemSettings;
