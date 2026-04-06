<?php
/**
 * Cloud 9 Cafe - Configuration Helper
 * 
 * Centralized access to application configuration
 */

require_once __DIR__ . '/Env.php';

class Config
{
    /**
     * Get application name
     * @return string
     */
    public static function appName()
    {
        return Env::get('APP_NAME', 'Cloud 9 Cafe');
    }

    /**
     * Get application URL
     * @return string
     */
    public static function appUrl()
    {
        return Env::get('APP_URL', 'http://localhost/cloud_9_cafe');
    }

    /**
     * Check if in development mode
     * @return bool
     */
    public static function isDevelopment()
    {
        return Env::get('APP_ENV', 'development') === 'development';
    }

    /**
     * Check if in production mode
     * @return bool
     */
    public static function isProduction()
    {
        return Env::get('APP_ENV') === 'production';
    }

    /**
     * Get upload path
     * @return string
     */
    public static function uploadPath()
    {
        return __DIR__ . '/../' . Env::get('UPLOAD_PATH', 'assets/uploads/');
    }

    /**
     * Get upload URL
     * @return string
     */
    public static function uploadUrl()
    {
        return self::appUrl() . '/' . Env::get('UPLOAD_PATH', 'assets/uploads/');
    }

    /**
     * Get max upload size in bytes
     * @return int
     */
    public static function maxUploadSize()
    {
        return Env::getInt('MAX_UPLOAD_SIZE', 5242880); // 5MB default
    }

    /**
     * Get allowed image types
     * @return array
     */
    public static function allowedImageTypes()
    {
        $types = Env::get('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif');
        return explode(',', $types);
    }

    /**
     * Check if feature is enabled
     * @param string $feature
     * @return bool
     */
    public static function featureEnabled($feature)
    {
        $features = [
            'registration' => 'ENABLE_REGISTRATION',
            'wishlist' => 'ENABLE_WISHLIST',
            'reviews' => 'ENABLE_REVIEWS',
            'loyalty' => 'ENABLE_LOYALTY_POINTS',
        ];

        if (isset($features[$feature])) {
            return Env::getBool($features[$feature], true);
        }

        return false;
    }

    /**
     * Get mail configuration
     * @return array
     */
    public static function mailConfig()
    {
        return [
            'host' => Env::get('MAIL_HOST', 'smtp.gmail.com'),
            'port' => Env::getInt('MAIL_PORT', 587),
            'username' => Env::get('MAIL_USERNAME'),
            'password' => Env::get('MAIL_PASSWORD'),
            'encryption' => Env::get('MAIL_ENCRYPTION', 'tls'),
            'from_address' => Env::get('MAIL_FROM_ADDRESS', 'noreply@cloud9cafe.com'),
            'from_name' => Env::get('MAIL_FROM_NAME', 'Cloud 9 Cafe'),
        ];
    }

    /**
     * Get database configuration
     * @return array
     */
    public static function dbConfig()
    {
        return [
            'host' => Env::get('DB_HOST', 'localhost'),
            'port' => Env::get('DB_PORT', '3306'),
            'database' => Env::get('DB_DATABASE', 'cloud_9_cafe'),
            'username' => Env::get('DB_USERNAME', 'root'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
        ];
    }

    /**
     * Get session configuration
     * @return array
     */
    public static function sessionConfig()
    {
        return [
            'name' => Env::get('SESSION_NAME', 'cloud9cafe_session'),
            'lifetime' => Env::getInt('SESSION_LIFETIME', 120),
        ];
    }

    /**
     * Get CSRF token name
     * @return string
     */
    public static function csrfTokenName()
    {
        return Env::get('CSRF_TOKEN_NAME', 'csrf_token');
    }

    /**
     * Get hash cost for password hashing
     * @return int
     */
    public static function hashCost()
    {
        return Env::getInt('HASH_COST', 10);
    }
}
