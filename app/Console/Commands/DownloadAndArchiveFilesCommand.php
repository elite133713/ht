<?php

namespace App\Console\Commands;

use App\Jobs\DownloadAndArchiveFilesJob;
use Illuminate\Console\Command;
use function config;
use function dd;

class DownloadAndArchiveFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and archive files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->input->getArgument('path');

        DownloadAndArchiveFilesJob::dispatch($path);
    }
}

///aws --endpoint-url=http://localstack:4566 sqs receive-message --queue-url http://localstack:4566/000000000000/my-queue --output json | cat
