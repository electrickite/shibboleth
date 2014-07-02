<?php

 /*               DO NOT EDIT THIS FILE

  Edit the file in the MyComponent config directory
  and run ExportObjects

 */



$packageNameLower = 'shibboleth'; /* No spaces, no dashes */

$components = array(
    /* These are used to define the package and set values for placeholders */
    'packageName' => 'Shibboleth',  /* No spaces, no dashes */
    'packageNameLower' => $packageNameLower,
    'packageDescription' => 'Add Shibboleth authentication to MODX Revolution',
    'version' => '0.4.0',
    'release' => 'pl',
    'author' => 'Corey Hinshaw',
    'email' => 'hinshaw.25@osu.edu',
    'packageDocumentationUrl' => 'https://github.com/osucomm/shibboleth',
    'copyright' => '2014',

    /* no need to edit this except to change format */
    'createdon' => strftime('%m-%d-%Y'),

    /* two-letter code of your primary language */
    'primaryLanguage' => 'en',

    /* Set directory and file permissions for project directories */
    'dirPermission' => 0755,  /* No quotes!! */
    'filePermission' => 0644, /* No quotes!! */

    /* Define source and target directories */

    /* path to MyComponent source files */
    'mycomponentRoot' => $this->modx->getOption('mc.root', null,
        MODX_CORE_PATH . 'components/mycomponent/'),

    /* path to new project root */
    'targetRoot' => MODX_ASSETS_PATH . 'mycomponents/' . $packageNameLower . '/',



    /* ************************ NAMESPACE(S) ************************* */
    /* (optional) Typically, there's only one namespace which is set
     * to the $packageNameLower value. Paths should end in a slash
    */

    'namespaces' => array(
        'shibboleth' => array(
            'name' => 'shibboleth',
            'path' => '{core_path}components/shibboleth/',
        ),

    ),


    /* ************************* CATEGORIES *************************** */
    /* (optional) List of categories. This is only necessary if you
     * need to categories other than the one named for packageName
     * or want to nest categories.
    */

    'categories' => array(
        'shibboleth' => array(
            'category' => 'Shibboleth',
            'parent' => '',  /* top level category */
        ),
    ),


    /* ************************* ELEMENTS **************************** */

    /* Array containing elements for your extra. 'category' is required
       for each element, all other fields are optional.
       Property Sets (if any) must come first!

       The standard file names are in this form:
           SnippetName.snippet.php
           PluginName.plugin.php
           ChunkName.chunk.html
           TemplateName.template.html

       If your file names are not standard, add this field:
          'filename' => 'actualFileName',
    */


    /* *********************** NEW SYSTEM SETTINGS ************************ */

    /* If your extra needs new System Settings, set their field values here.
     * You can also create or edit them in the Manager (System -> System Settings),
     * and export them with exportObjects. If you do that, be sure to set
     * their namespace to the lowercase package name of your extra */

    'newSystemSettings' => array(
        'shibboleth.rules' => array(
          'key' => 'shibboleth.rules',
          'value' => '',
          'xtype' => 'textarea',
          'namespace' => 'shibboleth',
          'area' => 'Content Protection',
          'name' => 'Shibboleth Authorization Rules',
          'description' => 'A list of Shibboleth authorization rules, one per line, in Apache directive format',
        ),
        'shibboleth.rules_file' => array(
          'key' => 'shibboleth.rules_file',
          'value' => '',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Content Protection',
          'name' => 'Shibboleth Rule File',
          'description' => 'Path to a file containing Shibboleth authorization rules in Apache format. Can be a .htaccess file.',
        ),
        'shibboleth.tv' => array(
          'key' => 'shibboleth.tv',
          'value' => '',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Content Protection',
          'name' => 'Shibboleth Template Variable',
          'description' => 'A template variable to designate a resource as protected by Shibboleth',
        ),
        'shibboleth.username_attribute' => array(
          'key' => 'shibboleth.username_attribute',
          'value' => 'REMOTE_USER',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Shibboleth Username Attribute',
          'description' => 'The Shibboleth attribute that contains a users unique account identifier',
        ),
        'shibboleth.login_path' => array(
          'key' => 'shibboleth.login_path',
          'value' => '/Shibboleth.sso/Login',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Shibboleth login path',
          'description' => 'The relative path to the Shibboleth login handler',
        ),
        'shibboleth.fixenv' => array(
          'key' => 'shibboleth.fixenv',
          'value' => '1',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Fix environment variables',
          'description' => 'Fixes REDIRECT_ prefixes in certain Apache environments',
        ),
        'shibboleth.session_indicator' => array(
          'key' => 'shibboleth.session_indicator',
          'value' => 'Shib-Session-ID',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Shibboleth Session Indicator',
          'description' => 'Environment variable that indicates the presence of a Shibboleth session',
        ),
        'shibboleth.force_shib' => array(
          'key' => 'shibboleth.force_shib',
          'value' => '0',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Force MODX Shibboleth authentication',
          'description' => 'Force users to authenticate via Shibboleth and not through any other authentication mechanism',
        ),
        'shibboleth.email_attribute' => array(
          'key' => 'shibboleth.email_attribute',
          'value' => 'EMAIL',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Shibboleth email attribute',
          'description' => 'The Shibboleth attribute that contains the user email address',
        ),
        'shibboleth.fullname_attribute' => array(
          'key' => 'shibboleth.fullname_attribute',
          'value' => 'DISPLAYNAME',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Environment',
          'name' => 'Shibboleth full name attribute',
          'description' => 'The Shibboleth attribute that contains the user full name',
        ),
        'shibboleth.login_text' => array(
          'key' => 'shibboleth.login_text',
          'value' => 'Shibboleth Login',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'Misc',
          'name' => 'Login text',
          'description' => 'Text for the Shibboleth login link on the MODX manager login form',
        ),
        'shibboleth.group_rules' => array(
          'key' => 'shibboleth.group_rules',
          'value' => '',
          'xtype' => 'textarea',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Group mapping rules',
          'description' => 'Assign MODX users to groups based on Shibboleth attributes. One rule per line. Ex: Administrator "Super User" AttributeName value1 value2',
        ),
        'shibboleth.force_ssl' => array(
          'key' => 'shibboleth.force_ssl',
          'value' => '1',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'Misc',
          'name' => 'Force SSL',
          'description' => 'Force all Shibboleth communication over SSL',
        ),
        'shibboleth.allow_auth' => array(
          'key' => 'shibboleth.allow_auth',
          'value' => '1',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Allow MODX user authentication',
          'description' => 'Allows MODX system users to authenticate via Shibboleth.',
        ),
        'shibboleth.transform_snippet' => array(
          'key' => 'shibboleth.transform_snippet',
          'value' => '',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Username transform snippet',
          'description' => 'Name of a snippet used to transform Shibboleth username format into MODX usernames. Shibboleth username will be passed in the $username parameter and the snippet should return a username to check against the MODX database. Ex: a snippet could transform Shibboleth usernames like \'DOMAIN\\username\' to \'username\'',
        ),
        'shibboleth.handler' => array(
          'key' => 'shibboleth.handler',
          'value' => '',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Shibboleth handler',
          'description' => 'ID of the resource containing the Shibboleth handler snippet or the full URL of the handler script',
        ),
        'shibboleth.create_users' => array(
          'key' => 'shibboleth.create_users',
          'value' => '0',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Create MODX users',
          'description' => 'Create MODX user accounts for Shibboleth users.',
        ),
        'shibboleth.enforce_session' => array(
          'key' => 'shibboleth.enforce_session',
          'value' => '0',
          'xtype' => 'combo-boolean',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Enforce Shibboleth session',
          'description' => 'Logs out a MODX user that was authenticated with Shibboleth if their Shibboleth session ends.',
        ),
        'shibboleth.login_param' => array(
          'key' => 'shibboleth.login_param',
          'value' => 'shibboleth_login',
          'xtype' => 'textfield',
          'namespace' => 'shibboleth',
          'area' => 'User Login',
          'name' => 'Login URL parameter',
          'description' => 'The URL parameter used to start a Shibboleth login attempt',
        ),
    ),


    'elements' => array(

        'snippets' => array(
            'shibAuth' => array(
                'category' => 'Shibboleth',
                'description' => 'Tests whether the user has a valid Shibboleth session',
                'static' => false,
            ),
            'shibLoginUrl' => array(
                'category' => 'Shibboleth',
                'description' => 'Returns the full URL for the shibboleth session initiator',
                'static' => false,
            ),
            'shibAttr' => array(
                'category' => 'Shibboleth',
                'description' => 'Displays the value of a Shibboleth attribute for a user',
                'static' => false,
            ),
            'shibHandler' => array(
                'category' => 'Shibboleth',
                'description' => 'Routes users through Shibboleth web authentication and logs them in to MODX when they have been authenticated.',
                'static' => false,
            ),
            'shibHandlerUrl' => array(
                'category' => 'Shibboleth',
                'description' => 'Generates a URL for the handler resource used to process MODX user logins via Shibboleth',
                'static' => false,
            ),
        ),

        'plugins' => array(
            'ShibbolethProtect' => array(
                'category' => 'Shibboleth',
                'description' => 'Secures certain pages via Shibboleth authentication and allow Shibboleth logins for MODX users',
                'static' => false,
                'events' => array(
                    'OnHandleRequest' => array(),
                    'OnWebPagePrerender' => array(),
                    'OnBeforeManagerLogin' => array(),
                    'OnBeforeWebLogin' => array(),
                    'OnManagerLoginFormPrerender' => array(),
                    'OnManagerLoginFormRender' => array(),
                    'OnManagerPageInit' => array(),
                ),
            ),
        ),
    ),
    /* (optional) will make all element objects static - 'static' field above will be ignored */
    'allStatic' => false,


    /* Array of languages for which you will have language files,
     *  and comma-separated list of topics
     *  ('.inc.php' will be added as a suffix). */
    'languages' => array(
        'en' => array(
            'default',
            'properties',
            'forms',
        ),
    ),


    /* ********************************************* */
    /* Define basic directories and files to be created in project*/

    'docs' => array(
        'readme.txt',
        'license.txt',
        'changelog.txt',
    ),

    /* (optional) Description file for GitHub project home page */
    'readme.md' => true,
    /* assume every package has a core directory */
    'hasCore' => true,


    /* ********************************************* */
    /* (optional) Only necessary if you will have class files.
     *
     * Array of class files to be created.
     *
     * Format is:
     *
     * 'ClassName' => 'directory:filename',
     *
     * or
     *
     *  'ClassName' => 'filename',
     *
     * ('.class.php' will be appended automatically)
     *
     *  Class file will be created as:
     * yourcomponent/core/components/yourcomponent/model/[directory/]{filename}.class.php
     *
     * Set to array() if there are no classes. */
    'classes' => array(
        'shibboleth' => 'shibboleth',
    ),


    /* *******************************************
     * These settings control exportObjects.php  *
     ******************************************* */
    /* ExportObjects will update existing files. If you set dryRun
       to '1', ExportObjects will report what it would have done
       without changing anything. Note: On some platforms,
       dryRun is *very* slow  */

    'dryRun' => '0',

    /* Array of elements to export. All elements set below will be handled.
     *
     * To export resources, be sure to list pagetitles and/or IDs of parents
     * of desired resources
    */
    'process' => array(
        'snippets',
        'plugins',
        'systemSettings',
    ),


    /* ******************** LEXICON HELPER SETTINGS ***************** */
    /* These settings are used by LexiconHelper */
    'rewriteCodeFiles' => false,  /* remove ~~descriptions */
    'rewriteLexiconFiles' => true, /* automatically add missing strings to lexicon files */
    /* ******************************************* */

    /* Array of aliases used in code for the properties array.
     * Used by the checkproperties utility to check properties in code against
     * the properties in your properties transport files.
     * if you use something else, add it here (OK to remove ones you never use.
     * Search also checks with '$this->' prefix -- no need to add it here. */
    'scriptPropertiesAliases' => array(
        'props',
        'sp',
        'config',
        'scriptProperties',
    ),
);

return $components;
