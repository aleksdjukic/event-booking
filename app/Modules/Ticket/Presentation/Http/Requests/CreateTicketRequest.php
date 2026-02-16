<?php

namespace App\Modules\Ticket\Presentation\Http\Requests;

use App\Modules\Ticket\Application\DTO\CreateTicketData;
use App\Modules\Event\Domain\Models\Event;
use App\Modules\Ticket\Domain\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && ($this->user()?->can('create', [Ticket::class, $event]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function toDto(): CreateTicketData
    {
        return CreateTicketData::fromArray($this->validated());
    }
}
