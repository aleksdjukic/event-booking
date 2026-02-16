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
                User::COL_NAME => 'Demo Admin',
                User::COL_EMAIL => 'admin@example.com',
                User::COL_PHONE => '0609990001',
                User::COL_ROLE => Role::ADMIN->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Demo Organizer',
                User::COL_EMAIL => 'organizer@example.com',
                User::COL_PHONE => '0609990002',
                User::COL_ROLE => Role::ORGANIZER->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Demo Customer',
                User::COL_EMAIL => 'customer@example.com',
                User::COL_PHONE => '0609990003',
                User::COL_ROLE => Role::CUSTOMER->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Admin 1',
                User::COL_EMAIL => 'admin1@example.com',
                User::COL_PHONE => '0600000001',
                User::COL_ROLE => Role::ADMIN->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Admin 2',
                User::COL_EMAIL => 'admin2@example.com',
                User::COL_PHONE => '0600000002',
                User::COL_ROLE => Role::ADMIN->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Organizer 1',
                User::COL_EMAIL => 'organizer1@example.com',
                User::COL_PHONE => '0610000001',
                User::COL_ROLE => Role::ORGANIZER->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Organizer 2',
                User::COL_EMAIL => 'organizer2@example.com',
                User::COL_PHONE => '0610000002',
                User::COL_ROLE => Role::ORGANIZER->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            [
                User::COL_NAME => 'Organizer 3',
                User::COL_EMAIL => 'organizer3@example.com',
                User::COL_PHONE => '0610000003',
                User::COL_ROLE => Role::ORGANIZER->value,
                User::COL_PASSWORD => $demoPassword,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ],
            ...$this->customerRows($demoPassword),
        ], [User::COL_EMAIL], [User::COL_NAME, User::COL_PHONE, User::COL_ROLE, User::COL_PASSWORD, User::COL_EMAIL_VERIFIED_AT]);
    }

    /**
     * @return list<array{name: string, email: string, phone: string, role: string, password: string, email_verified_at: \Illuminate\Support\Carbon}>
     */
    private function customerRows(string $password): array
    {
        $rows = [];

        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                User::COL_NAME => 'Customer '.$i,
                User::COL_EMAIL => 'customer'.$i.'@example.com',
                User::COL_PHONE => '06200000'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                User::COL_ROLE => Role::CUSTOMER->value,
                User::COL_PASSWORD => $password,
                User::COL_EMAIL_VERIFIED_AT => now(),
            ];
        }

        return $rows;
    }
}
