<?php
/**
 * Cloud 9 Cafe - Environment Configuration Loader
 * 
 * Loads environment variables from .env file
 * Usage: Env::get('DB_HOST', 'localhost')
 */

class Env
{
    private static $loaded = false;
    private static $variables = [];

    /**
     * Load .env file
     * @param string $path Path to .env file
     * @return bool
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return true;
        }

        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }

        if (!file_exists($path)) {
            error_log("Env file not found: " . $path);
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }

                self::$variables[$key] = $value;
                
                // Also set as environment variable
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }

        self::$loaded = true;
        return true;
    }

    /**
     * Get environment variable
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::$loaded) {
            self::load();
        }

        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        // Check system environment
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Get all environment variables
     * @return array
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$variables;
    }

    /**
     * Check if variable exists
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$variables[$key]) || getenv($key) !== false;
    }

    /**
     * Get boolean value
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function getBool($key, $default = false)
    {
        $value = self::get($key, $default);
        return in_array(strtolower($value), ['true', '1', 'yes', 'on'], true);
    }

    /**
     * Get integer value
     * @param string $key
     * @param int $default
     * @return int
     */
    public static function getInt($key, $default = 0)
    {
        return (int) self::get($key, $default);
    }
}

// Auto-load on include
Env::load();
