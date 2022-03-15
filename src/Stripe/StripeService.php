<?php

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService{

    public function getPaymentIntent(Purchase $purchase){

        // This is your test secret API key.
        \Stripe\Stripe::setApiKey('sk_test_51KbNpjHf4LwNIS0YHX9Ma81Qcma8m6gXEARsUQVzf6hY7a3N4xDjcln9C6FAqBEBydMPjWO7rZEX6Iy3RPXRxtET005UJ5ZEOP');


        header('Content-Type: application/json');

        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $purchase->getTotal(),
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


}
