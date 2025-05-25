<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CoopB2BService
{
    protected function headers()
    {
        return [
            'serviceName' => config('coop.service_name'),
            'messageID' => Str::uuid()->toString(),
            'connectionID' => config('coop.connection_id'),
            'connectionPassword' => config('coop.connection_password'),
        ];
    }

    public function validateTransaction(string $transactionReferenceCode, string $date)
    {
        $payload = [
            'header' => $this->headers(),
            'request' => [
                'TransactionReferenceCode' => $transactionReferenceCode,
                'TransactionDate' => $date,
                'InstitutionCode' => config('coop.institution_code'),
            ]
        ];

        return Http::post(config('coop.validation_url'), $payload)->json();
    }

    public function sendPaymentAdvice(array $data)
    {
        $payload = [
            'header' => $this->headers(),
            'request' => array_merge([
                'BankCode' => '00011',
                'BranchCode' => '00011001',
                'Currency' => '',
                'PaymentReferenceCode' => '',
                'PaymentCode' => '',
                'PaymentMode' => '1',
                'InstitutionCode' => config('coop.institution_code'),
                'InstitutionName' => 'Eldoret University'
            ], $data)
        ];

        return Http::post(config('coop.advice_url'), $payload)->json();
    }
}
