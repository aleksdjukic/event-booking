<?php

namespace App\Modules\Event\Presentation\Http\Requests;

use App\Modules\Event\Application\DTO\ListEventsData;
use Illuminate\Foundation\Http\FormRequest;

class ListEventsRequest extends FormRequest
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
            ListEventsData::INPUT_DATE => ['nullable', 'date'],
            ListEventsData::INPUT_SEARCH => ['nullable', 'string', 'max:100'],
            ListEventsData::INPUT_LOCATION => ['nullable', 'string', 'max:100'],
            ListEventsData::INPUT_PAGE => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): ListEventsData
    {
        return ListEventsData::fromArray($this->validated());
    }
}
