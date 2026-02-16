<?php

namespace App\Modules\Auth\Application\Services;

use App\Modules\Auth\Application\Contracts\AuthServiceInterface;
use App\Modules\Auth\Application\DTO\LoginData;
use App\Modules\Auth\Application\DTO\RegisterData;
use App\Modules\User\Domain\Enums\Role;
use App\Modules\User\Domain\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public const PAYLOAD_USER = 'user';
    public const PAYLOAD_TOKEN = 'token';
    public const API_TOKEN_NAME = 'api-token';

    /**
     * @return array{user: User, token: string}
     */
    public function register(RegisterData $data): array
    {
        $user = new User();
        $user->{User::COL_NAME} = $data->name;
        $user->{User::COL_EMAIL} = $data->email;
        $user->{User::COL_PHONE} = $data->phone;
        $user->{User::COL_ROLE} = Role::CUSTOMER;
        $user->{User::COL_PASSWORD} = Hash::make($data->password);
        $user->save();

        return [
            self::PAYLOAD_USER => $user,
            self::PAYLOAD_TOKEN => $user->createToken(self::API_TOKEN_NAME)->plainTextToken,
        ];
    }

    /**
     * @return array{user: User, token: string}|null
     */
    public function login(LoginData $data): ?array
    {
        $user = User::query()->where(User::COL_EMAIL, $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            return null;
        }

        return [
            self::PAYLOAD_USER => $user,
            self::PAYLOAD_TOKEN => $user->createToken(self::API_TOKEN_NAME)->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token !== null) {
            $token->delete();
        }
    }
}
