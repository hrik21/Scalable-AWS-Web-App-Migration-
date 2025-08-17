<?php

namespace App\Core;

/**
 * Main Application class
 */
class Application
{
    private array $config;
    
    public function __construct()
    {
        $this->loadConfiguration();
    }
    
    private function loadConfiguration(): void
    {
        $this->config = [
            'app' => [
                'name' => getenv('APP_NAME') ?: 'AWS PHP Data Platform',
                'env' => getenv('APP_ENV') ?: 'production',
                'debug' => filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            ],
            'database' => [
                'host' => getenv('DB_HOST'),
                'port' => getenv('DB_PORT') ?: 3306,
                'name' => getenv('DB_NAME'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
            ],
            'aws' => [
                'region' => getenv('AWS_REGION') ?: 'us-east-1',
                'secrets_manager_secret' => getenv('SECRETS_MANAGER_SECRET_NAME'),
            ]
        ];
    }
    
    public function getConfig(string $key = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}