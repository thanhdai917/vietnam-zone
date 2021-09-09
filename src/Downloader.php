<?php

namespace Kingfox\VietnamZone;

use GuzzleHttp\Client;

class Downloader
{
    const DOWNLOAD_URL = 'https://raw.githubusercontent.com/thanhdai917/kingfox-data/master/vietnam-zone.xls';

    /**
     * Download database VietNam Zone
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function downloadFile()
    {
        $image = file_get_contents(self::DOWNLOAD_URL);

        file_put_contents(public_path('vietnam-zone.xls'), $image);

        return public_path('vietnam-zone.xls');
    }
}