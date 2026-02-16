<?php

namespace App\Modules\Booking\Presentation\Http\Requests;

use App\Application\Booking\DTO\CreateBookingData;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function toDto(): CreateBookingData
    {
        return CreateBookingData::fromArray($this->validated());
    }
}
