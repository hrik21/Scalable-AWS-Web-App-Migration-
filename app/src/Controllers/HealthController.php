<?php

namespace App\Controllers;

/**
 * Health check controller for load balancer monitoring
 */
class HealthController
{
    public function check(): array
    {
        $status = 'healthy';
        $checks = [];
        
        // Check database connectivity (placeholder)
        $checks['database'] = $this->checkDatabase();
        
        // Check disk space
        $checks['disk_space'] = $this->checkDiskSpace();
        
        // Check memory usage
        $checks['memory'] = $this->checkMemory();
        
        // Determine overall status
        $allHealthy = array_reduce($checks, function($carry, $check) {
            return $carry && $check['status'] === 'ok';
        }, true);
        
        if (!$allHealthy) {
            $status = 'unhealthy';
        }
        
        return [
            'status' => $status,
            'timestamp' => date('c'),
            'checks' => $checks,
            'version' => '1.0.0'
        ];
    }
    
    private function checkDatabase(): array
    {
        // Placeholder for database connectivity check
        // This will be implemented when database module is added
        return [
            'status' => 'ok',
            'message' => 'Database connectivity check not implemented yet'
        ];
    }
    
    private function checkDiskSpace(): array
    {
        $freeBytes = disk_free_space('/');
        $totalBytes = disk_total_space('/');
        $usedPercent = (($totalBytes - $freeBytes) / $totalBytes) * 100;
        
        $status = $usedPercent < 90 ? 'ok' : 'warning';
        if ($usedPercent > 95) {
            $status = 'critical';
        }
        
        return [
            'status' => $status,
            'used_percent' => round($usedPercent, 2),
            'free_bytes' => $freeBytes,
            'total_bytes' => $totalBytes
        ];
    }
    
    private function checkMemory(): array
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        // Convert memory limit to bytes
        $limitBytes = $this->convertToBytes($memoryLimit);
        $usedPercent = ($memoryUsage / $limitBytes) * 100;
        
        $status = $usedPercent < 80 ? 'ok' : 'warning';
        if ($usedPercent > 90) {
            $status = 'critical';
        }
        
        return [
            'status' => $status,
            'used_percent' => round($usedPercent, 2),
            'current_usage' => $memoryUsage,
            'peak_usage' => $memoryPeak,
            'limit' => $limitBytes
        ];
    }
    
    private function convertToBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $number = (int) substr($value, 0, -1);
        
        switch ($unit) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return (int) $value;
        }
    }
}