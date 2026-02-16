<?php

namespace Database\Seeders;

use Database\Seeders\Modules\BookingPaymentModuleSeeder;
use Database\Seeders\Modules\EventTicketModuleSeeder;
use Database\Seeders\Modules\UserModuleSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserModuleSeeder::class,
            EventTicketModuleSeeder::class,
            BookingPaymentModuleSeeder::class,
        ]);
    }
}
