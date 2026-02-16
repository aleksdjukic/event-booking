<?php

namespace App\Modules\Event\Presentation\Http\Requests;

use App\Domain\Event\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class DeleteEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && ($this->user()?->can('delete', $event) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
