<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Setting::defaults() as $key => $meta) {
            Setting::query()->firstOrCreate(
                ['key' => $key],
                [
                    'value' => $meta['value'],
                    'type' => $meta['type'],
                    'group' => 'store',
                    'label' => $meta['label'],
                ]
            );
        }
    }
}
