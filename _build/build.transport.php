<?php
/**
 * Shibboleth MODX Extra build script
 *
 * @package shibboleth
 * @subpackage build
 */

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

/* define package names */
define('PKG_NAME', 'Shibboleth');
define('PKG_NAME_LOWER', 'shibboleth');
define('PKG_VERSION', '1.0.4');
define('PKG_RELEASE', 'pl');

/* define build paths */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'docs' => $root.'core/components/' . PKG_NAME_LOWER . '/docs/',
    'elements' => $root.'core/components/' . PKG_NAME_LOWER . '/elements/',
    'source_core' => $root.'core/components/' . PKG_NAME_LOWER,
);
unset($root);

/* set paths to MODX core and include modX base classes */
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/* create package */
$modx = new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->log(modX::LOG_LEVEL_INFO, 'MODX loaded. Beginning package build...'); flush();

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
$modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package and Namespace'); flush();

/* load system settings */
$settings = include_once $sources['data'] . 'transport.settings.php';
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
if (!is_array($settings)) { $modx->log(modX::LOG_LEVEL_ERROR, 'Adding settings failed'); }
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting, $attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in '.count($settings).' system settings'); flush();
unset($settings, $setting, $attributes);

/* add plugins */
$plugins = include $sources['data'].'transport.plugins.php';
$attributes = array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid', 'event'),
        ),
    ),
);
if (!is_array($plugins)) { $modx->log(modX::LOG_LEVEL_ERROR, 'Adding plugins failed.'); }
foreach ($plugins as $plugin) {
    $vehicle = $builder->createVehicle($plugin, $attributes);
    $resolver = $sources['resolvers'] . strtolower($plugin->get('name')) . '.resolver.php';
    if (file_exists($resolver)) {
        $vehicle->resolve('php', array('source' => $resolver));
        $modx->log(modX::LOG_LEVEL_INFO, 'Added resolver for ' . $plugin->get('name') . ' plugin');
    }
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in '.count($plugins).' plugins'); flush();
unset($plugins, $plugin, $attributes);

/* add category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in category'); flush();

/* add snippets */
$snippets = include $sources['data'] . 'transport.snippets.php';
if (!is_array($snippets)) { $modx->log(modX::LOG_LEVEL_ERROR, 'Adding snippets failed'); }
$category->addMany($snippets, 'Snippets');
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in '.count($snippets).' snippets'); flush();
unset($snippets);

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category, $attr);

/* add file resolver for core directory */
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in file resolvers'); flush();

/* add vehicle to package */
$builder->putVehicle($vehicle);

/* add package information */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));
$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options'); flush();

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
$builder->pack();

$tend = explode(" ", microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built<br />\nExecution time: {$totalTime}\n");

exit();
