<?php

require 'core/ClassLoader.php';

$loader = new ClassLoader();
$loader->registerDirectory(dirname(__FILE__) . '/core');
$loader->registerDirectory(dirname(__FILE__) . 'models');
$loader->register();