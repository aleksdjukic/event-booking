<?php

namespace App\Services\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @param  array<string, mixed>  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = new User();
        $user->name = (string) $data['name'];
        $user->email = (string) $data['email'];
        $user->phone = isset($data['phone']) ? (string) $data['phone'] : null;
        $user->role = Role::CUSTOMER->value;
        $user->password = Hash::make((string) $data['password']);
        $user->save();

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{user: User, token: string}|null
     */
    public function login(array $data): ?array
    {
        $user = User::query()->where('email', (string) $data['email'])->first();

        if (! $user || ! Hash::check((string) $data['password'], $user->password)) {
            return null;
        }

        return [
            'user' => $user,
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
