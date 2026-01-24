<?php
/**
 * =========================================================
 * Autoloader Class
 * =========================================================
 * 
 * PSR-4 style autoloading
 * Memuat class secara otomatis berdasarkan namespace
 */

spl_autoload_register(function ($class) {
    // Mapping namespace ke direktori
    $namespaceMap = [
        'Core\\'       => CORE_PATH,
        'App\\Controllers\\' => APP_PATH . 'Controllers' . DIRECTORY_SEPARATOR,
        'App\\Models\\'      => APP_PATH . 'Models' . DIRECTORY_SEPARATOR,
        'App\\Middleware\\'  => APP_PATH . 'Middleware' . DIRECTORY_SEPARATOR,
    ];
    
    foreach ($namespaceMap as $prefix => $baseDir) {
        // Periksa apakah class menggunakan namespace ini
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        
        // Dapatkan nama class relatif
        $relativeClass = substr($class, $len);
        
        // Ubah namespace separator ke directory separator
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        
        // Jika file ada, muat
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});
