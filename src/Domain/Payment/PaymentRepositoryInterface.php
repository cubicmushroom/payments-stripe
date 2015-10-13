<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\SavePaymentFailedException;

/**
 * Class PaymentRepositoryInterface
 *
 * @package CubicMushroom\Payments\Stripe
 *
 * @see     \spec\CubicMushroom\Payments\Stripe\Domain\Payment\PaymentRepositoryInterfaceSpec
 */
interface PaymentRepositoryInterface
{
    /**
     * Should save a new payment record, and mark it as unpaid
     *
     * @param Payment $payment
     *
     * @return void
     *
     * @throws SavePaymentFailedException
     */
    public function savePaymentBeforeProcessing(Payment $payment);


    /**
     * @param Payment $payment
     *
     * @return void
     *
     * @throws SavePaymentFailedException
     */
    public function markAsPaid(Payment $payment);
}
