<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use function config;

class AwsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(S3Client::class, function () {
            return new S3Client(
                [
                    'region' => config('services.aws.region'),
                    'version' => config('services.aws.version'),
                    'credentials' => config('services.aws.credentials'),
                    'endpoint' => config('services.aws.endpoint'),
                    'use_path_style_endpoint' => true,
                ]
                );
        });
    }
}
