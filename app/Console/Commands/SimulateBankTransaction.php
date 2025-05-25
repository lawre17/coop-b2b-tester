<?php

namespace App\Console\Commands;

use App\Services\CoopSimulatorService;
use Illuminate\Console\Command;

class SimulateBankTransaction extends Command
{
    protected $signature = 'simulate:bank-b2b';
    protected $description = 'Simulates Co-op Bank validation and IPN';

    public function handle(CoopSimulatorService $simulator)
    {
        $reference = 'EDA/1140/13';
        $this->info("Simulating validation for: $reference");

        $validationResponse = $simulator->simulateValidation($reference);

        if ($validationResponse['header']['statusCode'] === '200') {
            $this->info("Validation passed. Sending IPN...");

            $amount = rand(500, 5000);

            $simulator->simulateIpn([
                'TransactionReferenceCode' => 'TXN' . rand(10000, 99999),
                'TotalAmount' => $amount,
                'DocumentReferenceNumber' => $reference,
                'PaymentDate' => now()->toIso8601String(),
                'PaymentAmount' => $amount,
                'AdditionalInfo' => $reference,
                'AccountNumber' => $reference,
                'AccountName' => $validationResponse['response']['AccountName'] ?? '',
            ]);
        } else {
            $this->error("Validation failed: " . $validationResponse['header']['statusDescription']);
        }
    }
}
