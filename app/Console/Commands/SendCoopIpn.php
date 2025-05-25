<?php

namespace App\Console\Commands;

use App\Services\CoopB2BService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendCoopIpn extends Command
{
    protected $signature = 'coop:send-ipn';
    protected $description = 'Send dummy IPN advice to main backend app';

    public function handle(CoopB2BService $coop)
    {
        $reference = 'EDA/1140/13';
        $now = Carbon::now()->toIso8601String();

        $validation = $coop->validateTransaction($reference, $now);
        $this->info('Validation Response: ' . json_encode($validation));

        if ($validation['header']['statusCode'] === '200') {
            $response = $coop->sendPaymentAdvice([
                'TransactionReferenceCode' => 'TXN' . rand(100000, 999999),
                'TransactionDate' => $now,
                'TotalAmount' => 100.00,
                'DocumentReferenceNumber' => $reference,
                'PaymentDate' => $now,
                'PaymentAmount' => 100.00,
                'AdditionalInfo' => $reference,
                'AccountNumber' => $reference,
                'AccountName' => $validation['response']['AccountName'] ?? '',
            ]);

            $this->info('Payment Advice Response: ' . json_encode($response));
        } else {
            $this->error('Validation failed');
        }
    }
}
