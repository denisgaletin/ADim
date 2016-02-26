<?php

$loader = new \Phalcon\Loader();



/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->libraryDir,
    )
);

$loader->registerNamespaces(
    array(
        //'ADim'  =>  $config->application->libraryDir.'ADim/',
        'ADim\ServeristApi'  =>  $config->application->libraryDir.'ADim/ServeristApi/'
    )
);

$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
