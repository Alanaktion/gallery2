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
    protected $signature = 'thumbs:clear
        {-f|--force : Force deletion of all thumbnails}';

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
        if ($this->option('force') || $this->confirm('Are you sure you want to remove all generated thumbnails?')) {
            Storage::deleteDirectory('thumbs');
            $this->info('Thumbnails cleared.');
        }
        return 0;
    }
}
