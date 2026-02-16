<?php

namespace Database\Seeders\Modules;

use App\Modules\User\Domain\Enums\Role;
use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserModuleSeeder extends Seeder
{
    public function run(): void
    {
        fake()->seed(20260212);

        $demoPassword = Hash::make('password123');

        User::query()->upsert([
            [
                'name' => 'Demo Admin',
                'email' => 'admin@example.com',
                'phone' => '0609990001',
                'role' => Role::ADMIN->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Demo Organizer',
                'email' => 'organizer@example.com',
                'phone' => '0609990002',
                'role' => Role::ORGANIZER->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Demo Customer',
                'email' => 'customer@example.com',
                'phone' => '0609990003',
                'role' => Role::CUSTOMER->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin 1',
                'email' => 'admin1@example.com',
                'phone' => '0600000001',
                'role' => Role::ADMIN->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin 2',
                'email' => 'admin2@example.com',
                'phone' => '0600000002',
                'role' => Role::ADMIN->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Organizer 1',
                'email' => 'organizer1@example.com',
                'phone' => '0610000001',
                'role' => Role::ORGANIZER->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Organizer 2',
                'email' => 'organizer2@example.com',
                'phone' => '0610000002',
                'role' => Role::ORGANIZER->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Organizer 3',
                'email' => 'organizer3@example.com',
                'phone' => '0610000003',
                'role' => Role::ORGANIZER->value,
                'password' => $demoPassword,
                'email_verified_at' => now(),
            ],
            ...$this->customerRows($demoPassword),
        ], ['email'], ['name', 'phone', 'role', 'password', 'email_verified_at']);
    }

    /**
     * @return list<array{name: string, email: string, phone: string, role: string, password: string, email_verified_at: \Illuminate\Support\Carbon}>
     */
    private function customerRows(string $password): array
    {
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'name' => 'Customer '.$i,
                'email' => 'customer'.$i.'@example.com',
                'phone' => '06200000'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'role' => Role::CUSTOMER->value,
                'password' => $password,
                'email_verified_at' => now(),
            ];
        }

        return $rows;
    }
}
