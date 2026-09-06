<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class FacefoodSettingCommand extends Command
{
    protected $signature = 'facefood:setting
                            {key? : Setting key (tax_rate, tax_percent, delivery_fee, ...)}
                            {value? : New value}
                            {--list : List all settings}';

    protected $description = 'View or update Facefood store settings (tax, delivery fee, currency)';

    public function handle(): int
    {
        if ($this->option('list') || ! $this->argument('key')) {
            $this->table(
                ['Key', 'Value'],
                collect(Setting::publicPayload())->map(fn ($v, $k) => [$k, is_scalar($v) ? (string) $v : json_encode($v)])->values()->all()
            );

            return self::SUCCESS;
        }

        $key = (string) $this->argument('key');
        $value = $this->argument('value');
        if ($value === null) {
            $this->error('Provide a value, or use --list');

            return self::FAILURE;
        }

        if ($key === 'tax_percent') {
            Setting::put('tax_rate', round(((float) $value) / 100, 4));
        } else {
            Setting::put($key, $value);
        }

        $this->info('Updated.');
        $this->table(
            ['Key', 'Value'],
            collect(Setting::publicPayload())->map(fn ($v, $k) => [$k, is_scalar($v) ? (string) $v : json_encode($v)])->values()->all()
        );

        return self::SUCCESS;
    }
}
