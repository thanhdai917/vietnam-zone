<?php

namespace Kingfox\VietnamZone\Imports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class VietnamZoneImport implements WithHeadingRow, SkipsOnFailure, ToArray, WithChunkReading
{
    protected $districtMap = [];

    protected $provinceMap = [];

    protected $wardMap = [];

    public function __construct()
    {
        $this->createProvinceMap();
        $this->createDistrictMap();
        $this->createWardMap();
    }

    public function onFailure(Failure ...$failures)
    {

    }

    public function array(array $array)
    {
        $wardImport = [];
        foreach ($array as $item) {
            if (empty($item['ma']) || empty($item['ten'])) {
                continue;
            }

            if (isset($this->wardMap[$item['ma']])) {
                continue;
            }

            $districtId = $this->getDistrictId($item);
            $wardImport[] = [
                config('vietnam-zone.columns.name')        => $item['ten'],
                config('vietnam-zone.columns.gso_id')      => $item['ma'],
                config('vietnam-zone.columns.district_id') => $districtId,
                config('vietnam-zone.columns.slug')        => Str::slug($item['ten']).'-'.$districtId,
                'created_at'                               => now(),
                'updated_at'                               => now(),
            ];
        }

        try {
            DB::table(config('vietnam-zone.tables.wards'))->insert($wardImport);
        } catch (\Exception $e) {
            // Code
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function getProvinceId(array $item)
    {
        return $this->provinceMap[$item['ma_tp']] ?? $this->createProvince($item);
    }

    private function getDistrictId(array $item)
    {
        return $this->districtMap[$item['ma_qh']] ?? $this->createDistrict($item);
    }

    private function createProvince(array $item)
    {
        $provinceId = DB::table(config('vietnam-zone.tables.provinces'))->insertGetId([
            config('vietnam-zone.columns.name')   => $item['tinh_thanh_pho'],
            config('vietnam-zone.columns.gso_id') => $item['ma_tp'],
            config('vietnam-zone.columns.slug')   => Str::slug($item['tinh_thanh_pho']),
            'created_at'                          => now(),
            'updated_at'                          => now(),
        ]);

        $this->provinceMap[$item['ma_tp']] = $provinceId;

        return $provinceId;
    }

    private function createDistrict(array $item)
    {
        $provinceId = $this->getProvinceId($item);

        $districtId = DB::table(config('vietnam-zone.tables.districts'))->insertGetId([
            config('vietnam-zone.columns.name')        => $item['quan_huyen'],
            config('vietnam-zone.columns.gso_id')      => $item['ma_qh'],
            config('vietnam-zone.columns.province_id') => $provinceId,
            config('vietnam-zone.columns.slug')        => Str::slug($item['quan_huyen']),
            'created_at'                               => now(),
            'updated_at'                               => now(),
        ]);

        $this->districtMap[$item['ma_qh']] = $districtId;

        return $districtId;
    }

    private function createProvinceMap()
    {
        $provinces = DB::table(config('vietnam-zone.tables.provinces'))->get();

        $this->provinceMap = $provinces
            ->keyBy(config('vietnam-zone.columns.gso_id'))
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }

    private function createDistrictMap()
    {
        $districts = DB::table(config('vietnam-zone.tables.districts'))->get();

        $this->districtMap = $districts
            ->keyBy(config('vietnam-zone.columns.gso_id'))
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }

    private function createWardMap()
    {
        $wards = DB::table(config('vietnam-zone.tables.wards'))->get();

        $this->wardMap = $wards
            ->keyBy(config('vietnam-zone.columns.gso_id'))
            ->map(function ($item) {
                return $item->id;
            })
            ->toArray();
    }
}