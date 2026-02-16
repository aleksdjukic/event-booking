<?php

namespace App\Modules\Event\Presentation\Http\Requests;

use App\Modules\Event\Application\DTO\CreateEventData;
use App\Modules\Event\Domain\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Event::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            CreateEventData::INPUT_TITLE => ['required', 'string', 'max:255'],
            CreateEventData::INPUT_DESCRIPTION => ['nullable', 'string'],
            CreateEventData::INPUT_DATE => ['required', 'date'],
            CreateEventData::INPUT_LOCATION => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): CreateEventData
    {
        return CreateEventData::fromArray($this->validated());
    }
}
