<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use Money\Money;


/**
 * Class Payment
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Domain\Payment\PaymentSpec
 */
class Payment
{
    /**
     * @param Money  $cost Amount & Currency of payment
     * @param string $token  Token passed from Stripe.js
     */
    public function __construct(Money $cost, $token)
    {
        // TODO: write logic here
    }
}
