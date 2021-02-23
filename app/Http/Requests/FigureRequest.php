<?php declare(strict_types=1);

namespace App\Http\Requests;

class FigureRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ];
    }
}
