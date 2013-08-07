<?php

spl_autoload_register(function ($class) {
	$pkgName = 'SSql\\';
    if (strpos($class, $pkgName) === false) {
        return; 
    }
	$class = str_replace($pkgName, '', $class);
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    include __DIR__ . DIRECTORY_SEPARATOR . $classPath;
});
