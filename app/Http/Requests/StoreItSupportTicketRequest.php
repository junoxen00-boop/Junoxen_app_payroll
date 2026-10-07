<?php

namespace App\Http\Requests;

use App\Models\ItSupportTicket;
use Illuminate\Foundation\Http\FormRequest;

class StoreItSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->employee !== null;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'in:' . implode(',', ItSupportTicket::CATEGORIES)],
            'priority' => ['required', 'string', 'in:' . implode(',', ItSupportTicket::PRIORITIES)],
            'description' => ['required', 'string', 'max:10000'],
        ];
    }
}
