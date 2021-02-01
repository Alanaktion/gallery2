<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbnails:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all generated thumbnail images';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Storage::deleteDirectory('thumbs');
        return 0;
    }
}
