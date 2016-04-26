<?php

namespace CubicMushroom\Payments\Stripe\Domain\Payment;

use CubicMushroom\Payments\Stripe\Exception\Domain\Payment\CreatePaymentFailedException;
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
     * It should return the payment, with the $id property updated with the new ID
     *
     * @param Payment $payment
     *
     * @return Payment
     *
     * @throws CreatePaymentFailedException
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
