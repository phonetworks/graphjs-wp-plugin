<?php
/*
Plugin Name: GraphJS
Description: Easy way to install GraphJS on Wordpress
Version: 1.0.0
*/

// PSR-4 autoloading
include 'src/Psr4AutoloaderClass.php';

$psr4Autoloader = new Graphjs\Psr4AutoloaderClass();
$psr4Autoloader->addNamespace('Graphjs', __DIR__ . '/src');
$psr4Autoloader->register();

$plugin = new \Graphjs\WordpressPlugin(__FILE__, __DIR__, new \Graphjs\Graphjs());
$plugin->bootstrap();
