<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CoopSimulatorService
{
    public function simulateValidation(string $transactionRef)
    {
        $now = Carbon::now()->toIso8601String();

        $payload = [
            'header' => [
                'serviceName' => 'DemoSchoolA',
                'messageID' => Str::uuid()->toString(),
                'connectionID' => 'UOE',
                'connectionPassword' => '8786%$',
            ],
            'request' => [
                'TransactionReferenceCode' => $transactionRef,
                'TransactionDate' => $now,
                'InstitutionCode' => '2100082'
            ]
        ];

        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(env('VALIDATION_TARGET_URL'), $payload);
    }

    public function simulateIpn(array $validatedData)
    {
        $payload = [
            'header' => [
                'serviceName' => 'DemoSchoolA',
                'messageID' => Str::uuid()->toString(),
                'connectionID' => 'UOE',
                'connectionPassword' => '8786%$',
            ],
            'request' => array_merge([
                'TransactionDate' => now()->toIso8601String(),
                'BankCode' => '00011',
                'BranchCode' => '00011001',
                'Currency' => '',
                'PaymentReferenceCode' => '',
                'PaymentCode' => '',
                'PaymentMode' => '1',
                'InstitutionCode' => '2100082',
                'InstitutionName' => 'Demo School A'
            ], $validatedData)
        ];

        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(env('IPN_TARGET_URL'), $payload);
    }
}
