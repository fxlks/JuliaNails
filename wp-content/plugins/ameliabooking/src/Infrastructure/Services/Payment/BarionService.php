<?php

namespace AmeliaBooking\Infrastructure\Services\Payment;

use AmeliaBooking\Domain\Services\Payment\AbstractPaymentService;
use AmeliaBooking\Domain\Services\Payment\PaymentServiceInterface;

class BarionService extends AbstractPaymentService implements PaymentServiceInterface
{
    private function getPosKeyFromSettings()
    {
        return $this->settingsService->getCategorySettings('payments')['barion']['sandboxMode'] ?
            $this->settingsService->getCategorySettings('payments')['barion']['sandboxPOSKey'] :
            $this->settingsService->getCategorySettings('payments')['barion']['livePOSKey'];
    }

    private function getUrl()
    {
        return $this->settingsService->getCategorySettings('payments')['barion']['sandboxMode'] ?
            'https://api.test.barion.com' :
            'https://api.barion.com';
    }

    private function getPayeeFromSettings()
    {
        return $this->settingsService->getCategorySettings('payments')['barion']['payeeEmail'];
    }

    private function preparePayment($data)
    {
        $reservationData = $data['reservation'];
        $paymentId = $data['paymentId'];
        $serviceName = $reservationData->getBookable()->getName()->getValue();
        $serviceDescription = $reservationData->getBookable()->getDescription()->getValue() ?
            $reservationData->getBookable()->getDescription()->getValue() : $serviceName;
        $metaDescription = $data['info']['description'] ?: '';
        $payee = $this->getPayeeFromSettings();
        $currency = $this->settingsService->getCategorySettings('payments')['currency'];
        $posKey = $this->getPosKeyFromSettings();
        $response = wp_remote_post($this->getUrl() . '/v2/payment/start', [
            'headers' => [
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode([
                "POSKey" => $posKey,
                "PaymentType" => "Immediate",
                "PaymentRequestId" => "Amelia - Transaction " . $paymentId,
                "FundingSources" => ["All"],
                "RedirectUrl" => $data['redirectUrl'],
                "CallbackUrl" => $data['callbackUrl'],
                "Currency" => $currency,
                "Locale" => "en-US",
                "GuestCheckout" => true,
                "PaymentWindow" => "00:30:00",
                "Transactions" => [
                    [
                        "POSTransactionId" => "Amelia - Transaction " . $paymentId,
                        "Payee" => $payee,
                        "Total" => $data['amount'],
                        "Comment" => $metaDescription,
                        "Items" => [
                            [
                                "Name" => $serviceName,
                                "Description" => $serviceDescription,
                                "Quantity" => 1,
                                "Unit" => "db",
                                "UnitPrice" => 1,
                                "ItemTotal" => 1,
                                "SKU" => "SM-01"
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    public function execute($data, &$transfers)
    {
        return $this->preparePayment($data);
    }

    public function getPaymentLink($data)
    {
        $reservationData = $data['reservation'];

        $paymentId = $data['paymentId'];
        $name = $data['name'];
        $serviceDescription = !empty($reservationData['service']['description']) ? $reservationData['service']['description'] : '';
        $metaDescription = $data['info']['description'] ?: '';
        $payee = $this->getPayeeFromSettings();
        $currency = $this->settingsService->getCategorySettings('payments')['currency'];
        $posKey = $this->getPosKeyFromSettings();
        $response = wp_remote_post($this->getUrl() . '/v2/payment/start', [
            'headers' => [
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode([
                "POSKey" => $posKey,
                "PaymentType" => "Immediate",
                "PaymentRequestId" => "Amelia - Transaction " . $paymentId,
                "FundingSources" => ["All"],
                "RedirectUrl" => $data['returnUrl'],
                "Currency" => $currency,
                "Locale" => "en-US",
                "GuestCheckout" => true,
                "PaymentWindow" => "00:30:00",
                "Transactions" => [
                    [
                        "POSTransactionId" => "Amelia - Transaction " . $paymentId,
                        "Payee" => $payee,
                        "Total" => $data['amount'],
                        "Comment" => $metaDescription,
                        "Items" => [
                            [
                                "Name" => $name,
                                "Description" => $serviceDescription ?: $name,
                                "Quantity" => 1,
                                "Unit" => "db",
                                "UnitPrice" => 1,
                                "ItemTotal" => 1,
                                "SKU" => "SM-01"
                            ]
                        ]
                    ]
                ]
            ])
        ]);

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }

    public function refund($data)
    {
        $payment = $this->getPaymentState($data['id']);
        $posKey = $this->getPosKeyFromSettings();

        $transactions = [];

        foreach ($payment['Transactions'] as $transaction) {
            if ($transaction['TransactionType'] === 'CardPayment') { // refund only customer payment
                $transactions[] = [
                    "TransactionId" => $transaction['TransactionId'],
                    "AmountToRefund" => $transaction['Total'], // or a partial amount
                ];
            }
        }

        $response = wp_remote_post($this->getUrl() . '/v2/Payment/Refund', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'x-pos-key' => $posKey
            ],
            'body' => json_encode([
                "PaymentId" => $payment['PaymentId'],
                "TransactionsToRefund" => $transactions,
                ])
        ]);

        return json_decode(wp_remote_retrieve_body($response), true);
    }


    public function getTransactionAmount($id, $transfers)
    {
        $payment = $this->getPaymentState($id);

        return $payment ? $payment['Total'] : null;
    }

    public function getErrorMessage($errors)
    {
        $errors =  array_map(
            function ($error) {
                return $error['Description'];
            },
            $errors
        );
        return implode('; ', $errors);
    }

    public function getPaymentState($paymentId)
    {
        $posKey = $this->getPosKeyFromSettings();
        $response = wp_remote_get($this->getUrl() . "/v4/Payment/{$paymentId}/PaymentState", [
            'headers' => [
                'Accept' => 'application/json',
                'x-pos-key' => $posKey
            ]
        ]);

        return json_decode(wp_remote_retrieve_body($response), true);
    }
}
