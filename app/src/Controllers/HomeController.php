<?php

namespace App\Controllers;

/**
 * Home controller for basic application information
 */
class HomeController
{
    public function index(): array
    {
        return [
            'message' => 'Welcome to AWS PHP Data Platform',
            'version' => '1.0.0',
            'environment' => getenv('APP_ENV') ?: 'production',
            'timestamp' => date('c'),
            'endpoints' => [
                'health' => '/health',
                'api' => [
                    'data_ingest' => 'POST /api/data/ingest',
                    'jobs_list' => 'GET /api/data/jobs',
                    'job_detail' => 'GET /api/data/jobs/{id}'
                ]
            ]
        ];
    }
}