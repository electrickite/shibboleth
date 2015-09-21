<?php
/**
 * Shibboleth autoloading function
 *      
 * @param string $class
 *   The fully-qualified class name.
 */
spl_autoload_register(function ($class) {
    $base_dir = __DIR__.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR;

    $file = $base_dir . strtolower($class) . '.class.php';

    if (file_exists($file)) {
        require $file;
    }
});