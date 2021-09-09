<?php

namespace Kingfox\VietnamZone\Console\Commands;

use Illuminate\Console\Command;
use Kingfox\VietnamZone\Downloader;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Kingfox\VietnamZone\Imports\VietnamZoneImport;

class DownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vietnamzone:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'VietNam Zone Download Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Downloading...');

        $tmpFile = app(Downloader::class)->downloadFile();

        $this->info('Importing...');

        Excel::import(new VietnamZoneImport(), $tmpFile);

        File::delete($tmpFile);

        $this->info('Completed');
    }
}
