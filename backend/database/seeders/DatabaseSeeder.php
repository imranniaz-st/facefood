<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::updateOrCreate(
            ['email' => 'demo@facefood.pk'],
            [
                'name' => 'Facefood Guest',
                'password' => Hash::make('password'),
                'phone' => '+92 300 1234567',
                'avatar_url' => 'https://i.pravatar.cc/150?u=facefood',
                'is_premium' => true,
                'is_admin' => true,
                'location' => 'Karachi, Pakistan',
                'email_verified_at' => now(),
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $demo->id, 'line1' => 'PWD Housing Society'],
            [
                'label' => 'Home',
                'line2' => 'Block B',
                'city' => 'Islamabad',
                'area' => 'PWD',
                'latitude' => 33.5651,
                'longitude' => 73.0169,
                'is_default' => true,
            ]
        );

        $this->call([
            SettingSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductExtraSeeder::class,
            DealSeeder::class,
        ]);

        Favorite::query()->where('user_id', $demo->id)->delete();
    }
}
