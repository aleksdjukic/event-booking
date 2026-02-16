<?php

namespace App\Modules\User\Presentation\Http\Resources;

use App\Modules\User\Domain\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    private const OUT_ID = User::COL_ID;
    private const OUT_NAME = User::COL_NAME;
    private const OUT_EMAIL = User::COL_EMAIL;
    private const OUT_PHONE = User::COL_PHONE;
    private const OUT_ROLE = User::COL_ROLE;
    private const OUT_CREATED_AT = User::COL_CREATED_AT;
    private const OUT_UPDATED_AT = User::COL_UPDATED_AT;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            self::OUT_ID => $this->{User::COL_ID},
            self::OUT_NAME => $this->{User::COL_NAME},
            self::OUT_EMAIL => $this->{User::COL_EMAIL},
            self::OUT_PHONE => $this->{User::COL_PHONE},
            self::OUT_ROLE => $this->roleValue(),
            self::OUT_CREATED_AT => $this->{User::COL_CREATED_AT},
            self::OUT_UPDATED_AT => $this->{User::COL_UPDATED_AT},
        ];
    }
}
