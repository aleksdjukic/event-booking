<?php

namespace App\Modules\Ticket\Presentation\Http\Requests;

use App\Modules\Ticket\Application\DTO\UpdateTicketData;
use App\Modules\Ticket\Domain\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket && ($this->user()?->can('update', $ticket) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            UpdateTicketData::INPUT_TYPE => ['sometimes', 'string', 'max:50'],
            UpdateTicketData::INPUT_PRICE => ['sometimes', 'numeric', 'min:0'],
            UpdateTicketData::INPUT_QUANTITY => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function toDto(): UpdateTicketData
    {
        return UpdateTicketData::fromArray($this->validated());
    }
}
