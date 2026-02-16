<?php

namespace App\Modules\Auth\Presentation\Http\Requests;

use App\Modules\Auth\Application\DTO\RegisterData;
use App\Modules\User\Domain\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            RegisterData::INPUT_NAME => ['required', 'string', 'max:255'],
            RegisterData::INPUT_EMAIL => ['required', 'string', 'email', 'max:255', Rule::unique(User::TABLE, User::COL_EMAIL)],
            RegisterData::INPUT_PASSWORD => ['required', 'string', 'min:8', 'confirmed'],
            RegisterData::INPUT_PHONE => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): RegisterData
    {
        return RegisterData::fromArray($this->validated());
    }
}
