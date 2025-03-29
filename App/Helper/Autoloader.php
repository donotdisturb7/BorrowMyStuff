<?php
class Autoloader {
    public static function loader($className) {
        // Convert namespace separators to directory separators
        $file = str_replace('\\', DS, $className) . '.php';
        
        // Look for the file in the root path
        $filePath = ROOT_PATH . $file;
        
        if (file_exists($filePath)) {
            require_once $filePath;
            return;
        }
    }
}

spl_autoload_register('Autoloader::loader');