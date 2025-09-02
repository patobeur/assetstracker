<?php

namespace app\core;

use RuntimeException;

spl_autoload_register(function ($class) {
    $classPath = '../' . str_replace(['\\'], ['/'], $class) . '.php';

    if (file_exists($classPath)) {
        require_once $classPath;
    } else {
        throw new RuntimeException("Classe non trouvée : $classPath");
        // die("Classe non trouvée : $classPath");
    }
});
