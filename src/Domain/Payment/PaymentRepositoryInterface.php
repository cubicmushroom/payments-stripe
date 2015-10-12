<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;

/**
 * Class PaymentRepositoryInterface
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see \spec\CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterfaceSpec
 */
interface PaymentRepositoryInterface
{
    /**
     * @param Payment $payment
     *
     * @return void
     *
     * @throws SavePaymentFailedException
     */
    public function saveSuccessfulPayment(Payment $payment);
}
