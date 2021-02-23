<?php declare(strict_types=1);

namespace App\Http\Requests\Histories;

use App\Http\Requests\AbstractRequest;

class TransactionRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'account' => 'string',
            'type' => 'string',
            'providerId' => 'integer',
            'providerTransactionId' => 'string',
            'roundId' => 'string',
            'startDate' => 'date',
            'endDate' => 'date|after_or_equal:startDate',
        ];
    }
}
