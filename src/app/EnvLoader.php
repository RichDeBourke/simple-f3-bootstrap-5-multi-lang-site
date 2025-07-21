<?php

function envLoader() {
    $envFilePath = dirname(__DIR__) . '/.env';
    
    if (!file_exists($envFilePath)) {
        error_log("Environment file not found: $envFilePath");
        return false;
    }
    
    if (!is_readable($envFilePath)) {
        error_log("Environment file is not readable: $envFilePath");
        return false;
    }
    
    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if ($lines === false) {
        error_log("Failed to read environment file: $envFilePath");
        return false;
    }
    
    foreach ($lines as $lineNumber => $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Check if line contains '='
        if (strpos($line, '=') === false) {
            error_log("Invalid environment variable format on line " . ($lineNumber + 1) . ": $line");
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        
        // Validate variable name
        if (empty($name) || !preg_match('/^[A-Z_][A-Z0-9_]*$/i', $name)) {
            error_log("Invalid environment variable name on line " . ($lineNumber + 1) . ": $name");
            continue;
        }
        
        // Process the value
        $value = trim($value);
        
        // Remove surrounding quotes if present
        if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
            (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
            $value = substr($value, 1, -1);
        }
        
        // Handle null values
        if ($value === '' || strtolower($value) === 'null') {
            $value = null;
            putenv("$name"); // Remove from putenv
        } else {
            putenv("$name=$value");
        }
        
        $_ENV[$name] = $value;
    }
    
    return true;
}
