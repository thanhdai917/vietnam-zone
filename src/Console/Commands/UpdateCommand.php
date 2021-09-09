<?php

namespace Kingfox\VietnamZone\Console\Commands;

use Illuminate\Console\Command;
use Kingfox\VietnamZone\Downloader;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Kingfox\VietnamZone\Imports\VietnamZoneImport;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vietnamzone:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'VietNam Zone Update Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table(config('vietnam-zone.tables.wards'))->truncate();
        DB::table(config('vietnam-zone.tables.districts'))->truncate();
        DB::table(config('vietnam-zone.tables.provinces'))->truncate();

        $tmpFile = app(Downloader::class)->downloadFile();

        $this->info('Updating...');

        Excel::import(new VietnamZoneImport(), $tmpFile);

        File::delete($tmpFile);

        $this->info('Completed');
    }
}
