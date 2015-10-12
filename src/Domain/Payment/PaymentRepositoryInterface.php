<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

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
     * @return
     * @internal param $argument1
     */
    public function saveSuccessfulPayment(Payment $payment);
}
