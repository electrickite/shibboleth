<?php
/**
 * en default topic lexicon file for Shibboleth extra
 */

$_lang['shibboleth.logout_message'] = 'To finish logging out, you must close your web browser.';
$_lang['shibboleth.force_shib_message'] = 'You must log in using Shibboleth.';
$_lang['shibboleth.no_account_message'] = 'That account could not be located. You may not have permission to access this resource.';

$_lang['area_user_login'] = 'User Login';
$_lang['area_env'] = 'Environment';
$_lang['area_content_protection'] = 'Content Protection';
$_lang['area_misc'] = 'Miscellaneous';


/* Settings */
$_lang['setting_shibboleth.rules'] = 'Shibboleth Authorization Rules';
$_lang['setting_shibboleth.rules_desc'] = 'A list of Shibboleth authorization rules, one per line, in Apache directive format';
$_lang['setting_shibboleth.rules_file'] = 'Shibboleth Rule File';
$_lang['setting_shibboleth.rules_file_desc'] = 'Path to a file containing Shibboleth authorization rules in Apache format. Can be a .htaccess file.';
$_lang['setting_shibboleth.tv'] = 'Shibboleth Template Variable';
$_lang['setting_shibboleth.tv_desc'] = 'A template variable to designate a resource as protected by Shibboleth';
$_lang['setting_shibboleth.username_attribute'] = 'Shibboleth Username Attribute';
$_lang['setting_shibboleth.username_attribute_desc'] = 'The Shibboleth attribute that contains a users unique account identifier';
$_lang['setting_shibboleth.login_path'] = 'Shibboleth login path';
$_lang['setting_shibboleth.login_path_desc'] = 'The relative path to the Shibboleth login handler';
$_lang['setting_shibboleth.fixenv'] = 'Fix environment variables';
$_lang['setting_shibboleth.fixenv_desc'] = 'Fixes REDIRECT_ prefixes in certain Apache environments';
$_lang['setting_shibboleth.session_indicator'] = 'Shibboleth Session Indicator';
$_lang['setting_shibboleth.session_indicator_desc'] = 'Environment variable that indicates the presence of a Shibboleth session';
$_lang['setting_shibboleth.force_shib'] = 'Force MODX Shibboleth authentication';
$_lang['setting_shibboleth.force_shib_desc'] = 'Force users to authenticate via Shibboleth and not through any other authentication mechanism';
$_lang['setting_shibboleth.email_attribute'] = 'Shibboleth email attribute';
$_lang['setting_shibboleth.email_attribute_desc'] = 'The Shibboleth attribute that contains the user email address';
$_lang['setting_shibboleth.fullname_attribute'] = 'Shibboleth full name attribute';
$_lang['setting_shibboleth.fullname_attribute_desc'] = 'The Shibboleth attribute that contains the user full name';
$_lang['setting_shibboleth.login_text'] = 'Login text';
$_lang['setting_shibboleth.login_text_desc'] = 'Text for the Shibboleth login link on the MODX manager login form';
$_lang['setting_shibboleth.group_rules'] = 'Group mapping rules';
$_lang['setting_shibboleth.group_rules_desc'] = 'Assign MODX users to groups based on Shibboleth attributes. One rule per line. Ex: Administrator "Super User" AttributeName value1 value2';
$_lang['setting_shibboleth.force_ssl'] = 'Force SSL';
$_lang['setting_shibboleth.force_ssl_desc'] = 'Force all Shibboleth communication over SSL';
$_lang['setting_shibboleth.allow_auth'] = 'Allow MODX user authentication';
$_lang['setting_shibboleth.allow_auth_desc'] = 'Allows MODX system users to authenticate via Shibboleth.';
$_lang['setting_shibboleth.transform_snippet'] = 'Username transform snippet';
$_lang['setting_shibboleth.transform_snippet_desc'] = 'Name of a snippet used to transform Shibboleth username format into MODX usernames. Shibboleth username will be passed in the $username parameter and the snippet should return a username to check against the MODX database. Ex: a snippet could transform Shibboleth usernames like \'DOMAINusername\' to \'username\'';
$_lang['setting_shibboleth.handler'] = 'Shibboleth handler';
$_lang['setting_shibboleth.handler_desc'] = 'The resource ID of the Shibboleth login handler document';
$_lang['setting_shibboleth.create_users'] = 'Create MODX users';
$_lang['setting_shibboleth.create_users_desc'] = 'Create MODX user accounts for Shibboleth users.';
$_lang['setting_shibboleth.enforce_session'] = 'Enforce Shibboleth session';
$_lang['setting_shibboleth.enforce_session_desc'] = 'Logs out a MODX user that was authenticated with Shibboleth if their Shibboleth session ends.';
$_lang['setting_shibboleth.login_param'] = 'Login URL parameter';
$_lang['setting_shibboleth.login_param_desc'] = 'The URL parameter used to start a Shibboleth login attempt';
