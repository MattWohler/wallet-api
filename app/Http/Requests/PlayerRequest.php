<?php declare(strict_types=1);

namespace App\Http\Requests;

class PlayerRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'accounts' => 'required|array',
            'accounts.*' => 'string|alpha_num',
        ];
    }
}
