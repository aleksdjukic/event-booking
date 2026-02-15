<?php

namespace Tests\Unit\Domain;

use App\Domain\User\Enums\Role;
use App\Domain\User\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleHelpersTest extends TestCase
{
    public function test_role_value_handles_string_and_enum_roles(): void
    {
        $enumUser = new User();
        $enumUser->role = Role::ADMIN;

        $stringUser = new User();
        $stringUser->role = 'customer';

        $this->assertSame('admin', $enumUser->roleValue());
        $this->assertSame('customer', $stringUser->roleValue());
    }

    public function test_has_role_accepts_enum_and_string(): void
    {
        $user = new User();
        $user->role = Role::ORGANIZER;

        $this->assertTrue($user->hasRole(Role::ORGANIZER));
        $this->assertTrue($user->hasRole('organizer'));
        $this->assertFalse($user->hasRole(Role::CUSTOMER));
    }

    public function test_has_any_role_returns_true_on_first_match(): void
    {
        $user = new User();
        $user->role = 'customer';

        $this->assertTrue($user->hasAnyRole([Role::ADMIN, Role::CUSTOMER]));
        $this->assertFalse($user->hasAnyRole([Role::ADMIN, Role::ORGANIZER]));
    }
}
