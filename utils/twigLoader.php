<?php
require_once 'vendor/twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem(conf('folders.input'));
return new Twig_Environment($loader);